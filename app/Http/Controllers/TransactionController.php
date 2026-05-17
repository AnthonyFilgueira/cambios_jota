<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionLog;
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
        $user = auth()->user();
        $isAdmin = $user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('contador');
        $sellerRecord = $user->seller;

        if (!$isAdmin && !$sellerRecord) {
            abort(403, 'No autorizado');
        }

        $query = Transaction::with(['user', 'seller', 'exchangeRate', 'logs.user'])
            ->orderBy('created_at', 'desc');

        // Si es vendedor, solo mostrar sus transacciones
        if ($sellerRecord) {
            $query->where('seller_id', $sellerRecord->id);
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
        $user   = auth()->user()->load('assignedSeller.businessAccounts.bank');
        $seller = $user->assignedSeller;
        $pairs  = $this->getCurrencyPairs();

        // Pre-cargar cuentas del vendedor asignado
        $sellerAccounts = $seller
            ? $seller->businessAccounts->where('active', true)->values()
            : collect();

        return view('transactions.create', compact('pairs', 'seller', 'sellerAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $operationType = $request->input('operation_type', 'transferencia');

        $validated = $request->validate([
            'amount_pen'       => 'required|numeric|min:1',
            'amount_ves'       => 'required|numeric|min:1',
            'exchange_rate_id' => 'required|exists:exchange_rates,id',
            'operation_type'   => 'required|in:transferencia,pago_movil',
            'notes'            => 'nullable|string|max:500',

            // Tasas BCV (snapshot)
            'usd_bcv_rate' => 'nullable|numeric',
            'eur_bcv_rate' => 'nullable|numeric',

            // Datos bancarios del receptor (Venezuela) — comunes
            'recipient_bank' => 'required|string|max:255',
            'recipient_dni'  => 'required|string|max:30',
            'recipient_phone' => 'required|string|max:30',

            // Solo transferencia
            'recipient_account_number' => 'required_if:operation_type,transferencia|nullable|string|max:255',
            'recipient_account_type'   => 'required_if:operation_type,transferencia|nullable|in:ahorro,corriente',

            // Datos de transferencia desde Perú
            'sender_bank'           => 'required|string|max:255',
            'sender_account_number' => 'nullable|string|max:255',
            'sender_dni'            => 'required|string|max:30',

            // Comprobante
            'voucher' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        // Usar el vendedor asignado del usuario autenticado
        $user   = auth()->user()->load('assignedSeller');
        $seller = $user->assignedSeller;

        if (!$seller) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'No tienes un vendedor asignado. Contacta con soporte.']);
        }

        $validated['seller_id'] = $seller->id;
        $validated['user_id']   = $user->id;
        $validated['status']    = 'pending';

        // Snapshot de tasas BCV desde el exchange rate
        $rate = \App\Models\ExchangeRate::findOrFail($validated['exchange_rate_id']);
        $validated['usd_bcv_rate'] = $rate->usd_rate ?? 0;
        $validated['eur_bcv_rate'] = $rate->eur_rate ?? 0;

        // Subida de comprobante
        $validated['voucher'] = $request->file('voucher')->store('vouchers', 'public');

        // Limpiar campos condicionales si es pago móvil
        if ($operationType === 'pago_movil') {
            $validated['recipient_account_number'] = null;
            $validated['recipient_account_type']   = null;
        }

        $transaction = Transaction::create($validated);

        // Notificar al vendedor
        if ($seller->user) {
            $seller->user->notify(new \App\Notifications\NewTransactionForSeller($transaction));
        }

        return redirect()->route('transactions.confirmacion', $transaction)
            ->with('success', '¡Solicitud enviada! El vendedor ' . $seller->name . ' revisará tu comprobante.');
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
     * Pantalla de confirmación post-envío para el cliente
     */
    public function confirmacion(Transaction $transaction)
    {
        if ($transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load(['seller', 'exchangeRate']);

        return view('transactions.confirmacion', compact('transaction'));
    }

    /**
     * Admin sube comprobante final y cierra el ciclo
     */
    public function uploadFinalVoucher(Request $request, Transaction $transaction)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('admin') && !$user->hasRole('contador')) {
            abort(403, 'No autorizado');
        }

        if ($transaction->status !== 'processing') {
            return redirect()->back()->withErrors(['error' => 'Solo se puede completar una transacción en estado "en proceso".']);
        }

        $request->validate([
            'final_voucher' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $path = $request->file('final_voucher')->store('final_vouchers', 'public');

        $oldStatus = $transaction->status;
        $transaction->final_voucher = $path;
        $transaction->save();
        $transaction->complete();

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'user_id'        => auth()->id(),
            'action'         => 'completed',
            'old_status'     => $oldStatus,
            'new_status'     => 'completed',
            'comment'        => 'Completada por ' . $user->name . '. Comprobante final subido.',
        ]);

        // Notificar cliente
        $transaction->user->notify(new \App\Notifications\TransactionStatusChanged($transaction, 'completed'));

        // Notificar vendedor si tiene cuenta
        if ($transaction->seller && $transaction->seller->user) {
            $transaction->seller->user->notify(
                new \App\Notifications\TransactionStatusChanged($transaction, 'completed')
            );
        }

        return redirect()->back()->with('success', '¡Transacción #' . $transaction->id . ' completada! El cliente y el vendedor han sido notificados.');
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
