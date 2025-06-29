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

        Sale::create($request->all());
        return redirect()->route('sales.index');
    }
    public function bulkCreate()
    {
        $sellers = Seller::all();
        return view('sales.bulk-create', compact('sellers'));
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'sales.*.seller_id' => 'required|exists:sellers,id',
            'sales.*.amount' => 'required|numeric|min:0.01',
            'sales.*.sale_date' => 'required|date',
        ]);

        foreach ($data['sales'] as $saleData) {
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