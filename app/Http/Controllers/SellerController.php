<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\CommissionRule;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::all();
        return view('sellers.index', compact('sellers'));
    }

    public function create()
    {
        return view('sellers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'seller_commission' => 'required|numeric',
            'boss_commission' => 'required|numeric',
        ]);

        Seller::create($request->except('_token'));
        return redirect()->route('sellers.index');
    }

    public function edit(Seller $seller)
    {
        return view('sellers.edit', compact('seller'));
    }

    public function update(Request $request, Seller $seller)
    {
        $request->validate([
            'name' => 'required',
            'seller_commission' => 'required|numeric|min:0|max:100',
            'boss_commission' => 'required|numeric|min:0|max:100',
        ]);

        // Proteger historicidad: verificar si intenta cambiar comisiones
        $changingCommissions = (
            $request->seller_commission != $seller->seller_commission ||
            $request->boss_commission != $seller->boss_commission
        );

        if ($changingCommissions && !$seller->commissionsCanBeModified()) {
            return redirect()->route('sellers.index')->with('error',
                'No se pueden modificar las comisiones de este vendedor. Ya tiene ventas registradas. Crea un nuevo vendedor con las nuevas comisiones.');
        }

        $seller->update($request->except('_token'));
        return redirect()->route('sellers.index')->with('success', 'Vendedor actualizado correctamente.');
    }

    public function destroy(Seller $seller)
    {
        $seller->delete();
        return redirect()->route('sellers.index');
    }

    public function commissions(Seller $seller)
    {
        $rules = $seller->commissionRules()->with('appliedBy')->get();
        $latest = $rules->first();
        return view('sellers.commissions', compact('seller', 'rules', 'latest'));
    }

    public function storeCommission(Request $request, Seller $seller)
    {
        $validated = $request->validate([
            'commission_type' => 'required|in:percentage,fixed',
            'seller_value'    => 'required|numeric|min:0|max:100',
            'boss_value'      => 'required|numeric|min:0|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        $validated['seller_id']  = $seller->id;
        $validated['applied_by'] = auth()->id();

        CommissionRule::create($validated);

        // Sincronizar con los campos legacy del seller
        $seller->update([
            'seller_commission' => $validated['seller_value'],
            'boss_commission'   => $validated['boss_value'],
        ]);

        return redirect()->route('sellers.commissions', $seller)
            ->with('success', 'Regla de comisión guardada y aplicada correctamente.');
    }
}