<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferencia Completada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(to right, #5B21B6, #7C3AED);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .highlight {
            background: #DDD6FE;
            padding: 15px;
            border-left: 4px solid #5B21B6;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: #14B8A6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .details {
            margin: 20px 0;
        }
        .details-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .details-label {
            font-weight: bold;
            color: #5B21B6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0;">Cambio J</h1>
        <p style="margin: 10px 0 0 0;">Transferencia Completada</p>
    </div>

    <div class="content">
        <h2 style="color: #5B21B6;">¡Comprobante de Transferencia Disponible!</h2>

        <p>Estimado/a <strong>{{ $sale->seller->name }}</strong>,</p>

        <p>Te informamos que la venta <strong>#{{ $sale->id }}</strong> ha sido procesada exitosamente y el comprobante de transferencia ya está disponible.</p>

        <div class="highlight">
            <h3 style="margin-top: 0; color: #5B21B6;">Detalles de la Venta</h3>
            <div class="details">
                <div class="details-row">
                    <span class="details-label">ID de Venta:</span>
                    <span>#{{ $sale->id }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Fecha:</span>
                    <span>{{ $sale->sale_date->format('d/m/Y') }}</span>
                </div>
                <div class="details-row">
                    <span class="details-label">Monto:</span>
                    <span>S/. {{ number_format($sale->amount, 2) }}</span>
                </div>
                @if($sale->seller_commission_percent)
                <div class="details-row">
                    <span class="details-label">Tu Comisión:</span>
                    <span>S/. {{ number_format($sale->seller_commission_amount ?? 0, 2) }}</span>
                </div>
                @endif
            </div>
        </div>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $voucherUrl }}" class="button">Ver Comprobante</a>
        </p>

        <p style="color: #666; font-size: 14px;">
            <strong>Nota:</strong> Puedes descargar el comprobante desde tu panel de ventas o haciendo clic en el botón superior.
        </p>
    </div>

    <div class="footer">
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        <p>
            <strong>Cambio J</strong><br>
            Sistema de Gestión de Divisas
        </p>
    </div>
</body>
</html>
