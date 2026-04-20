# Implementación 4.8: Sistema de Notificaciones Email

## Archivos creados

### 1. Evento: `app/Events/SaleCompleted.php`
```php
class SaleCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Sale $sale;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }
}
```

### 2. Listener: `app/Listeners/SendVoucherUploadedNotification.php`
```php
class SendVoucherUploadedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;

        if ($sale->seller && $sale->seller->email) {
            Mail::to($sale->seller->email)
                ->send(new VoucherUploadedMail($sale));
        }
    }
}
```

### 3. Mailable: `app/Mail/VoucherUploadedMail.php`
```php
class VoucherUploadedMail extends Mailable implements ShouldQueue
{
    public Sale $sale;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu transferencia ha sido completada - Cambio J',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.voucher-uploaded',
            with: [
                'sale' => $this->sale,
                'voucherUrl' => route('sales.showVoucher', $this->sale),
            ],
        );
    }
}
```

### 4. Vista Email: `resources/views/emails/voucher-uploaded.blade.php`
- HTML responsivo con paleta Cambio J
- Detalles de la venta (ID, fecha, monto, comisión)
- Botón para ver comprobante
- Footer con branding

---

## Modificaciones en archivos existentes

### app/Observers/SaleObserver.php
```php
if ($newStatus === 'completed') {
    event(new SaleCompleted($sale));
}
```

### app/Providers/AppServiceProvider.php
```php
Event::listen(
    SaleCompleted::class,
    SendVoucherUploadedNotification::class,
);
```

### app/Models/Sale.php
- Renombrado método `observe()` a `markAsObserved()` (conflicto con Model::observe())

### app/Http/Controllers/SaleController.php
- Actualizado para usar `markAsObserved()`

---

## Configuración

### .env (ya configurado)
```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
QUEUE_CONNECTION=database
```

### Mailpit
- URL: http://localhost:8025
- Captura todos los emails enviados
- No envía emails reales (solo desarrollo)

---

## Flujo de funcionamiento

1. Admin carga comprobante en venta aprobada
2. Método `complete()` cambia estado a `completed`
3. `SaleObserver` detecta cambio y dispara `SaleCompleted`
4. `SendVoucherUploadedNotification` se encola (ShouldQueue)
5. Queue worker procesa el job
6. Email se envía al vendedor via Mailpit
7. Log registra el envío

---

## Comandos para testing

### Procesar queue manualmente
```bash
./vendor/bin/sail artisan queue:work --once
```

### Ver emails en Mailpit
```
http://localhost:8025
```

### Ver logs
```bash
./vendor/bin/sail artisan tail
```

---

## Destinatario del email

**Actual:** Vendedor (`sale->seller->email`)

**Futuro (cuando se implemente REQ 2 completo):**
```php
// En el listener
$transaction = $sale->transaction;
$customer = $transaction->user;

Mail::to($customer->email)
    ->cc($sale->seller->email) // Copia al vendedor
    ->send(new VoucherUploadedMail($sale));
```

---

## Paleta Cambio J en email

- Header: Gradiente purple-700 to purple-600
- Detalles: Fondo morado claro (DDD6FE)
- Botón: Teal (14B8A6)
- Texto destacado: Morado profundo (5B21B6)

---

**Fecha:** 2026-04-20  
**Estado:** ✅ COMPLETO
