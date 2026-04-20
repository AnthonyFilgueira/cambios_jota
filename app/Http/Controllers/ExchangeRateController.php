<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Seller;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function index(Request $request)
    {
        // Query base: tasas con sus pares
        $query = ExchangeRate::with(['currencyPair.fromCurrency', 'currencyPair.toCurrency'])
            ->orderBy('is_active', 'desc')
            ->orderBy('updated_at', 'desc');

        // Filtro por divisa origen
        if ($request->filled('from_currency')) {
            $query->whereHas('currencyPair', function ($q) use ($request) {
                $q->where('from_currency_id', $request->from_currency);
            });
        }

        // Filtro por divisa destino
        if ($request->filled('to_currency')) {
            $query->whereHas('currencyPair', function ($q) use ($request) {
                $q->where('to_currency_id', $request->to_currency);
            });
        }

        // Filtro por estado: SOLO ACTIVAS POR DEFECTO
        // (las inactivas se mantienen en BD por snapshots de ventas,
        // pero no se muestran para no confundir al usuario)
        if ($request->filled('status')) {
            if ($request->status === 'all') {
                // Mostrar todas (activas + inactivas)
            } else {
                $query->where('is_active', $request->status === 'active');
            }
        } else {
            // Por defecto: SOLO ACTIVAS
            $query->where('is_active', true);
        }

        $rates = $query->get();
        $activeRate = ExchangeRate::getActive();

        // Divisas para filtros
        $currencies = \App\Models\Currency::where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('exchange_rates.index', compact('rates', 'activeRate', 'currencies'));
    }

    public function create()
    {
        return view('exchange_rates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_pair_id' => 'required|exists:currency_pairs,id',
            'usd_rate' => 'required|numeric|min:0.01',
            'eur_rate' => 'required|numeric|min:0.01',
            'ves_rate' => 'required|numeric|min:0.00001',
            'boss_commission_default' => 'nullable|numeric|min:0|max:100',
        ], [
            'currency_pair_id.required' => 'Debes seleccionar un par de divisas',
            'currency_pair_id.exists' => 'El par de divisas seleccionado no existe',
            'usd_rate.min' => 'La tasa USD debe ser mayor a 0',
            'eur_rate.min' => 'La tasa EUR debe ser mayor a 0',
            'ves_rate.min' => 'La tasa VES debe ser mayor a 0',
        ]);

        // 1. Crear tasa (sin guardar comisión en exchange_rates)
        $rate = ExchangeRate::create($request->only(['currency_pair_id', 'usd_rate', 'eur_rate', 'ves_rate']));

        // 2. Activar automáticamente la nueva tasa (desactiva las demás del mismo par)
        $rate->activate();

        // 3. Solo actualizar comisiones si el campo fue enviado
        if ($request->filled('boss_commission_default')) {
            $updated = Seller::query()->update([
                'boss_commission' => $request->boss_commission_default
            ]);

            return redirect()->route('exchange_rates.index')->with('success',
                "Tasa creada y activada correctamente. Comisión del dueño ({$request->boss_commission_default}%) actualizada en {$updated} vendedor(es).");
        }

        return redirect()->route('exchange_rates.index')->with('success',
            'Tasa creada y activada correctamente. Las tasas anteriores del mismo par se desactivaron automáticamente.');
    }

    public function edit(ExchangeRate $exchangeRate)
    {
        // Cargar historial de cambios
        $history = $exchangeRate->history()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('exchange_rates.edit', compact('exchangeRate', 'history'));
    }

    public function update(Request $request, ExchangeRate $exchangeRate)
    {
        // Proteger historicidad: no permitir editar si tiene transacciones
        if (!$exchangeRate->canBeModified()) {
            return redirect()->route('exchange_rates.index')->with('error',
                'No se puede modificar esta tasa. Ya tiene transacciones asociadas. Crea una nueva tasa en su lugar.');
        }

        $request->validate([
            'usd_rate' => 'required|numeric|min:0.01',
            'eur_rate' => 'required|numeric|min:0.01',
            'ves_rate' => 'required|numeric|min:0.00001',
            'boss_commission_default' => 'nullable|numeric|min:0|max:100',
        ], [
            'usd_rate.min' => 'La tasa USD debe ser mayor a 0',
            'eur_rate.min' => 'La tasa EUR debe ser mayor a 0',
            'ves_rate.min' => 'La tasa VES debe ser mayor a 0',
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
