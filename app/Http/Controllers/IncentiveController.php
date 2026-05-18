<?php

namespace App\Http\Controllers;

use App\Models\IncentiveRule;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;

class IncentiveController extends Controller
{
    public function index()
    {
        $active   = IncentiveRule::active()->orderBy('starts_at', 'desc')->get();
        $inactive = IncentiveRule::where(fn ($q) =>
                        $q->where('active', false)
                          ->orWhere('ends_at', '<', now())
                          ->orWhereColumn('uses_count', '>=', 'max_uses')
                    )
                    ->whereNotNull('id')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->filter(fn ($r) => !$r->isCurrentlyActive());

        return view('admin.incentives.index', compact('active', 'inactive'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:150',
            'description'          => 'nullable|string|max:500',
            'type'                 => 'required|in:extra_receptor,extra_comision,descuento_tasa',
            'target_type'          => 'required|in:todos_clientes,cliente_nuevo,cliente_especifico,todos_vendedores,vendedor_especifico,clientes_de_vendedor',
            'target_id'            => 'nullable|integer',
            'value_type'           => 'required|in:percentage,fixed',
            'value'                => 'required|numeric|min:0',
            'min_amount'           => 'nullable|numeric|min:0',
            'min_transactions'     => 'nullable|integer|min:1',
            'max_uses'             => 'nullable|integer|min:1',
            'condition_new_client' => 'boolean',
            'starts_at'            => 'required|date',
            'ends_at'              => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['created_by']          = auth()->id();
        $validated['active']              = true;
        $validated['uses_count']          = 0;
        $validated['condition_new_client'] = $request->boolean('condition_new_client');

        IncentiveRule::create($validated);

        return redirect()->route('admin.incentives.index')
            ->with('success', 'Incentivo creado correctamente.');
    }

    public function toggleActive(IncentiveRule $incentive)
    {
        $incentive->update(['active' => !$incentive->active]);
        return redirect()->back()
            ->with('success', 'Estado del incentivo actualizado.');
    }

    public function destroy(IncentiveRule $incentive)
    {
        $incentive->delete();
        return redirect()->route('admin.incentives.index')
            ->with('success', 'Incentivo eliminado.');
    }
}
