<?php

namespace App\Http\Controllers;

use App\Models\IncentiveRule;
use Illuminate\Http\Request;

class IncentiveController extends Controller
{
    public function index()
    {
        $active   = IncentiveRule::active()->orderBy('starts_at', 'desc')->get();
        $inactive = IncentiveRule::where('active', false)
                        ->orWhere('ends_at', '<', now())
                        ->orderBy('created_at', 'desc')
                        ->get()
                        ->filter(fn ($r) => !$r->isCurrentlyActive());

        return view('admin.incentives.index', compact('active', 'inactive'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'description'      => 'nullable|string|max:500',
            'type'             => 'required|in:bono_volumen,bono_estrella,descuento_cliente,bono_nuevo_cliente',
            'target_type'      => 'required|in:seller,client,all',
            'value_type'       => 'required|in:percentage,fixed',
            'value'            => 'required|numeric|min:0',
            'min_amount'       => 'nullable|numeric|min:0',
            'min_transactions' => 'nullable|integer|min:1',
            'starts_at'        => 'required|date',
            'ends_at'          => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['active']     = true;

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
