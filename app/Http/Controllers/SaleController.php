<?php
namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Seller;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with('seller')->latest('sale_date')->paginate(20);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $sellers = Seller::all();
        return view('sales.create', compact('sellers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'amount' => 'required|numeric',
            'sale_date' => 'required|date',
        ]);

        $sale = Sale::create($validated);

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

    foreach ($validated as $saleData) {
        Sale::create($saleData);
    }

    return redirect()->route('sales.index')->with('success', 'Ventas registradas correctamente.');
}
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index');
    }
}