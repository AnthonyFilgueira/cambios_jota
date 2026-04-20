<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Exportar rankings a CSV
     */
    public function rankingsCSV(Request $request)
    {
        [$startDate, $endDate] = $this->calculateDateRange(
            $request->get('period', 'month'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        $sellers = Seller::all()->map(function ($seller) use ($startDate, $endDate) {
            $metrics = $seller->metricsBetween($startDate, $endDate);
            return [
                'codigo' => $seller->code,
                'nombre' => $seller->name,
                'total_vendido' => $metrics['total_amount'],
                'cantidad_ventas' => $metrics['total_sales'],
                'ventas_aprobadas' => $metrics['approved_sales'],
                'ticket_promedio' => $metrics['average_ticket'],
                'tasa_conversion' => $metrics['conversion_rate'],
                'comision_vendedor' => $metrics['seller_commission'],
                'comision_dueno' => $metrics['boss_commission'],
                'saldo_monedero' => $seller->walletBalance(),
            ];
        })->sortByDesc('total_vendido');

        $fileName = 'rankings_vendedores_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($sellers) {
            $handle = fopen('php://output', 'w');

            // BOM para UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($handle, [
                'Código',
                'Nombre',
                'Total Vendido (S/.)',
                'Cantidad Ventas',
                'Ventas Aprobadas',
                'Ticket Promedio (S/.)',
                'Tasa Conversión (%)',
                'Comisión Vendedor (S/.)',
                'Comisión Dueño (S/.)',
                'Saldo Monedero (S/.)',
            ]);

            // Datos
            foreach ($sellers as $seller) {
                fputcsv($handle, [
                    $seller['codigo'],
                    $seller['nombre'],
                    number_format($seller['total_vendido'], 2, '.', ''),
                    $seller['cantidad_ventas'],
                    $seller['ventas_aprobadas'],
                    number_format($seller['ticket_promedio'], 2, '.', ''),
                    number_format($seller['tasa_conversion'], 1, '.', ''),
                    number_format($seller['comision_vendedor'], 2, '.', ''),
                    number_format($seller['comision_dueno'], 2, '.', ''),
                    number_format($seller['saldo_monedero'], 2, '.', ''),
                ]);
            }

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Exportar rankings a PDF
     */
    public function rankingsPDF(Request $request)
    {
        [$startDate, $endDate] = $this->calculateDateRange(
            $request->get('period', 'month'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        $sellers = Seller::all()->map(function ($seller) use ($startDate, $endDate) {
            $metrics = $seller->metricsBetween($startDate, $endDate);
            return [
                'seller' => $seller,
                'total_sales' => $metrics['total_amount'],
                'sales_count' => $metrics['total_sales'],
                'approved_count' => $metrics['approved_sales'],
                'average_ticket' => $metrics['average_ticket'],
                'conversion_rate' => $metrics['conversion_rate'],
                'seller_commission' => $metrics['seller_commission'],
                'wallet_balance' => $seller->walletBalance(),
            ];
        })->sortByDesc('total_sales');

        $data = [
            'sellers' => $sellers,
            'period' => ucfirst($request->get('period', 'month')),
            'startDate' => Carbon::parse($startDate)->format('d/m/Y'),
            'endDate' => Carbon::parse($endDate)->format('d/m/Y'),
            'generatedAt' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('exports.pdf.rankings', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('rankings_vendedores_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar reporte individual a PDF
     */
    public function sellerReportPDF(Request $request, Seller $seller)
    {
        [$startDate, $endDate] = $this->calculateDateRange(
            $request->get('period', 'month'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        $metrics = $seller->metricsBetween($startDate, $endDate);

        $liquidations = [
            'total' => $seller->liquidations()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount'),
            'count' => $seller->liquidations()
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            'recent' => $seller->liquidations()->limit(10)->get(),
        ];

        $data = [
            'seller' => $seller,
            'metrics' => $metrics,
            'walletBalance' => $seller->walletBalance(),
            'liquidations' => $liquidations,
            'period' => ucfirst($request->get('period', 'month')),
            'startDate' => Carbon::parse($startDate)->format('d/m/Y'),
            'endDate' => Carbon::parse($endDate)->format('d/m/Y'),
            'generatedAt' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('exports.pdf.seller-report', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('reporte_' . $seller->code . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar dashboard del dueño a CSV
     */
    public function ownerDashboardCSV(Request $request)
    {
        [$startDate, $endDate] = $this->calculateDateRange(
            $request->get('period', 'month'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        $metrics = [
            'total_sales' => Seller::globalTotalSales($startDate, $endDate),
            'seller_commissions' => Seller::globalSellerCommissions($startDate, $endDate),
            'boss_commissions' => Seller::globalBossCommissions($startDate, $endDate),
            'sales_count' => Sale::whereBetween('sale_date', [$startDate, $endDate])->count(),
        ];

        $fileName = 'dashboard_dueno_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($metrics, $startDate, $endDate) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezado
            fputcsv($handle, ['Dashboard del Dueño - Cambio J']);
            fputcsv($handle, ['Período', Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y')]);
            fputcsv($handle, ['Generado', Carbon::now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);

            // Métricas
            fputcsv($handle, ['Métrica', 'Valor']);
            fputcsv($handle, ['Total Vendido (S/.)', number_format($metrics['total_sales'], 2, '.', '')]);
            fputcsv($handle, ['Comisiones Vendedores (S/.)', number_format($metrics['seller_commissions'], 2, '.', '')]);
            fputcsv($handle, ['Comisiones Dueño (S/.)', number_format($metrics['boss_commissions'], 2, '.', '')]);
            fputcsv($handle, ['Cantidad de Ventas', $metrics['sales_count']]);

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Exportar dashboard del dueño a PDF
     */
    public function ownerDashboardPDF(Request $request)
    {
        [$startDate, $endDate] = $this->calculateDateRange(
            $request->get('period', 'month'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        $metrics = [
            'total_sales' => Seller::globalTotalSales($startDate, $endDate),
            'seller_commissions' => Seller::globalSellerCommissions($startDate, $endDate),
            'boss_commissions' => Seller::globalBossCommissions($startDate, $endDate),
            'sales_count' => Sale::whereBetween('sale_date', [$startDate, $endDate])->count(),
        ];

        $rankings = [
            'by_sales' => Seller::rankingByTotalSales($startDate, $endDate, 10),
            'by_count' => Seller::rankingBySalesCount($startDate, $endDate, 10),
        ];

        $data = [
            'metrics' => $metrics,
            'rankings' => $rankings,
            'period' => ucfirst($request->get('period', 'month')),
            'startDate' => Carbon::parse($startDate)->format('d/m/Y'),
            'endDate' => Carbon::parse($endDate)->format('d/m/Y'),
            'generatedAt' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $pdf = Pdf::loadView('exports.pdf.owner-dashboard', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('dashboard_dueno_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Calcular rango de fechas según período
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
}
