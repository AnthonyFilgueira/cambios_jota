<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SellerReportController extends Controller
{
    /**
     * Reporte individual de rendimiento por vendedor
     */
    public function performance(Request $request, Seller $seller)
    {
        // Obtener filtro de período (default: mes actual)
        $period = $request->get('period', 'month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Calcular fechas según período
        [$startDate, $endDate] = $this->calculateDateRange($period, $customStart, $customEnd);

        // Métricas del vendedor en el período
        $metrics = $seller->metricsBetween($startDate, $endDate);

        // Información de monedero
        $wallet = [
            'balance' => $seller->walletBalance(),
            'recent_transactions' => $seller->walletTransactions()->limit(10)->get(),
        ];

        // Liquidaciones
        $liquidations = [
            'total' => $seller->liquidations()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'count' => $seller->liquidations()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'recent' => $seller->liquidations()->limit(10)->get(),
        ];

        // Posición en rankings
        $rankings = $this->getSellerRankings($seller, $startDate, $endDate);

        // Comparación con período anterior
        $comparison = $this->compareWithPreviousPeriod($seller, $startDate, $endDate, $metrics);

        // Comparación con promedio del sistema
        $systemAverage = $this->getSystemAverage($startDate, $endDate);

        return view('reports.seller-performance', compact(
            'seller',
            'metrics',
            'wallet',
            'liquidations',
            'rankings',
            'comparison',
            'systemAverage',
            'period',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Rankings globales de todos los vendedores
     */
    public function rankings(Request $request)
    {
        // Obtener filtro de período (default: mes actual)
        $period = $request->get('period', 'month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Calcular fechas según período
        [$startDate, $endDate] = $this->calculateDateRange($period, $customStart, $customEnd);

        // Obtener todos los vendedores con métricas
        $sellers = Seller::all()->map(function ($seller) use ($startDate, $endDate) {
            $metrics = $seller->metricsBetween($startDate, $endDate);

            return [
                'seller' => $seller,
                'total_sales' => $metrics['total_amount'],
                'sales_count' => $metrics['total_sales'],
                'approved_count' => $metrics['approved_sales'],
                'seller_commission' => $metrics['seller_commission'],
                'boss_commission' => $metrics['boss_commission'],
                'average_ticket' => $metrics['average_ticket'],
                'conversion_rate' => $metrics['conversion_rate'],
                'wallet_balance' => $seller->walletBalance(),
            ];
        });

        // Ordenar por defecto por total vendido
        $sortBy = $request->get('sort', 'total_sales');
        $sortDirection = $request->get('direction', 'desc');

        $sellers = $sortDirection === 'desc'
            ? $sellers->sortByDesc($sortBy)
            : $sellers->sortBy($sortBy);

        return view('reports.seller-rankings', compact(
            'sellers',
            'period',
            'startDate',
            'endDate',
            'sortBy',
            'sortDirection'
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
            'all' => ['2000-01-01', $end],
            default => [Carbon::now()->startOfMonth()->format('Y-m-d'), $end],
        };
    }

    /**
     * Obtener posición del vendedor en rankings
     */
    private function getSellerRankings(Seller $seller, $startDate, $endDate)
    {
        // Ranking por monto
        $rankingByAmount = Seller::rankingByTotalSales($startDate, $endDate, 100);
        $positionByAmount = $rankingByAmount->search(function ($item) use ($seller) {
            return $item['seller']->id === $seller->id;
        });

        // Ranking por cantidad
        $rankingByCount = Seller::rankingBySalesCount($startDate, $endDate, 100);
        $positionByCount = $rankingByCount->search(function ($item) use ($seller) {
            return $item['seller']->id === $seller->id;
        });

        // Ranking por comisiones
        $rankingByCommission = Seller::rankingByCommissions($startDate, $endDate, 100);
        $positionByCommission = $rankingByCommission->search(function ($item) use ($seller) {
            return $item['seller']->id === $seller->id;
        });

        return [
            'by_amount' => $positionByAmount !== false ? $positionByAmount + 1 : null,
            'by_count' => $positionByCount !== false ? $positionByCount + 1 : null,
            'by_commission' => $positionByCommission !== false ? $positionByCommission + 1 : null,
            'total_sellers' => Seller::count(),
        ];
    }

    /**
     * Comparar con período anterior
     */
    private function compareWithPreviousPeriod(Seller $seller, $startDate, $endDate, $currentMetrics)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diff = $start->diffInDays($end);

        // Calcular período anterior
        $prevStart = $start->copy()->subDays($diff + 1)->format('Y-m-d');
        $prevEnd = $start->copy()->subDay()->format('Y-m-d');

        // Métricas del período anterior
        $prevMetrics = $seller->metricsBetween($prevStart, $prevEnd);

        return [
            'sales_change' => $this->calculatePercentageChange(
                $prevMetrics['total_amount'],
                $currentMetrics['total_amount']
            ),
            'count_change' => $this->calculatePercentageChange(
                $prevMetrics['total_sales'],
                $currentMetrics['total_sales']
            ),
            'commission_change' => $this->calculatePercentageChange(
                $prevMetrics['seller_commission'],
                $currentMetrics['seller_commission']
            ),
            'ticket_change' => $this->calculatePercentageChange(
                $prevMetrics['average_ticket'],
                $currentMetrics['average_ticket']
            ),
        ];
    }

    /**
     * Obtener promedio del sistema
     */
    private function getSystemAverage($startDate, $endDate)
    {
        $sellers = Seller::all();
        $count = $sellers->count();

        if ($count === 0) {
            return [
                'total_sales' => 0,
                'sales_count' => 0,
                'average_ticket' => 0,
                'seller_commission' => 0,
            ];
        }

        $totals = $sellers->reduce(function ($carry, $seller) use ($startDate, $endDate) {
            $metrics = $seller->metricsBetween($startDate, $endDate);

            return [
                'total_sales' => $carry['total_sales'] + $metrics['total_amount'],
                'sales_count' => $carry['sales_count'] + $metrics['total_sales'],
                'seller_commission' => $carry['seller_commission'] + $metrics['seller_commission'],
            ];
        }, ['total_sales' => 0, 'sales_count' => 0, 'seller_commission' => 0]);

        return [
            'total_sales' => $totals['total_sales'] / $count,
            'sales_count' => $totals['sales_count'] / $count,
            'average_ticket' => $totals['sales_count'] > 0
                ? $totals['total_sales'] / $totals['sales_count']
                : 0,
            'seller_commission' => $totals['seller_commission'] / $count,
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
