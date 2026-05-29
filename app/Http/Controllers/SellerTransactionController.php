<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SellerTransactionController extends Controller
{
    private function getSellerOrAbort()
    {
        $seller = auth()->user()->seller;
        abort_if(!$seller, 403, 'No tienes perfil de vendedor asignado.');
        return $seller;
    }

    /** Bandeja principal del vendedor */
    public function bandeja(Request $request)
    {
        $seller     = $this->getSellerOrAbort();
        $statusFilter = $request->input('status', 'all');

        $query = Transaction::with(['user', 'exchangeRate', 'logs'])
            ->where('seller_id', $seller->id)
            ->orderByRaw("FIELD(status,'pending','observed','processing','completed','cancelled')")
            ->orderBy('created_at', 'desc');

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $transactions = $query->get();

        $stats = [
            'pending'    => Transaction::where('seller_id', $seller->id)->where('status', 'pending')->count(),
            'observed'   => Transaction::where('seller_id', $seller->id)->where('status', 'observed')->count(),
            'processing' => Transaction::where('seller_id', $seller->id)->where('status', 'processing')->count(),
            'mes_count'  => Transaction::where('seller_id', $seller->id)
                                ->whereMonth('created_at', now()->month)->count(),
            'mes_volume' => Transaction::where('seller_id', $seller->id)
                                ->whereMonth('created_at', now()->month)->sum('amount_pen'),
        ];

        return view('seller.bandeja', compact('seller', 'transactions', 'stats', 'statusFilter'));
    }

    /** Detalle de una solicitud */
    public function show(Transaction $transaction)
    {
        $seller = $this->getSellerOrAbort();
        abort_if($transaction->seller_id !== $seller->id, 403);

        $transaction->load(['user', 'exchangeRate.currencyPair.fromCurrency', 'exchangeRate.currencyPair.toCurrency', 'logs.user']);

        return view('seller.solicitud-show', compact('transaction', 'seller'));
    }

    /** Vista "Mi Código" con QR, KPIs y enlace de invitación */
    public function miCodigo()
    {
        $seller = $this->getSellerOrAbort();
        $seller->load('commissionRules');

        $stats = [
            'pending'      => \App\Models\Transaction::where('seller_id', $seller->id)->where('status', 'pending')->count(),
            'mes_count'    => \App\Models\Transaction::where('seller_id', $seller->id)->whereMonth('created_at', now()->month)->count(),
            'mes_volume'   => \App\Models\Transaction::where('seller_id', $seller->id)->whereMonth('created_at', now()->month)->sum('amount_pen'),
            'total_clients' => \App\Models\User::where('assigned_seller_id', $seller->id)->count(),
            'total_completed' => \App\Models\Transaction::where('seller_id', $seller->id)->where('status', 'completed')->count(),
        ];

        $publicUrl = url('/') . '?vendedor=' . $seller->code;

        return view('seller.mi-codigo', compact('seller', 'stats', 'publicUrl'));
    }

    /** Aprobar solicitud → escala al dueño (processing) */
    public function approve(Request $request, Transaction $transaction): RedirectResponse
    {
        $seller = $this->getSellerOrAbort();
        abort_if($transaction->seller_id !== $seller->id, 403);
        abort_if(!in_array($transaction->status, ['pending', 'observed']), 422, 'Esta solicitud no puede aprobarse en su estado actual.');

        $transaction->update(['status' => 'processing']);

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'user_id'        => auth()->id(),
            'action'         => 'approved_by_seller',
            'comment'        => 'Solicitud aprobada por el vendedor. Escalada al dueño para ejecución.',
        ]);

        // Notificar al cliente (con email)
        if ($transaction->user) {
            $transaction->user->notify(new \App\Notifications\TransactionProcessed($transaction));
        }

        // Notificar al dueño/admin
        $this->notifyOwners($transaction, 'processing');

        return redirect()->route('seller.bandeja')
            ->with('success', '¡Solicitud aprobada! El dueño ha sido notificado para ejecutar la transferencia.');
    }

    /** Observar solicitud → devuelve al cliente con motivo */
    public function observe(Request $request, Transaction $transaction): RedirectResponse
    {
        $seller = $this->getSellerOrAbort();
        abort_if($transaction->seller_id !== $seller->id, 403);
        abort_if($transaction->status !== 'pending', 422, 'Solo se pueden observar solicitudes pendientes.');

        $request->validate([
            'motivo' => 'required|string|min:10|max:500',
        ], [
            'motivo.required' => 'El motivo de la observación es obligatorio.',
            'motivo.min'      => 'El motivo debe tener al menos 10 caracteres.',
        ]);

        $transaction->update([
            'status'      => 'observed',
            'observation' => $request->motivo,
        ]);

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'user_id'        => auth()->id(),
            'action'         => 'observed_by_seller',
            'comment'        => $request->motivo,
        ]);

        // Notificar al cliente (con email)
        if ($transaction->user) {
            $transaction->user->notify(new \App\Notifications\TransactionObserved($transaction));
        }

        // Notificar al dueño/admin
        $this->notifyOwners($transaction, 'observed', $request->motivo);

        return redirect()->route('seller.bandeja')
            ->with('success', 'Observación enviada. El cliente ha sido notificado para corregir su solicitud.');
    }

    /** Denegar solicitud → cierra definitivamente */
    public function deny(Request $request, Transaction $transaction): RedirectResponse
    {
        $seller = $this->getSellerOrAbort();
        abort_if($transaction->seller_id !== $seller->id, 403);
        abort_if(!in_array($transaction->status, ['pending', 'observed']), 422, 'Esta solicitud no puede denegarse en su estado actual.');

        $request->validate([
            'motivo' => 'required|string|min:10|max:500',
        ], [
            'motivo.required' => 'El motivo del rechazo es obligatorio.',
            'motivo.min'      => 'El motivo debe tener al menos 10 caracteres.',
        ]);

        $transaction->update([
            'status'      => 'cancelled',
            'observation' => $request->motivo,
        ]);

        TransactionLog::create([
            'transaction_id' => $transaction->id,
            'user_id'        => auth()->id(),
            'action'         => 'denied_by_seller',
            'comment'        => $request->motivo,
        ]);

        // Notificar al cliente (con email)
        if ($transaction->user) {
            $transaction->user->notify(new \App\Notifications\TransactionStatusChanged($transaction, 'cancelled', $request->motivo));
        }

        // Notificar al dueño/admin
        $this->notifyOwners($transaction, 'cancelled', $request->motivo);

        return redirect()->route('seller.bandeja')
            ->with('success', 'Solicitud denegada. El cliente ha sido notificado.');
    }
}
