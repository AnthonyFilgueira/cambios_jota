<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\CurrencyPair;
use App\Models\Corridor;
use Illuminate\Http\Request;

class CurrencyPairController extends Controller
{
    public function index()
    {
        $pairs = CurrencyPair::with(['fromCurrency', 'toCurrency', 'corridors'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('currency-pairs.index', compact('pairs'));
    }

    public function create()
    {
        $currencies = Currency::active()->orderBy('code')->get();

        return view('currency-pairs.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
        ], [
            'to_currency_id.different' => 'La divisa de destino debe ser diferente a la de origen.',
        ]);

        // Verificar que el par no exista ya
        $exists = CurrencyPair::where('from_currency_id', $validated['from_currency_id'])
            ->where('to_currency_id', $validated['to_currency_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['to_currency_id' => 'Este par de divisas ya existe.'])
                ->withInput();
        }

        $validated['is_active'] = $request->has('is_active');

        CurrencyPair::create($validated);

        return redirect()->route('currency-pairs.index')
            ->with('success', 'Par de divisas creado exitosamente.');
    }

    public function edit(CurrencyPair $currencyPair)
    {
        $currencies = Currency::active()->orderBy('code')->get();
        $corridors = Corridor::active()->orderBy('name')->get();

        // Obtener IDs de corredores asignados y habilitados
        $assignedCorridors = $currencyPair->corridors()
            ->wherePivot('is_enabled', true)
            ->pluck('corridors.id')
            ->toArray();

        return view('currency-pairs.edit', compact('currencyPair', 'currencies', 'corridors', 'assignedCorridors'));
    }

    public function update(Request $request, CurrencyPair $currencyPair)
    {
        $validated = $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
        ], [
            'to_currency_id.different' => 'La divisa de destino debe ser diferente a la de origen.',
        ]);

        // Verificar que el par no exista ya (excepto el actual)
        $exists = CurrencyPair::where('from_currency_id', $validated['from_currency_id'])
            ->where('to_currency_id', $validated['to_currency_id'])
            ->where('id', '!=', $currencyPair->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['to_currency_id' => 'Este par de divisas ya existe.'])
                ->withInput();
        }

        $validated['is_active'] = $request->has('is_active');

        $currencyPair->update($validated);

        // Actualizar corredores asignados
        $corridorIds = $request->input('corridors', []);

        // Sincronizar: eliminar no seleccionados, agregar nuevos con is_enabled=true
        $syncData = [];
        foreach ($corridorIds as $corridorId) {
            $syncData[$corridorId] = ['is_enabled' => true];
        }

        $currencyPair->corridors()->sync($syncData);

        return redirect()->route('currency-pairs.index')
            ->with('success', 'Par de divisas actualizado exitosamente.');
    }

    public function destroy(CurrencyPair $currencyPair)
    {
        // Desactivar en lugar de eliminar
        $currencyPair->update(['is_active' => false]);

        return redirect()->route('currency-pairs.index')
            ->with('success', 'Par de divisas desactivado exitosamente.');
    }

    public function toggleStatus(CurrencyPair $currencyPair)
    {
        $currencyPair->update(['is_active' => !$currencyPair->is_active]);

        $status = $currencyPair->is_active ? 'activado' : 'desactivado';

        return back()->with('success', "Par {$status} exitosamente.");
    }
}
