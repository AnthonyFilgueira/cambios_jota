<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Vendedor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #374151;
        }
        .header {
            background: linear-gradient(to right, #5B21B6, #7C3AED);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20pt;
            margin-bottom: 5px;
        }
        .header h2 {
            font-size: 16pt;
            margin-bottom: 3px;
        }
        .header p {
            font-size: 9pt;
            opacity: 0.9;
        }
        .info-box {
            background: #F3F4F6;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #7C3AED;
        }
        .info-box p {
            margin: 3px 0;
        }
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .metric-row {
            display: table-row;
        }
        .metric {
            display: table-cell;
            width: 50%;
            padding: 10px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }
        .metric-label {
            font-size: 9pt;
            color: #6B7280;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 16pt;
            font-weight: bold;
            color: #5B21B6;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #5B21B6;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #7C3AED;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th {
            background: #F3F4F6;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            border-bottom: 2px solid #7C3AED;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9pt;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding-top: 10px;
            border-top: 2px solid #E5E7EB;
            font-size: 8pt;
            color: #6B7280;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Reporte de Rendimiento</h1>
        <h2>{{ $seller->name }}</h2>
        <p>Código: {{ $seller->code }} | Comisión: {{ $seller->seller_commission }}%</p>
    </div>

    <div class="info-box">
        <p><strong>Período:</strong> {{ $period }} ({{ $startDate }} - {{ $endDate }})</p>
        <p><strong>Generado:</strong> {{ $generatedAt }}</p>
    </div>

    <div class="section-title">Métricas Principales</div>

    <div class="metrics-grid">
        <div class="metric-row">
            <div class="metric">
                <div class="metric-label">Total Vendido</div>
                <div class="metric-value">S/. {{ number_format($metrics['total_amount'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Cantidad de Ventas</div>
                <div class="metric-value">{{ $metrics['total_sales'] }}</div>
            </div>
        </div>
        <div class="metric-row">
            <div class="metric">
                <div class="metric-label">Ticket Promedio</div>
                <div class="metric-value">S/. {{ number_format($metrics['average_ticket'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Tasa de Conversión</div>
                <div class="metric-value">{{ number_format($metrics['conversion_rate'], 1) }}%</div>
            </div>
        </div>
        <div class="metric-row">
            <div class="metric">
                <div class="metric-label">Comisión Generada</div>
                <div class="metric-value">S/. {{ number_format($metrics['seller_commission'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Saldo en Monedero</div>
                <div class="metric-value">S/. {{ number_format($walletBalance, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="section-title">Desglose por Estado</div>
    <table>
        <tr>
            <th>Estado</th>
            <th class="text-right">Cantidad</th>
        </tr>
        <tr>
            <td>✓ Aprobadas</td>
            <td class="text-right">{{ $metrics['approved_sales'] }}</td>
        </tr>
        <tr>
            <td>✓ Completadas</td>
            <td class="text-right">{{ $metrics['completed_sales'] }}</td>
        </tr>
        <tr>
            <td>⏳ Pendientes</td>
            <td class="text-right">{{ $metrics['pending_sales'] }}</td>
        </tr>
        <tr>
            <td>👁 Observadas</td>
            <td class="text-right">{{ $metrics['observed_sales'] }}</td>
        </tr>
        <tr>
            <td>✗ Rechazadas</td>
            <td class="text-right">{{ $metrics['rejected_sales'] }}</td>
        </tr>
    </table>

    <div class="section-title">Liquidaciones Recientes</div>
    @if($liquidations['recent']->count() > 0)
        <table>
            <tr>
                <th>Fecha</th>
                <th>Método de Pago</th>
                <th class="text-right">Monto</th>
            </tr>
            @foreach($liquidations['recent'] as $liq)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($liq->payment_date)->format('d/m/Y') }}</td>
                    <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $liq->payment_method) }}</td>
                    <td class="text-right">S/. {{ number_format($liq->amount, 2) }}</td>
                </tr>
            @endforeach
            <tr style="background: #F3F4F6; font-weight: bold;">
                <td colspan="2">TOTAL LIQUIDADO</td>
                <td class="text-right">S/. {{ number_format($liquidations['total'], 2) }}</td>
            </tr>
        </table>
    @else
        <p style="padding: 10px; background: #F3F4F6; text-align: center; color: #6B7280;">
            No hay liquidaciones registradas en este período
        </p>
    @endif

    <div class="footer">
        <p>Documento generado automáticamente por {{ config('client.name') }} | {{ $generatedAt }}</p>
        <p>Este reporte contiene información confidencial</p>
    </div>
</body>
</html>
