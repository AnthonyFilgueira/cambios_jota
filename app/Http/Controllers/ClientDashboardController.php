<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class ClientDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Transacciones del cliente
        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Estadísticas del cliente
        $stats = [
            'total_transactions' => Transaction::where('user_id', $user->id)->count(),
            'total_amount' => Transaction::where('user_id', $user->id)->sum('amount'),
            'recent_count' => Transaction::where('user_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
        ];

        return view('client-dashboard', compact('user', 'transactions', 'stats'));
    }
}
