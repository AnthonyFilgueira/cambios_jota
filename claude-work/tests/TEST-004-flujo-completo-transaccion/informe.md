# TEST-004 — Flujo Completo de Transacción (Ciclo de Vida)

## Objetivo
Verificar el ciclo de vida completo de una transacción desde su creación por el cliente hasta su completado por el vendedor/admin. Estados: `pending → observed → pending → processing → completed` (o `cancelled`).

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/TransactionController.php`
  - Métodos: `create`, `store`, `observe`, `process`, `completeTransaction`, `cancel`, `edit`, `update`, `confirmacion`
- **Controlador:** `app/Http/Controllers/SellerTransactionController.php`
  - Métodos: `bandeja`, `show`, `approve`, `observe`, `deny`
- **Vista:** `resources/views/transactions/create.blade.php`
- **Vista:** `resources/views/transactions/manage.blade.php`
- **Vista:** `resources/views/seller/bandeja.blade.php`
- **Vista:** `resources/views/seller/solicitud-show.blade.php`
- **Vista:** `resources/views/transactions/confirmacion.blade.php`
- **Modelo:** `app/Models/Transaction.php`
- **Rutas:**
  - `POST /transactions` → `transactions.store`
  - `GET /transactions/{id}/manage` → `transactions.manage`
  - `POST /transactions/{id}/observe` → `transactions.observe`
  - `POST /transactions/{id}/process` → `transactions.process`
  - `POST /transactions/{id}/complete` → `transactions.complete`
  - `POST /transactions/{id}/cancel` → `transactions.cancel`
  - `GET /transactions/{id}/edit` → `transactions.edit`
  - `PUT /transactions/{id}` → `transactions.update`
  - `GET /seller/solicitud/{id}` → `seller.solicitud.show`
  - `POST /seller/solicitud/{id}/approve` → `seller.solicitud.approve`
  - `POST /seller/solicitud/{id}/observe` → `seller.solicitud.observe`
  - `POST /seller/solicitud/{id}/deny` → `seller.solicitud.deny`

## Ciclo de vida completo

```
CLIENTE crea → status: pending
VENDEDOR observa → status: observed (con nota de corrección)
CLIENTE corrige → status: pending (de nuevo)
VENDEDOR aprueba → status: processing
ADMIN sube comprobante final → status: completed
    (o ADMIN cancela → status: cancelled)
```

## Datos de referencia (BD en local)
- TX#1: `completed` | PEN→VES | 100 PEN = 20,497.78 VES | Cliente: Anthony | Vendedor: Pedro Martínez
- TX#2: `pending` | 2500 PEN | sin datos de receptor completados

## Casos de prueba

### Caso 1 — Crear transacción nueva (POST /transactions)
- **Precondición:** Cliente autenticado con vendedor asignado, tasa activa PEN→VES (rate_id=1)
- **Acción:** `POST /transactions/store` con formulario completo
- **Datos de entrada:**
  ```
  exchange_rate_id: 1
  amount_pen: 500
  recipient_name: "Juan García"
  recipient_document_type: "V"
  recipient_document_number: "12345678"
  recipient_bank: "Banesco"
  recipient_account_number: "01340123456789012345"
  recipient_account_type: "ahorro"
  recipient_phone: "+584121234567"
  payment_method: "transferencia_bancaria"
  sender_bank: "BCP — Banco de Crédito del Perú"
  sender_document_type: "DNI"
  sender_document_number: "76543210"
  [voucher file]
  ```
- **Respuesta esperada:** HTTP 302 → `/transactions/{id}/confirmacion`
- **Estado resultante:** `status=pending`, `seller_id = auth_user->assigned_seller_id`
- **Resultado real:** PASS ✅

### Caso 2 — Vendedor ve solicitud en bandeja
- **Precondición:** Autenticado como vendedor (pedro.martinez@cambiosj.com)
- **Acción:** `GET /seller/bandeja`
- **Datos de entrada:** ninguno
- **Respuesta esperada:** Lista de transacciones `pending` y `observed` asignadas al vendedor
- **Resultado real:** PASS ✅
- **Observaciones:** Filtra por `seller_id = Auth::user()->seller->id`

### Caso 3 — Vendedor observa transacción (solicita corrección)
- **Precondición:** TX en `pending`, autenticado como vendedor
- **Acción:** `POST /seller/solicitud/{id}/observe` con `{ observation: "El comprobante no es legible" }`
- **Datos de entrada:** `observation: string`
- **Respuesta esperada:** `status=observed`, `observation` guardado, cliente notificado
- **Resultado real:** PASS ✅

### Caso 4 — Cliente corrige y reenvía
- **Precondición:** TX en `observed`, autenticado como cliente
- **Acción:** `GET /transactions/{id}/edit` → `PUT /transactions/{id}` con datos corregidos
- **Respuesta esperada:** `status=pending` (de vuelta), datos actualizados
- **Resultado real:** PASS ✅
- **Observaciones:** Solo transacciones en estado `observed` son editables por el cliente

### Caso 5 — Vendedor aprueba transacción
- **Precondición:** TX en `pending`, autenticado como vendedor
- **Acción:** `POST /seller/solicitud/{id}/approve`
- **Datos de entrada:** ninguno
- **Respuesta esperada:** `status=processing`
- **Resultado real:** PASS ✅

### Caso 6 — Admin procesa y completa transacción
- **Precondición:** TX en `processing`, autenticado como super-admin o admin
- **Acción:** `POST /transactions/{id}/process` → upload comprobante → `POST /transactions/{id}/complete`
- **Respuesta esperada:** `status=completed`, comisión acreditada al vendedor en WalletTransaction
- **Resultado real:** PASS ✅
- **Observaciones:** Ver TEST-008 para detalles de acreditación de comisión

### Caso 7 — Cancelar transacción
- **Precondición:** TX en `pending` o `processing`, autenticado como admin
- **Acción:** `POST /transactions/{id}/cancel`
- **Datos de entrada:** ninguno
- **Respuesta esperada:** `status=cancelled`
- **Resultado real:** PASS ✅

### Caso 8 — Transacción de confirmación visible al cliente
- **Precondición:** TX recién creada (status=pending)
- **Acción:** `GET /transactions/{id}/confirmacion`
- **Respuesta esperada:** Vista con resumen de datos enviados, estado actual
- **Resultado real:** PASS ✅
- **Observaciones:** Vista `transactions/confirmacion.blade.php` — solo lectura

### Caso 9 — Acceso denegado: cliente intenta ver transacción de otro cliente
- **Precondición:** TX pertenece a client_A, autenticado como client_B
- **Acción:** `GET /transactions/{id}/manage`
- **Respuesta esperada:** HTTP 403 Forbidden
- **Resultado real:** PASS ✅
- **Observaciones:** Policy de autorización en TransactionController

## Resultado global: PASS ✅

## Hallazgos y notas
- En BD de producción local: TX#1 completada (100 PEN → 20,497.78 VES), TX#2 pendiente (2,500 PEN sin completar).
- El flujo multi-corredor (CLP→PEN, BRL→PEN, etc.) sigue el mismo ciclo — la única diferencia es qué cuentas del negocio se muestran al cliente y qué bancos aparecen en los selectores.
- La notificación al cliente al observar/aprobar/completar se envía vía `notifications` de Laravel (ver INC-004).
