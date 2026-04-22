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
     * Mostrar todas las transacciones para admin/vendedor
     */
    public function manage()
    {
        // Verificar que el usuario sea admin o vendedor
        if (!auth()->user()->hasRole('admin') && !auth()->user()->seller) {
            abort(403, 'No autorizado');
        }

        $query = Transaction::with(['user', 'seller', 'exchangeRate', 'logs.user'])
            ->orderBy('created_at', 'desc');

        // Si es vendedor, solo mostrar sus transacciones
        if (auth()->user()->seller) {
            $query->where('seller_id', auth()->user()->seller->id);
        }

        $transactions = $query->get();

        // Estadísticas
        $stats = [
            'total' => $transactions->count(),
            'pending' => $transactions->where('status', 'pending')->count(),
            'observed' => $transactions->where('status', 'observed')->count(),
            'processing' => $transactions->where('status', 'processing')->count(),
            'completed' => $transactions->where('status', 'completed')->count(),
            'cancelled' => $transactions->where('status', 'cancelled')->count(),
        ];

        return view('transactions.manage', compact('transactions', 'stats'));
    }

    /**
     * Get currency pairs for the form
     */
    private function getCurrencyPairs()
    {
        return \App\Models\ExchangeRate::with(['currencyPair.fromCurrency', 'currencyPair.toCurrency'])
            ->whereNotNull('currency_pair_id')
            ->where('is_active', true)
            ->get()
            ->map(function($rate) {
                return [
                    'id' => $rate->id,
                    'from_code' => $rate->currencyPair->fromCurrency->code ?? 'N/A',
                    'from_name' => $rate->currencyPair->fromCurrency->name ?? 'N/A',
                    'from_symbol' => $rate->currencyPair->fromCurrency->symbol ?? '$',
                    'ves_rate' => $rate->ves_rate ?? 0,
                    'usd_rate' => $rate->usd_rate ?? 0,
                    'eur_rate' => $rate->eur_rate ?? 0,
                ];
            });
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener tasas activas
        $rates = \App\Models\ExchangeRate::where('is_active', true)->first();

        // Obtener pares de divisas disponibles
        $pairs = $this->getCurrencyPairs();

        return view('transactions.create', compact('rates', 'pairs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount_pen' => 'required|numeric|min:1',
            'amount_ves' => 'required|numeric|min:1',
            'exchange_rate_id' => 'required|exists:exchange_rates,id',
            'notes' => 'nullable|string|max:500',

            // Código de vendedor (opcional)
            'seller_code' => 'nullable|string|max:20',

            // Tasas BCV (snapshot)
            'usd_bcv_rate' => 'nullable|numeric',
            'eur_bcv_rate' => 'nullable|numeric',

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

        // Buscar vendedor por código si se proporcionó
        $seller = null;
        if ($request->filled('seller_code')) {
            $seller = \App\Models\Seller::where('code', strtoupper($request->seller_code))->first();

            if (!$seller) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['seller_code' => 'El código de vendedor no existe.']);
            }

            $validated['seller_id'] = $seller->id;
        }

        // Si no vienen las tasas BCV, obtenerlas del exchange rate
        if (!$request->has('usd_bcv_rate') || !$request->has('eur_bcv_rate')) {
            $rate = \App\Models\ExchangeRate::findOrFail($validated['exchange_rate_id']);
            $validated['usd_bcv_rate'] = $rate->usd_rate;
            $validated['eur_bcv_rate'] = $rate->eur_rate;
        }

        // Manejar subida del comprobante
        if ($request->hasFile('voucher')) {
            $path = $request->file('voucher')->store('vouchers', 'public');
            $validated['voucher'] = $path;
        }

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        // Crear transacción
        $transaction = Transaction::create($validated);

        // Enviar notificación al vendedor si fue asignado
        if ($seller) {
            $seller->user->notify(new \App\Notifications\NewTransactionForSeller($transaction));
        }

        return redirect()->route('transactions.index')
            ->with('success', '¡Solicitud de envío creada exitosamente! ' . ($seller ? 'El vendedor ' . $seller->name . ' ha sido notificado.' : 'Un vendedor se pondrá en contacto contigo.'));
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

    /**
     * Marcar transacción como observada
     */
    public function observe(Request $request, Transaction $transaction)
    {
        // Verificar que el usuario sea admin o vendedor
        if (!auth()->user()->hasRole('admin') && !auth()->user()->seller) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'observation' => 'required|string|max:1000',
        ]);

        try {
            $oldStatus = $transaction->status;
            $transaction->markAsObserved($validated['observation']);

            // Crear log
            \App\Models\TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'observed',
                'old_status' => $oldStatus,
                'new_status' => 'observed',
                'comment' => $validated['observation'],
            ]);

            // Notificar al usuario
            $transaction->user->notify(new \App\Notifications\TransactionObserved($transaction));

            return redirect()->back()->with('success', 'Transacción marcada como observada. El cliente ha sido notificado.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Marcar transacción como en proceso
     */
    public function process(Transaction $transaction)
    {
        // Verificar que el usuario sea admin o vendedor
        if (!auth()->user()->hasRole('admin') && !auth()->user()->seller) {
            abort(403, 'No autorizado');
        }

        try {
            $oldStatus = $transaction->status;
            $transaction->process();

            // Crear log
            \App\Models\TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'processed',
                'old_status' => $oldStatus,
                'new_status' => 'processing',
                'comment' => 'Transacción iniciada por ' . auth()->user()->name,
            ]);

            // Notificar al usuario
            $transaction->user->notify(new \App\Notifications\TransactionProcessed($transaction));

            return redirect()->back()->with('success', 'Transacción marcada como en proceso. El cliente ha sido notificado.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Marcar transacción como completada
     */
    public function completeTransaction(Transaction $transaction)
    {
        // Verificar que el usuario sea admin o vendedor
        if (!auth()->user()->hasRole('admin') && !auth()->user()->seller) {
            abort(403, 'No autorizado');
        }

        try {
            $oldStatus = $transaction->status;
            $transaction->complete();

            // Crear log
            \App\Models\TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'completed',
                'old_status' => $oldStatus,
                'new_status' => 'completed',
                'comment' => 'Transacción completada por ' . auth()->user()->name,
            ]);

            // Notificar al usuario
            $transaction->user->notify(new \App\Notifications\TransactionCompleted($transaction));

            return redirect()->back()->with('success', 'Transacción completada exitosamente. El cliente ha sido notificado.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancelar transacción
     */
    public function cancel(Request $request, Transaction $transaction)
    {
        // Verificar que el usuario sea admin o el dueño de la transacción
        if (!auth()->user()->hasRole('admin') && $transaction->user_id !== auth()->id()) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $oldStatus = $transaction->status;
            $transaction->cancel();

            // Crear log
            \App\Models\TransactionLog::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'action' => 'cancelled',
                'old_status' => $oldStatus,
                'new_status' => 'cancelled',
                'comment' => $validated['reason'],
            ]);

            return redirect()->back()->with('success', 'Transacción cancelada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
