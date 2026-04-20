<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Seller;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('seller')->latest('sale_date')->paginate(20);
        return view('sales.index', compact('sales'));
    }

    public function pendingSeller()
    {
        $sales = Sale::with('seller')
            ->where('approval_status', 'pending_seller')
            ->latest('sale_date')
            ->paginate(20);
        return view('sales.pending-seller', compact('sales'));
    }

    public function pendingAdmin()
    {
        $sales = Sale::with('seller')
            ->where('approval_status', 'pending_admin')
            ->oldest('sale_date')
            ->paginate(20);
        return view('sales.pending-admin', compact('sales'));
    }

    public function observed()
    {
        $sales = Sale::with('seller')
            ->where('approval_status', 'observed')
            ->latest('updated_at')
            ->paginate(20);
        return view('sales.observed', compact('sales'));
    }

    public function approved()
    {
        $sales = Sale::with('seller')
            ->where('approval_status', 'approved')
            ->latest('updated_at')
            ->paginate(20);
        return view('sales.approved', compact('sales'));
    }

    public function uploadVoucher(Request $request, Sale $sale)
    {
        $request->validate([
            'voucher' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB
        ]);

        try {
            if (!$sale->canBeCompleted()) {
                throw new \Exception('Solo se pueden cargar comprobantes en ventas aprobadas.');
            }

            // Almacenar el archivo
            $file = $request->file('voucher');
            $filename = 'voucher_' . $sale->id . '_' . time() . '.' . $file->extension();
            $path = $file->storeAs('vouchers', $filename, 'local');

            // Marcar venta como completada
            $sale->complete($path);

            return redirect()->back()->with('success', 'Comprobante cargado exitosamente. Venta marcada como completada.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function showVoucher(Sale $sale)
    {
        if (!$sale->voucher_path) {
            abort(404, 'No hay comprobante disponible para esta venta.');
        }

        $path = storage_path('app/' . $sale->voucher_path);

        if (!file_exists($path)) {
            abort(404, 'El archivo del comprobante no existe.');
        }

        return response()->file($path);
    }

    public function downloadVoucher(Sale $sale)
    {
        if (!$sale->voucher_path) {
            abort(404, 'No hay comprobante disponible para esta venta.');
        }

        $path = storage_path('app/' . $sale->voucher_path);

        if (!file_exists($path)) {
            abort(404, 'El archivo del comprobante no existe.');
        }

        return response()->download($path, 'comprobante_venta_' . $sale->id . '.' . pathinfo($path, PATHINFO_EXTENSION));
    }

    public function create()
    {
        $sellers = Seller::all();
        return view('sales.create', compact('sellers'));
    }

    public function edit(Sale $sale)
    {
        $sellers = Seller::all();
        return view('sales.edit', compact('sale', 'sellers'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'amount' => 'required|numeric|min:0.01',
            'sale_date' => 'required|date',
        ]);

        $wasObserved = $sale->isObserved();

        $sale->update($validated);

        // Si la venta estaba observada, volver a pending_admin después de corregir
        if ($wasObserved) {
            $sale->approval_status = 'pending_admin';
            $sale->admin_observation = null; // Limpiar observación
            $sale->save();
            return redirect()->route('sales.observed')->with('success', 'Venta corregida y re-enviada al administrador.');
        }

        return redirect()->route('sales.index')->with('success', 'Venta actualizada exitosamente.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'amount' => 'required|numeric|min:0.01',
            'sale_date' => 'required|date',
        ]);

        // Obtener seller para calcular comisiones
        $seller = Seller::findOrFail($validated['seller_id']);

        // Obtener tasa activa para snapshots
        $activeRate = ExchangeRate::getActive();

        // Crear venta con snapshots de comisiones y tasas
        $sale = Sale::create([
            'seller_id' => $validated['seller_id'],
            'amount' => $validated['amount'],
            'sale_date' => $validated['sale_date'],
            'approval_status' => 'pending_seller',

            // Snapshots de comisiones (valores en el momento de la venta)
            'seller_commission_percent' => $seller->seller_commission,
            'admin_commission_percent' => $seller->boss_commission,
            'seller_commission_amount' => $validated['amount'] * ($seller->seller_commission / 100),
            'admin_commission_amount' => $validated['amount'] * ($seller->boss_commission / 100),

            // Snapshots de tasas (para trazabilidad)
            'usd_rate_snapshot' => $activeRate->usd_rate ?? null,
            'eur_rate_snapshot' => $activeRate->eur_rate ?? null,
            'ves_rate_snapshot' => $activeRate->ves_rate ?? $activeRate->base_rate ?? null,
        ]);

        // Si es una petición AJAX, respondemos con JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Venta registrada exitosamente.',
                'sale' => $sale,
            ]);
        }

        // Si por alguna razón no es AJAX, redirige (fallback)
        return redirect()->route('sales.index');
    }


    
    public function bulkCreate()
    {
        $sellers = Seller::all();
        return view('sales.bulk-create', compact('sellers'));
    }
    public function bulkStore(Request $request)
{
    $sales = collect($request->input('sales', []));

    $validSales = $sales->filter(function ($sale) {
        return isset($sale['amount']) && $sale['amount'] > 0 && !empty($sale['sale_date']);
    });

    $validated = $validSales->map(function ($sale) {
        return validator($sale, [
            'seller_id' => 'required|exists:sellers,id',
            'amount' => 'required|numeric|min:0.01',
            'sale_date' => 'required|date',
        ])->validate();
    });

    // Obtener tasa activa una sola vez para todas las ventas
    $activeRate = ExchangeRate::getActive();

    foreach ($validated as $saleData) {
        // Obtener seller para calcular comisiones
        $seller = Seller::findOrFail($saleData['seller_id']);

        // Crear venta con snapshots de comisiones y tasas
        Sale::create([
            'seller_id' => $saleData['seller_id'],
            'amount' => $saleData['amount'],
            'sale_date' => $saleData['sale_date'],
            'approval_status' => 'pending_seller',

            // Snapshots de comisiones (valores en el momento de la venta)
            'seller_commission_percent' => $seller->seller_commission,
            'admin_commission_percent' => $seller->boss_commission,
            'seller_commission_amount' => $saleData['amount'] * ($seller->seller_commission / 100),
            'admin_commission_amount' => $saleData['amount'] * ($seller->boss_commission / 100),

            // Snapshots de tasas (para trazabilidad)
            'usd_rate_snapshot' => $activeRate->usd_rate ?? null,
            'eur_rate_snapshot' => $activeRate->eur_rate ?? null,
            'ves_rate_snapshot' => $activeRate->ves_rate ?? $activeRate->base_rate ?? null,
        ]);
    }

    return redirect()->route('sales.index')->with('success', 'Ventas registradas correctamente.');
}
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index');
    }

    public function approve(Sale $sale)
    {
        try {
            $sale->approve();

            $message = $sale->isApproved()
                ? 'Venta aprobada exitosamente.'
                : 'Venta escalada a administrador para aprobación final.';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function reject(Sale $sale)
    {
        try {
            $sale->reject();
            return redirect()->back()->with('success', 'Venta rechazada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function observe(Request $request, Sale $sale)
    {
        $request->validate([
            'observation' => 'required|string|min:10|max:1000',
        ]);

        try {
            $sale->markAsObserved($request->observation);
            return redirect()->back()->with('success', 'Venta devuelta al vendedor con observación.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}