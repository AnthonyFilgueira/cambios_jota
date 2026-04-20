<?php

namespace App\Http\Controllers;

use App\Models\Liquidation;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LiquidationController extends Controller
{
    public function index(Request $request)
    {
        $query = Liquidation::with(['seller', 'creator']);

        // Filtros
        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        $liquidations = $query->orderBy('payment_date', 'desc')->paginate(20);
        $sellers = Seller::orderBy('name')->get();
        $paymentMethods = Liquidation::paymentMethods();

        return view('liquidations.index', compact('liquidations', 'sellers', 'paymentMethods'));
    }

    public function create()
    {
        $sellers = Seller::orderBy('name')->get();
        $paymentMethods = Liquidation::paymentMethods();

        return view('liquidations.create', compact('sellers', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:efectivo,transferencia,yape,plin,otro',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_date' => 'required|date|before_or_equal:today',
        ]);

        $seller = Seller::findOrFail($validated['seller_id']);

        // Validar saldo suficiente
        if (!$seller->hasSufficientBalance($validated['amount'])) {
            return back()->withErrors([
                'amount' => "Saldo insuficiente. Saldo actual: S/. " . number_format($seller->walletBalance(), 2)
            ])->withInput();
        }

        DB::transaction(function () use ($validated, $seller) {
            // Crear liquidación
            $liquidation = Liquidation::create([
                ...$validated,
                'created_by' => Auth::id(),
            ]);

            // Restar del monedero
            $seller->deductFromWallet(
                $validated['amount'],
                'liquidation',
                "Liquidación #{$liquidation->id} - " . Liquidation::paymentMethods()[$validated['payment_method']],
                $liquidation
            );
        });

        return redirect()->route('liquidations.index')
            ->with('success', 'Liquidación registrada exitosamente. Saldo del vendedor actualizado.');
    }

    public function show(Liquidation $liquidation)
    {
        $liquidation->load(['seller', 'creator']);
        $walletTransaction = $liquidation->walletTransaction();

        return view('liquidations.show', compact('liquidation', 'walletTransaction'));
    }

    public function edit(Liquidation $liquidation)
    {
        $sellers = Seller::orderBy('name')->get();
        $paymentMethods = Liquidation::paymentMethods();

        return view('liquidations.edit', compact('liquidation', 'sellers', 'paymentMethods'));
    }

    public function update(Request $request, Liquidation $liquidation)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'payment_date' => 'required|date|before_or_equal:today',
        ]);

        // Solo permitir editar campos no críticos
        $liquidation->update($validated);

        return redirect()->route('liquidations.show', $liquidation)
            ->with('success', 'Liquidación actualizada exitosamente.');
    }

    public function destroy(Liquidation $liquidation)
    {
        // Nota: No se recomienda eliminar liquidaciones por trazabilidad
        // Esta función podría ser restringida solo a administradores

        return back()->withErrors(['delete' => 'No se pueden eliminar liquidaciones por razones de auditoría. Contacte al administrador del sistema.']);
    }
}
