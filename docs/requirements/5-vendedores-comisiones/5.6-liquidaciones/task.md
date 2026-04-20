# Tarea 5.6: Registro de Liquidaciones

**Estado:** 🔄 EN PROGRESO  
**Horas estimadas:** 4h  
**Prioridad:** ALTA

---

## Objetivo

Implementar un sistema de registro de liquidaciones (pagos) a vendedores que se integre con el monedero virtual, permitiendo al dueño/admin realizar pagos y mantener trazabilidad completa.

---

## Alcance

### 1. Base de Datos

**Tabla: `liquidations`**
- `id` - PK
- `seller_id` - FK a sellers
- `amount` - decimal (monto pagado)
- `payment_method` - enum: 'efectivo', 'transferencia', 'yape', 'plin', 'otro'
- `reference` - string (número de operación/comprobante)
- `notes` - text (observaciones)
- `payment_date` - date
- `created_by` - FK a users (quien registró)
- `created_at` / `updated_at`

### 2. Modelo Liquidation

- Relación a Seller
- Relación a User (creador)
- Relación polimórfica inversa con WalletTransaction

### 3. CRUD de Liquidaciones

**Controlador:** `LiquidationController`

**Rutas:**
- GET `/liquidations` - Index (lista de liquidaciones)
- GET `/liquidations/create` - Formulario de creación
- POST `/liquidations` - Guardar liquidación
- GET `/liquidations/{id}` - Ver detalle
- GET `/liquidations/{id}/edit` - Editar
- PUT `/liquidations/{id}` - Actualizar
- DELETE `/liquidations/{id}` - Eliminar

### 4. Integración con Monedero

Al crear liquidación:
1. Validar que vendedor tenga saldo suficiente
2. Restar monto del monedero (`deductFromWallet()`)
3. Crear transacción de tipo `liquidation`
4. Guardar liquidación en tabla

### 5. Vistas

**Index:**
- Tabla de liquidaciones con filtros
- Filtro por vendedor, fecha, método de pago
- Total liquidado por vendedor
- Botón "Nueva Liquidación"

**Create/Edit:**
- Select vendedor (con saldo actual)
- Input monto (validación <= saldo)
- Select método de pago
- Input referencia/comprobante
- Textarea notas
- Date picker fecha de pago

**Show:**
- Detalles completos de la liquidación
- Info del vendedor
- Link a transacción de monedero
- Botón imprimir/PDF (opcional)

---

## Flujo de Liquidación

```
1. Admin/Dueño va a "Liquidaciones"
2. Click "Nueva Liquidación"
3. Selecciona vendedor → Se muestra saldo disponible
4. Ingresa monto (validado <= saldo)
5. Selecciona método de pago
6. Ingresa referencia (ej: "OP-123456")
7. Guarda
8. Sistema:
   - Crea registro en liquidations
   - Resta del monedero (deductFromWallet)
   - Crea wallet_transaction
9. Éxito: "Liquidación registrada. Saldo actualizado"
```

---

## Validaciones

- ✅ Vendedor existe
- ✅ Monto > 0
- ✅ Monto <= saldo disponible del vendedor
- ✅ Método de pago válido
- ✅ Fecha de pago no futura
- ✅ Usuario autenticado

---

## Criterios de Aceptación

- ✅ Tabla liquidations creada
- ✅ Modelo Liquidation con relaciones
- ✅ CRUD completo funcional
- ✅ Integración con monedero (resta automática)
- ✅ Validación de saldo suficiente
- ✅ Vistas con diseño Cambio J
- ✅ Filtros funcionales en index
- ✅ Trazabilidad: liquidation → wallet_transaction

---

**Fecha creación:** 2026-04-20
