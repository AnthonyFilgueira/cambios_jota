<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Seller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
        $end   = $request->input('end_date')   ?? now()->endOfMonth()->toDateString();
        $status = $request->input('status', 'all');
        $sellerId = $request->input('seller_id');

        $query = Transaction::with([
                'seller',
                'user',
                'exchangeRate.currencyPair.fromCurrency',
                'exchangeRate.currencyPair.toCurrency',
            ])
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($sellerId) {
            $query->where('seller_id', $sellerId);
        }

        $transactions = $query->orderByDesc('created_at')->get();

        $sellers = Seller::orderBy('name')->get();

        $totals = [
            'count'      => $transactions->count(),
            'amount_pen' => $transactions->sum('amount_pen'),
            'amount_ves' => $transactions->sum('amount_ves'),
            'completed'  => $transactions->where('status', 'completed')->count(),
            'pending'    => $transactions->whereIn('status', ['pending', 'processing'])->count(),
            'cancelled'  => $transactions->where('status', 'cancelled')->count(),
        ];

        return view('reports.index', compact('transactions', 'sellers', 'start', 'end', 'status', 'sellerId', 'totals'));
    }

    public function conciliation(Request $request)
    {
        $start = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
        $end   = $request->input('end_date')   ?? now()->endOfMonth()->toDateString();

        $transactions = Transaction::with([
                'seller',
                'user',
                'exchangeRate.currencyPair.fromCurrency',
                'exchangeRate.currencyPair.toCurrency',
            ])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->get();

        $totals = [
            'count'      => $transactions->count(),
            'amount_pen' => $transactions->sum('amount_pen'),
            'amount_ves' => $transactions->sum('amount_ves'),
        ];

        return view('reports.conciliation', compact('transactions', 'start', 'end', 'totals'));
    }

    public function exportTransactions(Request $request)
    {
        $start    = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
        $end      = $request->input('end_date')   ?? now()->endOfMonth()->toDateString();
        $status   = $request->input('status', 'all');
        $sellerId = $request->input('seller_id');

        return Excel::download(
            new \App\Exports\TransactionExport($start, $end, $status, $sellerId),
            'transacciones-' . $start . '-al-' . $end . '.xlsx'
        );
    }

    public function exportConciliation(Request $request)
    {
        $start = $request->input('start_date') ?? now()->startOfMonth()->toDateString();
        $end   = $request->input('end_date')   ?? now()->endOfMonth()->toDateString();

        return Excel::download(
            new \App\Exports\ConciliationExport($start, $end),
            'conciliacion-' . $start . '-al-' . $end . '.xlsx'
        );
    }
}
