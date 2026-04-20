<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Sale;
use App\Models\Liquidation;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    /**
     * Mostrar dashboard del dueño con métricas y rankings
     */
    public function index(Request $request)
    {
        // Obtener filtro de período (default: mes actual)
        $period = $request->get('period', 'month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Calcular fechas según período
        [$startDate, $endDate] = $this->calculateDateRange($period, $customStart, $customEnd);

        // MÉTRICAS GLOBALES
        $metrics = [
            'total_sales' => Seller::globalTotalSales($startDate, $endDate),
            'seller_commissions' => Seller::globalSellerCommissions($startDate, $endDate),
            'boss_commissions' => Seller::globalBossCommissions($startDate, $endDate),
            'sales_count' => Sale::whereBetween('sale_date', [$startDate, $endDate])->count(),
            'approved_count' => Sale::whereBetween('sale_date', [$startDate, $endDate])
                ->whereIn('approval_status', ['approved', 'completed'])
                ->count(),
            'pending_count' => Sale::whereBetween('sale_date', [$startDate, $endDate])
                ->whereIn('approval_status', ['pending_seller', 'pending_admin'])
                ->count(),
            'average_ticket' => $this->calculateAverageTicket($startDate, $endDate),
        ];

        // RANKINGS
        $rankings = [
            'by_sales' => Seller::rankingByTotalSales($startDate, $endDate, 5),
            'by_count' => Seller::rankingBySalesCount($startDate, $endDate, 5),
            'by_commissions' => Seller::rankingByCommissions($startDate, $endDate, 5),
        ];

        // LIQUIDACIONES
        $liquidations = [
            'total_liquidated' => Liquidation::whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'count' => Liquidation::whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'by_method' => $this->getLiquidationsByMethod($startDate, $endDate),
            'recent' => Liquidation::with('seller')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->orderBy('payment_date', 'desc')
                ->limit(5)
                ->get(),
        ];

        // MONEDEROS
        $wallets = [
            'total_balance' => $this->getTotalWalletBalance(),
            'pending_liquidations' => $this->getPendingLiquidations(),
        ];

        // COMPARACIÓN CON PERÍODO ANTERIOR
        $comparison = $this->compareWithPreviousPeriod($startDate, $endDate, $metrics);

        return view('owner-dashboard', compact(
            'metrics',
            'rankings',
            'liquidations',
            'wallets',
            'comparison',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Calcular rango de fechas según período seleccionado
     */
    private function calculateDateRange($period, $customStart = null, $customEnd = null)
    {
        if ($period === 'custom' && $customStart && $customEnd) {
            return [$customStart, $customEnd];
        }

        $end = Carbon::now()->format('Y-m-d');

        return match ($period) {
            'today' => [Carbon::today()->format('Y-m-d'), $end],
            'week' => [Carbon::now()->startOfWeek()->format('Y-m-d'), $end],
            'month' => [Carbon::now()->startOfMonth()->format('Y-m-d'), $end],
            'quarter' => [Carbon::now()->startOfQuarter()->format('Y-m-d'), $end],
            'year' => [Carbon::now()->startOfYear()->format('Y-m-d'), $end],
            'all' => ['2000-01-01', $end], // Todo el tiempo
            default => [Carbon::now()->startOfMonth()->format('Y-m-d'), $end],
        };
    }

    /**
     * Calcular ticket promedio del sistema
     */
    private function calculateAverageTicket($startDate, $endDate)
    {
        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->whereIn('approval_status', ['approved', 'completed'])
            ->get();

        if ($sales->isEmpty()) {
            return 0;
        }

        return $sales->avg('amount');
    }

    /**
     * Obtener liquidaciones agrupadas por método de pago
     */
    private function getLiquidationsByMethod($startDate, $endDate)
    {
        return Liquidation::whereBetween('payment_date', [$startDate, $endDate])
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn($item) => [$item->payment_method => [
                'total' => $item->total,
                'count' => $item->count,
            ]]);
    }

    /**
     * Obtener saldo total en monederos
     */
    private function getTotalWalletBalance()
    {
        return Seller::all()->sum(fn($seller) => $seller->walletBalance());
    }

    /**
     * Obtener saldo pendiente de liquidar
     */
    private function getPendingLiquidations()
    {
        // Vendedores con saldo > 0 ordenados por saldo descendente
        return Seller::all()
            ->map(fn($seller) => [
                'seller' => $seller,
                'balance' => $seller->walletBalance(),
            ])
            ->filter(fn($item) => $item['balance'] > 0)
            ->sortByDesc('balance');
    }

    /**
     * Comparar métricas con período anterior
     */
    private function compareWithPreviousPeriod($startDate, $endDate, $currentMetrics)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diff = $start->diffInDays($end);

        // Calcular período anterior
        $prevStart = $start->copy()->subDays($diff + 1)->format('Y-m-d');
        $prevEnd = $start->copy()->subDay()->format('Y-m-d');

        // Métricas del período anterior
        $prevMetrics = [
            'total_sales' => Seller::globalTotalSales($prevStart, $prevEnd),
            'sales_count' => Sale::whereBetween('sale_date', [$prevStart, $prevEnd])->count(),
        ];

        // Calcular porcentajes de cambio
        return [
            'sales_change' => $this->calculatePercentageChange(
                $prevMetrics['total_sales'],
                $currentMetrics['total_sales']
            ),
            'count_change' => $this->calculatePercentageChange(
                $prevMetrics['sales_count'],
                $currentMetrics['sales_count']
            ),
        ];
    }

    /**
     * Calcular porcentaje de cambio
     */
    private function calculatePercentageChange($old, $new)
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }

        return round((($new - $old) / $old) * 100, 1);
    }
}
