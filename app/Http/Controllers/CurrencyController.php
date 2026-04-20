<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $currencies = Currency::orderBy('code')->get();
        return view('currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'flag_emoji' => 'nullable|string|max:10',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Currency::create($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'Divisa creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'country' => 'required|string|max:255',
            'flag_emoji' => 'nullable|string|max:10',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $currency->update($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'Divisa actualizada exitosamente.');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Currency $currency)
    {
        $currency->update(['is_active' => !$currency->is_active]);

        return redirect()->route('currencies.index')
            ->with('success', 'Estado de divisa actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     * (Soft delete - just deactivate)
     */
    public function destroy(Currency $currency)
    {
        // Verificar si está en uso
        if ($currency->pairsAsOrigin()->exists() || $currency->pairsAsDestination()->exists()) {
            return redirect()->route('currencies.index')
                ->with('error', 'No se puede eliminar una divisa que está en uso en pares de conversión.');
        }

        $currency->update(['is_active' => false]);

        return redirect()->route('currencies.index')
            ->with('success', 'Divisa desactivada exitosamente.');
    }
}
