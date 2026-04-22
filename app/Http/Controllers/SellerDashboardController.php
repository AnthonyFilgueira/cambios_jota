<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\Sale;
use Carbon\Carbon;

class SellerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Buscar vendedor por nombre (temporal - idealmente usar user_id)
        $seller = Seller::where('name', $user->name)->first();

        if (!$seller) {
            return redirect()->route('dashboard')->with('error', 'No se encontró información de vendedor para este usuario');
        }

        // Período de análisis (por defecto: este mes)
        $period = $request->input('period', 'month');
        $startDate = $this->getStartDate($period, $request->input('start_date'));
        $endDate = $this->getEndDate($period, $request->input('end_date'));

        // Métricas del vendedor
        $metrics = [
            'total_sales' => $seller->totalSalesBetween($startDate, $endDate),
            'sales_count' => $seller->salesCountBetween($startDate, $endDate),
            'pending_count' => $seller->salesCountBetween($startDate, $endDate, 'pending_seller'),
            'approved_count' => $seller->salesCountBetween($startDate, $endDate, 'approved') +
                               $seller->salesCountBetween($startDate, $endDate, 'completed'),
            'rejected_count' => $seller->salesCountBetween($startDate, $endDate, 'rejected'),
            'observed_count' => $seller->salesCountBetween($startDate, $endDate, 'observed'),
            'seller_commission' => $seller->sellerCommissionTotal($startDate, $endDate),
            'average_ticket' => $seller->averageTicketBetween($startDate, $endDate),
            'conversion_rate' => $seller->conversionRateBetween($startDate, $endDate),
        ];

        // Monedero
        $wallet = [
            'balance' => $seller->walletBalance(),
            'recent_transactions' => $seller->walletTransactions()->take(5)->get(),
        ];

        // Liquidaciones recientes
        $liquidations = [
            'recent' => $seller->liquidations()->take(5)->get(),
            'total_liquidated' => $seller->liquidations()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
        ];

        // Ventas recientes
        $recentSales = $seller->sales()
            ->with('seller')
            ->orderBy('sale_date', 'desc')
            ->take(10)
            ->get();

        // Ventas por estado
        $salesByStatus = [
            'pending' => $seller->salesByStatus('pending_seller'),
            'observed' => $seller->salesByStatus('observed'),
        ];

        return view('seller-dashboard', compact(
            'seller',
            'metrics',
            'wallet',
            'liquidations',
            'recentSales',
            'salesByStatus',
            'period',
            'startDate',
            'endDate'
        ));
    }

    private function getStartDate($period, $customStart = null)
    {
        return match($period) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'quarter' => Carbon::now()->startOfQuarter(),
            'year' => Carbon::now()->startOfYear(),
            'custom' => $customStart ? Carbon::parse($customStart) : Carbon::now()->startOfMonth(),
            default => Carbon::now()->startOfMonth(),
        };
    }

    private function getEndDate($period, $customEnd = null)
    {
        return match($period) {
            'today' => Carbon::today()->endOfDay(),
            'week' => Carbon::now()->endOfWeek(),
            'month' => Carbon::now()->endOfMonth(),
            'quarter' => Carbon::now()->endOfQuarter(),
            'year' => Carbon::now()->endOfYear(),
            'custom' => $customEnd ? Carbon::parse($customEnd)->endOfDay() : Carbon::now()->endOfMonth(),
            default => Carbon::now()->endOfMonth(),
        };
    }
}
