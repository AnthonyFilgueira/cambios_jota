# TEST-008 — Wallet y Comisiones del Vendedor

## Objetivo
Verificar que el sistema de comisiones funcione correctamente: acreditación automática al completar una transacción, cálculo según `seller_commission`, visibilidad en la vista de wallet, y liquidación de saldo.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/WalletController.php` (método: `index`)
- **Controlador:** `app/Http/Controllers/TransactionController.php` (método: `completeTransaction`)
- **Controlador:** `app/Http/Controllers/LiquidationController.php` (CRUD)
- **Vista:** `resources/views/wallet/index.blade.php`
- **Vista:** `resources/views/liquidations/index.blade.php`
- **Modelo:** `app/Models/WalletTransaction.php`
- **Modelo:** `app/Models/Seller.php` — campos `seller_commission`, `boss_commission`
- **Modelo:** `app/Models/Liquidation.php`
- **Rutas:**
  - `GET /wallet` → `wallet.index`
  - `GET /liquidations` → `liquidations.index`
  - `POST /liquidations` → `liquidations.store`
  - `POST /transactions/{id}/complete` → `transactions.complete`

## Estructura de comisiones

```
Transacción completada por X PEN:
├── seller_commission (5% default) → se acredita al wallet del vendedor
├── boss_commission (15% default) → comisión del administrador/dueño
└── incentivos adicionales (si aplican — ver REQ-9)
```

## Estado actual del wallet (BD local)

**Vendedor Pedro Martínez (VEND001):**
- `seller_commission = 5%`
- `boss_commission = 15%`
- Historial WalletTransaction:
  - ID 1: `+5.00` (commission) — Comisión base 5% — Transacción #1
  - ID 2: `+2.00` (commission) — Bono incentivo: Comisión Extra Mayo
  - ID 3: `-2.00` (liquidation) — Liquidación #1 - Transferencia Bancaria
- **Saldo después de liquidación:** 5.00 USD/unidad (balance_after del último movimiento)

## Casos de prueba

### Caso 1 — Acreditación de comisión al completar transacción
- **Precondición:** TX en `processing`, `seller_commission = 5%`, monto=100 PEN
- **Acción:** `POST /transactions/{id}/complete` (admin ejecuta)
- **Datos de entrada:** comprobante de pago (upload)
- **Respuesta esperada:**
  - `status = completed`
  - `WalletTransaction` creada: `type=commission`, `amount = 100 * 0.05 = 5.0`
  - `balance_after = saldo_anterior + 5.0`
- **Resultado real:** PASS ✅ (TX#1: 100 PEN × 5% = 5.00)

### Caso 2 — Ver wallet del vendedor
- **Precondición:** Autenticado como vendedor (pedro.martinez@cambiosj.com)
- **Acción:** `GET /wallet`
- **Respuesta esperada:** Lista de WalletTransactions con balance actual, historial de comisiones e incentivos
- **Resultado real:** PASS ✅

### Caso 3 — Acreditación de incentivo al completar transacción
- **Precondición:** Incentivo activo `Comisión Extra Mayo` (tipo: extra_comision, valor fijo=2)
- **Acción:** Completar transacción elegible
- **Respuesta esperada:** 2 registros en WalletTransaction: uno de commission + uno de incentivo
- **Resultado real:** PASS ✅ (WalletTransaction ID=2: bono incentivo 2.00)
- **Observaciones:** Ver `IncentiveService` y TEST-suite `IncentiveServiceTest` (13 tests, todos PASS)

### Caso 4 — Crear liquidación
- **Precondición:** Vendedor con saldo > 0
- **Acción:** `POST /liquidations` con `{ amount: 2.00, method: 'Transferencia Bancaria', notes: '...' }`
- **Respuesta esperada:** `Liquidation` creada, `WalletTransaction` de tipo `liquidation` con `amount=-2.00`
- **Resultado real:** PASS ✅ (TX ID=3: liquidación -2.00)

### Caso 5 — Balance no puede quedar negativo
- **Precondición:** Saldo = 5.00
- **Acción:** Intentar liquidar 10.00 (más del saldo disponible)
- **Respuesta esperada:** Validación rechaza la liquidación con error
- **Resultado real:** PASS ✅

### Caso 6 — Dashboard del dueño muestra totales de comisiones
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /owner-dashboard`
- **Respuesta esperada:** Totales de comisiones de todos los vendedores visibles
- **Resultado real:** PASS ✅

## Resultado global: PASS ✅

## Hallazgos y notas
- No existe un modelo `Wallet` separado — el balance se calcula como suma de `WalletTransaction.amount` o usando `balance_after` del último registro.
- El campo `seller_commission` en la tabla `sellers` guarda el porcentaje (ej: 5 = 5%). Era `commission_rate` en versiones anteriores — corrección aplicada (no existe ese campo).
- Incentivos: `IncentiveService` maneja la lógica completa. El test `IncentiveServiceTest` cubre 13 escenarios incluyendo bonos por tipo de cliente, por corredor, y por monto mínimo.
- `boss_commission` no genera un `WalletTransaction` — solo `seller_commission` e incentivos. La comisión del jefe es implícita en la diferencia.
