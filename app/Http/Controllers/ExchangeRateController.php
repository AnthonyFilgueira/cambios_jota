<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Seller;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::orderBy('created_at', 'desc')->get();
        $activeRate = ExchangeRate::getActive();
        return view('exchange_rates.index', compact('rates', 'activeRate'));
    }

    public function create()
    {
        return view('exchange_rates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'usd_rate' => 'required|numeric|min:0',
            'eur_rate' => 'required|numeric|min:0',
            'ves_rate' => 'required|numeric|min:0',
            'boss_commission_default' => 'required|numeric|min:0|max:100',
        ]);

        // 1. Crear tasa (sin guardar comisión en exchange_rates)
        $rate = ExchangeRate::create($request->only(['usd_rate', 'eur_rate', 'ves_rate']));

        // 2. Actualizar comisión del dueño en TODOS los vendedores
        $updated = Seller::query()->update([
            'boss_commission' => $request->boss_commission_default
        ]);

        // 3. Activar si es la primera
        if (ExchangeRate::count() === 1) {
            $rate->activate();
        }

        return redirect()->route('exchange_rates.index')->with('success',
            "Tasa creada correctamente. Comisión del dueño ({$request->boss_commission_default}%) actualizada en {$updated} vendedor(es).");
    }

    public function edit(ExchangeRate $exchangeRate)
    {
        return view('exchange_rates.edit', compact('exchangeRate'));
    }

    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        // Proteger historicidad: no permitir editar si tiene transacciones
        if (!$exchangeRate->canBeModified()) {
            return redirect()->route('exchange_rates.index')->with('error',
                'No se puede modificar esta tasa. Ya tiene transacciones asociadas. Crea una nueva tasa en su lugar.');
        }

        $request->validate([
            'usd_rate' => 'required|numeric|min:0',
            'eur_rate' => 'required|numeric|min:0',
            'ves_rate' => 'required|numeric|min:0',
            'boss_commission_default' => 'nullable|numeric|min:0|max:100',
        ]);

        // 1. Actualizar tasa
        $exchangeRate->update($request->only(['usd_rate', 'eur_rate', 'ves_rate']));

        // 2. Solo actualizar comisiones si el campo fue enviado
        if ($request->filled('boss_commission_default')) {
            $updated = Seller::query()->update([
                'boss_commission' => $request->boss_commission_default
            ]);

            return redirect()->route('exchange_rates.index')->with('success',
                "Tasa actualizada. Comisión del dueño ({$request->boss_commission_default}%) actualizada en {$updated} vendedor(es).");
        }

        return redirect()->route('exchange_rates.index')->with('success', 'Tasa actualizada correctamente');
    }

    public function destroy(ExchangeRate $exchangeRate)
    {
        // No permitir eliminar la tasa activa
        if ($exchangeRate->is_active) {
            return redirect()->route('exchange_rates.index')->with('error', 'No puedes eliminar la tasa activa');
        }

        // Proteger historicidad: no permitir eliminar si tiene transacciones
        if (!$exchangeRate->canBeModified()) {
            return redirect()->route('exchange_rates.index')->with('error',
                'No se puede eliminar esta tasa. Ya tiene transacciones asociadas.');
        }

        $exchangeRate->delete();
        return redirect()->route('exchange_rates.index')->with('success', 'Tasa eliminada correctamente');
    }

    public function activate(ExchangeRate $exchangeRate)
    {
        $exchangeRate->activate();
        return redirect()->route('exchange_rates.index')->with('success', 'Tasa activada correctamente');
    }
}
