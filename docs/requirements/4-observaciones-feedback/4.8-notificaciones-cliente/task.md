# Tarea 4.8: Sistema de Notificaciones al Cliente

**Módulo:** REQ 4 - Observaciones y Feedback  
**Estimado:** 4h (tarea 4.6 original)  
**Estado:** ⏸️ PENDIENTE (Requiere REQ 2 completo)

---

## Objetivo

Notificar automáticamente al cliente cuando su venta pasa a estado `completed` (comprobante cargado).

---

## Dependencias

Esta tarea requiere:
- ✅ REQ 2: Sistema de transacciones (parcialmente completo)
- ⏸️ REQ 2: Vincular transacciones con ventas (pendiente)
- ⏸️ Configuración de email en Laravel (SMTP, Mailgun, etc.)

**Razón del bloqueo:**  
Actualmente no hay forma de relacionar una `sale` con un `user` cliente. Necesitamos primero implementar la vinculación `transactions ↔ sales` del REQ 2.

---

## Diseño propuesto

### 1. Listener de evento
```php
// app/Listeners/SendVoucherUploadedNotification.php
class SendVoucherUploadedNotification
{
    public function handle(SaleCompleted $event)
    {
        $sale = $event->sale;
        $transaction = $sale->transaction; // Requiere relación pendiente
        $user = $transaction->user;

        Mail::to($user->email)->queue(
            new VoucherUploadedMail($sale, $user)
        );
    }
}
```

### 2. Evento
```php
// app/Events/SaleCompleted.php
class SaleCompleted
{
    public function __construct(public Sale $sale) {}
}
```

### 3. Mail
```php
// app/Mail/VoucherUploadedMail.php
class VoucherUploadedMail extends Mailable
{
    public function build()
    {
        return $this->subject('Tu transferencia ha sido completada')
            ->view('emails.voucher-uploaded')
            ->with([
                'sale' => $this->sale,
                'voucher_url' => route('sales.showVoucher', $this->sale),
            ]);
    }
}
```

### 4. Trigger en SaleObserver
```php
// Cuando sale pasa a 'completed'
if ($newStatus === 'completed') {
    event(new SaleCompleted($sale));
}
```

---

## Alternativas implementables ahora

### Opción A: Log en base de datos
- Crear tabla `notifications` (si no existe con Breeze)
- Guardar notificación cuando venta se completa
- Usuario ve notificaciones en su panel

### Opción B: Notificación en sistema
- Reutilizar componente `<x-notifications />` (REQ 3.6)
- Mostrar mensaje al cliente cuando entre al sistema
- Badge "Tienes X ventas completadas"

---

## Notas de implementación futura

**Cuando se implemente:**
1. Configurar `.env` con credenciales SMTP/Mailgun
2. Configurar queue driver (redis, database, etc.)
3. Crear evento `SaleCompleted`
4. Crear listener y mail
5. Registrar en `EventServiceProvider`
6. Disparar evento desde `SaleObserver`

**Email debe incluir:**
- Saludo personalizado
- Monto de la transferencia
- Fecha de procesamiento
- Enlace directo al comprobante
- Información de contacto

---

**Estado:** Documentado, pendiente de implementación (bloqueado por REQ 2)  
**Fecha:** 2026-04-20
