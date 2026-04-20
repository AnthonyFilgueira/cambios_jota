<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rankings de Vendedores</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background: #5B21B6;
            color: white;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #E5E7EB;
            font-size: 9pt;
        }
        tr:nth-child(even) {
            background: #F9FAFB;
        }
        tr.top-3 {
            background: #FEF3C7 !important;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
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
        <h1>🏆 Rankings de Vendedores</h1>
        <p>Cambio J - Plataforma de Gestión de Divisas</p>
    </div>

    <div class="info-box">
        <p><strong>Período:</strong> {{ $period }} ({{ $startDate }} - {{ $endDate }})</p>
        <p><strong>Generado:</strong> {{ $generatedAt }}</p>
        <p><strong>Total vendedores:</strong> {{ $sellers->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">#</th>
                <th width="12%">Código</th>
                <th width="20%">Nombre</th>
                <th width="13%" class="text-right">Total Vendido</th>
                <th width="10%" class="text-right">Cantidad</th>
                <th width="13%" class="text-right">Ticket Prom.</th>
                <th width="10%" class="text-right">Conversión</th>
                <th width="12%" class="text-right">Comisión</th>
                <th width="10%" class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sellers as $index => $data)
                <tr class="{{ $index < 3 ? 'top-3' : '' }}">
                    <td class="text-center">
                        @if($index < 3)
                            <span class="badge {{ $index === 0 ? 'badge-gold' : ($index === 1 ? 'badge-silver' : 'badge-bronze') }}">
                                {{ $index + 1 }}
                            </span>
                        @else
                            {{ $index + 1 }}
                        @endif
                    </td>
                    <td>{{ $data['seller']->code }}</td>
                    <td>{{ $data['seller']->name }}</td>
                    <td class="text-right">S/. {{ number_format($data['total_sales'], 2) }}</td>
                    <td class="text-right">{{ $data['sales_count'] }} ({{ $data['approved_count'] }})</td>
                    <td class="text-right">S/. {{ number_format($data['average_ticket'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['conversion_rate'], 1) }}%</td>
                    <td class="text-right">S/. {{ number_format($data['seller_commission'], 2) }}</td>
                    <td class="text-right">S/. {{ number_format($data['wallet_balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #F3F4F6; font-weight: bold;">
                <td colspan="3">TOTALES</td>
                <td class="text-right">S/. {{ number_format($sellers->sum('total_sales'), 2) }}</td>
                <td class="text-right">{{ $sellers->sum('sales_count') }}</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">S/. {{ number_format($sellers->sum('seller_commission'), 2) }}</td>
                <td class="text-right">S/. {{ number_format($sellers->sum('wallet_balance'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Documento generado automáticamente por Cambio J | {{ $generatedAt }}</p>
        <p>Este reporte contiene información confidencial</p>
    </div>
</body>
</html>
