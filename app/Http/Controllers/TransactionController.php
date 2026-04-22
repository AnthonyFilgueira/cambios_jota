<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['seller', 'exchangeRate'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSpent = $transactions->sum('amount_pen');

        return view('transactions.index', compact('transactions', 'totalSpent'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener tasas activas
        $rates = \App\Models\ExchangeRate::where('is_active', true)->first();

        // Obtener pares de divisas disponibles
        $pairs = \App\Models\ExchangeRate::with(['currencyPair.fromCurrency', 'currencyPair.toCurrency'])
            ->whereNotNull('currency_pair_id')
            ->where('is_active', true)
            ->get()
            ->map(function($rate) {
                return [
                    'id' => $rate->id,
                    'from_code' => $rate->currencyPair->fromCurrency->code,
                    'from_name' => $rate->currencyPair->fromCurrency->name,
                    'from_symbol' => $rate->currencyPair->fromCurrency->symbol,
                    'ves_rate' => $rate->ves_rate,
                    'usd_rate' => $rate->usd_rate,
                ];
            });

        return view('transactions.create', compact('rates', 'pairs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount_pen' => 'required|numeric|min:1',
            'exchange_rate_id' => 'required|exists:exchange_rates,id',
            'notes' => 'nullable|string|max:500',

            // Datos bancarios del receptor (Venezuela)
            'recipient_bank' => 'required|string|max:255',
            'recipient_account_number' => 'required|string|max:255',
            'recipient_dni' => 'required|string|max:255',
            'recipient_account_type' => 'required|in:ahorro,corriente',

            // Datos de transferencia desde Perú
            'sender_bank' => 'required|string|max:255',
            'sender_account_number' => 'required|string|max:255',

            // Comprobante
            'voucher' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Obtener la tasa de cambio para cálculos
        $rate = \App\Models\ExchangeRate::findOrFail($validated['exchange_rate_id']);

        // Calcular amount_ves basado en la tasa
        $validated['amount_ves'] = $validated['amount_pen'] * $rate->ves_rate;

        // Guardar snapshot de tasas BCV (historicidad)
        $validated['usd_bcv_rate'] = $rate->usd_rate;
        $validated['eur_bcv_rate'] = $rate->eur_rate;

        // Manejar subida del comprobante
        if ($request->hasFile('voucher')) {
            $path = $request->file('voucher')->store('vouchers', 'public');
            $validated['voucher'] = $path;
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        Transaction::create($validated);

        return redirect()->route('transactions.index')
            ->with('success', '¡Solicitud de envío creada exitosamente! Un vendedor se pondrá en contacto contigo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
