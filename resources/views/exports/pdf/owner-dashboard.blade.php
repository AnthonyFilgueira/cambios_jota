<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard del Dueño</title>
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
            font-size: 24pt;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10pt;
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
            padding: 15px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
        }
        .metric-label {
            font-size: 9pt;
            color: #6B7280;
            margin-bottom: 5px;
        }
        .metric-value {
            font-size: 18pt;
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
        thead {
            background: #F3F4F6;
        }
        th {
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
        .badge {
            display: inline-block;
            width: 18px;
            height: 18px;
            line-height: 18px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            font-size: 8pt;
        }
        .badge-gold {
            background: #FCD34D;
            color: #92400E;
        }
        .badge-silver {
            background: #E5E7EB;
            color: #4B5563;
        }
        .badge-bronze {
            background: #FDBA74;
            color: #9A3412;
        }
        .footer {
            margin-top: 30px;
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
        <h1>📊 Dashboard del Dueño</h1>
        <p>Cambio J - Plataforma de Gestión de Divisas</p>
    </div>

    <div class="info-box">
        <p><strong>Período:</strong> {{ $period }} ({{ $startDate }} - {{ $endDate }})</p>
        <p><strong>Generado:</strong> {{ $generatedAt }}</p>
    </div>

    <div class="section-title">Métricas Globales</div>

    <div class="metrics-grid">
        <div class="metric-row">
            <div class="metric">
                <div class="metric-label">Total Vendido</div>
                <div class="metric-value">S/. {{ number_format($metrics['total_sales'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Cantidad de Ventas</div>
                <div class="metric-value">{{ $metrics['sales_count'] }}</div>
            </div>
        </div>
        <div class="metric-row">
            <div class="metric">
                <div class="metric-label">Comisiones Vendedores</div>
                <div class="metric-value">S/. {{ number_format($metrics['seller_commissions'], 2) }}</div>
            </div>
            <div class="metric">
                <div class="metric-label">Mis Comisiones (Dueño)</div>
                <div class="metric-value">S/. {{ number_format($metrics['boss_commissions'], 2) }}</div>
            </div>
        </div>
    </div>

    <div class="section-title">Top Vendedores por Monto</div>
    <table>
        <thead>
            <tr>
                <th width="8%">#</th>
                <th width="35%">Vendedor</th>
                <th width="20%" class="text-right">Total Vendido</th>
                <th width="15%" class="text-right">Cantidad</th>
                <th width="22%" class="text-right">Comisión</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankings['by_sales'] as $index => $rank)
                <tr>
                    <td>
                        @if($index < 3)
                            <span class="badge {{ $index === 0 ? 'badge-gold' : ($index === 1 ? 'badge-silver' : 'badge-bronze') }}">
                                {{ $index + 1 }}
                            </span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $rank['seller']->name }}</td>
                    <td class="text-right">S/. {{ number_format($rank['total_sales'], 2) }}</td>
                    <td class="text-right">{{ $rank['sales_count'] }}</td>
                    <td class="text-right">S/. {{ number_format($rank['commission'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Top Vendedores por Cantidad</div>
    <table>
        <thead>
            <tr>
                <th width="8%">#</th>
                <th width="35%">Vendedor</th>
                <th width="20%" class="text-right">Cantidad</th>
                <th width="15%" class="text-right">Total Vendido</th>
                <th width="22%" class="text-right">Comisión</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rankings['by_count'] as $index => $rank)
                <tr>
                    <td>
                        @if($index < 3)
                            <span class="badge {{ $index === 0 ? 'badge-gold' : ($index === 1 ? 'badge-silver' : 'badge-bronze') }}">
                                {{ $index + 1 }}
                            </span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $rank['seller']->name }}</td>
                    <td class="text-right">{{ $rank['sales_count'] }}</td>
                    <td class="text-right">S/. {{ number_format($rank['total_sales'], 2) }}</td>
                    <td class="text-right">S/. {{ number_format($rank['commission'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Documento generado automáticamente por Cambio J | {{ $generatedAt }}</p>
        <p>Este reporte contiene información confidencial</p>
    </div>
</body>
</html>
