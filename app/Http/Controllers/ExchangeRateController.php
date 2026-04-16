<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
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
        ]);

        $rate = ExchangeRate::create($request->only(['usd_rate', 'eur_rate', 'ves_rate']));

        // Si es la primera, activarla automáticamente
        if (ExchangeRate::count() === 1) {
            $rate->activate();
        }

        return redirect()->route('exchange_rates.index')->with('success', 'Tasa creada correctamente');
    }

    public function edit(ExchangeRate $exchangeRate)
    {
        return view('exchange_rates.edit', compact('exchangeRate'));
    }

    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        $request->validate([
            'usd_rate' => 'required|numeric|min:0',
            'eur_rate' => 'required|numeric|min:0',
            'ves_rate' => 'required|numeric|min:0',
        ]);

        $exchangeRate->update($request->only(['usd_rate', 'eur_rate', 'ves_rate']));

        return redirect()->route('exchange_rates.index')->with('success', 'Tasa actualizada correctamente');
    }

    public function destroy(ExchangeRate $exchangeRate)
    {
        // No permitir eliminar la tasa activa
        if ($exchangeRate->is_active) {
            return redirect()->route('exchange_rates.index')->with('error', 'No puedes eliminar la tasa activa');
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
