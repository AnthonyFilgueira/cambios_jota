<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user   = Auth::user();
        $seller = $user->seller;

        if (!$seller) {
            abort(403, 'No tienes un perfil de vendedor asociado.');
        }

        $type = $request->input('type');
        $days = $request->input('days', 30);

        $query = $seller->walletTransactions();

        if ($type) {
            $query->where('type', $type);
        }

        if ($days !== 'all') {
            $query->where('created_at', '>=', now()->subDays((int) $days));
        }

        $transactions = $query->paginate(20);
        $balance      = $seller->walletBalance();

        // KPIs
        $totalEarned      = $seller->walletTransactions()->where('amount', '>', 0)->sum('amount');
        $totalLiquidated  = abs($seller->walletTransactions()->where('type', 'liquidation')->sum('amount'));
        $totalCommissions = $seller->walletTransactions()->where('type', 'commission')->sum('amount');

        // Datos para gráfico semanal (últimas 8 semanas)
        $weeklyData = collect(range(7, 0))->map(function ($weeksAgo) use ($seller) {
            $start = now()->startOfWeek()->subWeeks($weeksAgo);
            $end   = $start->copy()->endOfWeek();
            return [
                'label'  => $start->format('d M'),
                'amount' => (float) $seller->walletTransactions()
                    ->where('type', 'commission')
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('amount'),
            ];
        });

        return view('wallet.index', compact(
            'seller', 'transactions', 'balance', 'type', 'days',
            'totalEarned', 'totalLiquidated', 'totalCommissions', 'weeklyData'
        ));
    }
}
