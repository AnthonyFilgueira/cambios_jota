# Tarea 5.5: Monedero Virtual con Saldo en Tiempo Real

**Estado:** 🔄 EN PROGRESO  
**Horas estimadas:** 5h  
**Prioridad:** ALTA

---

## Objetivo

Implementar un sistema de monedero virtual para cada vendedor que registre automáticamente comisiones ganadas y liquidaciones recibidas, mostrando el saldo disponible en tiempo real.

---

## Alcance

### 1. Base de Datos

**Tabla: `wallet_transactions`**
- `id` - PK
- `seller_id` - FK a sellers
- `type` - enum: 'commission', 'liquidation', 'adjustment'
- `amount` - decimal (puede ser + o -)
- `balance_after` - decimal (saldo después de esta transacción)
- `description` - texto
- `reference_id` - int (nullable, ID de venta o liquidación)
- `reference_type` - string (nullable, Sale/Liquidation)
- `created_at` / `updated_at`

### 2. Modelo WalletTransaction

- Relación a Seller
- Relación polimórfica a reference (Sale/Liquidation)
- Scopes por tipo de transacción

### 3. Cálculo de Saldo

**En modelo Seller:**
- `walletBalance()` - saldo actual del monedero
- `addToWallet($amount, $type, $description, $reference = null)` - agregar fondos
- `deductFromWallet($amount, $type, $description, $reference = null)` - restar fondos
- `walletTransactions()` - relación a transacciones

### 4. Automatización

**Trigger automático al:**
- Aprobar venta → agregar comisión al monedero
- Completar venta → agregar comisión al monedero (si no se hizo antes)
- Crear liquidación → restar del monedero

### 5. Vistas

- Página "Mi Monedero" para vendedor
- Tabla de transacciones con filtros
- Saldo destacado visualmente
- Gráfico de evolución (opcional)

---

## Flujo de Transacciones

### Caso 1: Venta Aprobada
1. Admin aprueba venta
2. Sistema calcula comisión del vendedor
3. Crea transacción tipo "commission"
4. Actualiza saldo en monedero

### Caso 2: Liquidación
1. Dueño crea liquidación para vendedor
2. Sistema resta monto del monedero
3. Crea transacción tipo "liquidation"
4. Actualiza saldo

### Caso 3: Ajuste Manual
1. Admin hace ajuste (corrección)
2. Crea transacción tipo "adjustment"
3. Actualiza saldo

---

## Criterios de Aceptación

- ✅ Tabla wallet_transactions creada con campos necesarios
- ✅ Modelo WalletTransaction con relaciones
- ✅ Métodos de cálculo de saldo en Seller
- ✅ Automatización al aprobar ventas
- ✅ Vista "Mi Monedero" para vendedor
- ✅ Historial de transacciones visible
- ✅ Saldo siempre correcto y en tiempo real

---

## Notas Técnicas

### Integridad de Datos

- El campo `balance_after` registra el saldo después de cada transacción
- Permite auditoría y detección de inconsistencias
- No se puede eliminar transacciones (solo marcar como anuladas)

### Performance

- Índice en `seller_id` para consultas rápidas
- Índice en `created_at` para filtros por fecha
- Saldo actual = última transacción `balance_after`

---

**Fecha creación:** 2026-04-20
