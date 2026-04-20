<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Mostrar monedero del vendedor autenticado
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Por ahora asumimos que el user_id = seller_id
        // TODO: Actualizar cuando se implemente relación User -> Seller
        $seller = \App\Models\Seller::find($user->id);

        if (!$seller) {
            abort(404, 'Vendedor no encontrado');
        }

        // Filtros opcionales
        $type = $request->input('type'); // commission, liquidation, adjustment
        $days = $request->input('days', 30); // Últimos 30 días por defecto

        $query = $seller->walletTransactions();

        if ($type) {
            $query->where('type', $type);
        }

        if ($days !== 'all') {
            $query->recent($days);
        }

        $transactions = $query->paginate(20);
        $balance = $seller->walletBalance();

        return view('wallet.index', compact('seller', 'transactions', 'balance', 'type', 'days'));
    }
}
