# INC-004 — Diagnóstico

## Causa raíz
Existen dos flujos que modifican el estado de una transacción pero usan mecanismos de
notificación distintos. El flujo del vendedor (`SellerTransactionController`) usa
`TransactionStatusChanged` que es database-only (sin email). Además, ningún evento
notifica a los usuarios con rol admin/super-admin.

## Mapa del estado actual

| Acción | Controller | Notificación enviada | Canal | Cliente email | Owner email |
|--------|-----------|---------------------|-------|---------------|-------------|
| Nueva transacción | TransactionController@store | NewTransactionForSeller | mail+db | ❌ | ❌ |
| Admin observa | TransactionController@observe | TransactionObserved | mail+db | ✅ | ❌ |
| Admin procesa | TransactionController@process | TransactionProcessed | mail+db | ✅ | ❌ |
| Admin completa | TransactionController@completeTransaction | TransactionCompleted | mail+db | ✅ | ❌ |
| Admin sube voucher | TransactionController@uploadFinalVoucher | TransactionStatusChanged | db only | ❌ | ❌ |
| Vendedor aprueba | SellerTransactionController@approve | TransactionStatusChanged | db only | ❌ | ❌ |
| Vendedor observa | SellerTransactionController@observe | TransactionStatusChanged | db only | ❌ | ❌ |
| Vendedor deniega | SellerTransactionController@deny | TransactionStatusChanged | db only | ❌ | ❌ |

## Archivos afectados

### 1. `app/Notifications/TransactionStatusChanged.php`
- `via()` retorna solo `['database']` — falta `'mail'`
- No tiene método `toMail()`

### 2. `app/Http/Controllers/SellerTransactionController.php`
- `approve()` (~línea 97): usa `TransactionStatusChanged` en lugar de `TransactionProcessed`
- `observe()` (~línea 132): usa `TransactionStatusChanged` en lugar de `TransactionObserved`
- `deny()` (~línea 167): usa `TransactionStatusChanged`, no existe `TransactionCancelled`

### 3. `app/Http/Controllers/TransactionController.php`
- Ningún método notifica a roles admin/super-admin

## Solución planificada
1. Añadir canal `'mail'` + método `toMail()` a `TransactionStatusChanged`
2. Crear `app/Notifications/NewTransactionForOwner.php` (mail+db, ShouldQueue)
3. Añadir método privado `notifyOwners(Transaction $t, $notification)` en `TransactionController`
   que busca `User::role(['admin', 'super-admin'])->get()` y notifica a cada uno
4. Actualizar `SellerTransactionController` para usar las notificaciones con email:
   - `approve()` → `TransactionProcessed` al cliente + `notifyOwners()`
   - `observe()` → `TransactionObserved` al cliente + `notifyOwners()`
   - `deny()` → `TransactionStatusChanged('cancelled')` al cliente + `notifyOwners()`
5. Aplicar `notifyOwners()` en todos los eventos de `TransactionController`
