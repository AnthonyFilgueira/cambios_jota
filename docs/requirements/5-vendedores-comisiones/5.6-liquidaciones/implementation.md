# Implementación 5.6: Registro de Liquidaciones

**Estado:** ✅ COMPLETADO  
**Fecha:** 2026-04-20  
**Tiempo real:** 2h  

---

## Resumen

Se implementó un sistema completo de registro de liquidaciones (pagos) a vendedores con integración al monedero virtual, validación de saldos y trazabilidad completa.

---

## Componentes Implementados

### 1. Base de Datos

**Migración:** `2026_04_20_110345_create_liquidations_table.php`

**Tabla:** `liquidations`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | Primary key |
| `seller_id` | BIGINT FK | Vendedor que recibe el pago |
| `amount` | DECIMAL(10,2) | Monto pagado |
| `payment_method` | ENUM | efectivo, transferencia, yape, plin, otro |
| `reference` | STRING | Número de operación/comprobante |
| `notes` | TEXT | Observaciones |
| `payment_date` | DATE | Fecha del pago |
| `created_by` | BIGINT FK | Usuario que registró |
| `created_at` | TIMESTAMP | Fecha de creación |

**Índices:**
- `seller_id`, `payment_date`, `created_by`

---

### 2. Modelo Liquidation

**Archivo:** `app/Models/Liquidation.php`

**Relaciones:**
```php
seller()            // belongsTo(Seller)
creator()           // belongsTo(User, 'created_by')
walletTransaction() // Transacción asociada
```

**Scopes:**
```php
recent($days)                  // Últimos N días
byPaymentMethod($method)       // Por método
byDateRange($start, $end)      // Por rango
```

**Métodos útiles:**
```php
paymentMethods()        // Static: array de métodos
paymentMethodLabel()    // Etiqueta legible
```

---

### 3. Controlador LiquidationController

**Archivo:** `app/Http/Controllers/LiquidationController.php`

**Métodos implementados:**

#### 3.1. index()
Lista de liquidaciones con filtros:
- Por vendedor
- Por método de pago
- Por rango de fechas
- Paginación (20 por página)

#### 3.2. create()
Formulario de nueva liquidación:
- Select de vendedores
- Saldo disponible (dinámico con Alpine.js)
- Métodos de pago

#### 3.3. store()
Guarda liquidación con validaciones:

```php
Validaciones:
- seller_id: required, exists
- amount: required, numeric, min:0.01
- amount: <= saldo disponible
- payment_method: required, in:lista válida
- payment_date: required, date, <= hoy
```

**Flujo:**
1. Validar datos
2. Verificar saldo suficiente
3. DB::transaction {
   - Crear liquidation
   - deductFromWallet() → crea wallet_transaction
}

#### 3.4. show()
Detalle completo de liquidación:
- Info del vendedor
- Datos de la liquidación
- Transacción de monedero asociada
- Saldo antes/después

#### 3.5. update()
Solo permite editar campos no críticos:
- `reference`
- `notes`
- `payment_date`

**No editable:** seller_id, amount, payment_method

#### 3.6. destroy()
**Bloqueado** por razones de auditoría.

Retorna error: "No se pueden eliminar liquidaciones por razones de auditoría"

---

### 4. Integración con Monedero

Al crear liquidación:

```php
DB::transaction(function () use ($validated, $seller) {
    // 1. Crear liquidación
    $liquidation = Liquidation::create([...$validated]);

    // 2. Restar del monedero
    $seller->deductFromWallet(
        $amount,
        'liquidation',
        "Liquidación #{$liquidation->id} - Método",
        $liquidation  // ← Referencia para trazabilidad
    );
});
```

**Trazabilidad:**
- Liquidation tiene `id`
- WalletTransaction tiene `reference_type = Liquidation` y `reference_id = liquidation.id`
- Método `walletTransaction()` encuentra la transacción

---

### 5. Vistas

#### 5.1. index.blade.php

**Componentes:**
- **Header:** Título + Botón "Nueva Liquidación"
- **Filtros:** Vendedor, método, fechas
- **Tabla:** Fecha, vendedor, monto (rojo), método (badge), referencia, acciones

**Features:**
- Paginación
- Filtros persistentes en URL
- Badge azul para método de pago
- Monto en rojo con signo negativo

---

#### 5.2. create.blade.php

**Componentes:**
- **Alpine.js:** Muestra saldo disponible al seleccionar vendedor
- **Formulario:** 6 campos
  1. Vendedor (select)
  2. Monto (input number)
  3. Método de pago (select)
  4. Referencia (input text)
  5. Fecha (date picker, max=hoy)
  6. Notas (textarea)

**Banner de saldo:**
```html
<div x-show="selectedSeller" class="bg-purple-50...">
    Saldo disponible: S/. XX.XX
</div>
```

**Validación cliente:**
- Monto > 0
- Fecha <= hoy

---

#### 5.3. show.blade.php

**Estructura:**

1. **Header morado:** Monto grande
2. **Detalles:**
   - Vendedor (nombre + código)
   - Fecha y método
   - Referencia (si existe)
   - Notas (si existe)
3. **Transacción de monedero:**
   - Saldo anterior
   - Saldo después
4. **Info de registro:**
   - Creado por
   - Fecha de registro

---

## Flujo Completo

### Crear Liquidación

```
1. Admin va a /liquidations
2. Click "Nueva Liquidación"
3. Selecciona vendedor → Alpine muestra saldo: S/. 500.00
4. Ingresa monto: 200
5. Selecciona método: Transferencia
6. Ingresa referencia: OP-123456
7. Fecha: 2026-04-20
8. Click "Registrar Liquidación"
9. Sistema valida:
   ✓ Vendedor existe
   ✓ Monto > 0
   ✓ 200 <= 500 (saldo suficiente)
   ✓ Método válido
   ✓ Fecha <= hoy
10. DB::transaction {
    - Crea liquidation (id=1)
    - deductFromWallet(200, ..., liquidation)
      → Crea wallet_transaction (type=liquidation, reference_id=1)
      → Actualiza balance_after = 300
}
11. Redirect: "Liquidación registrada. Saldo actualizado"
12. Nuevo saldo vendedor: S/. 300.00
```

---

## Validaciones Implementadas

### Backend (LiquidationController)

```php
'seller_id' => 'required|exists:sellers,id',
'amount' => 'required|numeric|min:0.01',
'payment_method' => 'required|in:efectivo,transferencia,yape,plin,otro',
'reference' => 'nullable|string|max:255',
'notes' => 'nullable|string',
'payment_date' => 'required|date|before_or_equal:today',
```

### Custom: Saldo Suficiente

```php
if (!$seller->hasSufficientBalance($amount)) {
    return back()->withErrors([
        'amount' => "Saldo insuficiente. Saldo actual: S/. X.XX"
    ]);
}
```

---

## Pruebas Realizadas

### Prueba 1: Crear liquidación exitosa

**Setup:**
- Vendedor con saldo: S/. 500.00
- Liquidación: S/. 200.00

**Resultado:**
```
Antes:  S/. 500.00
Después: S/. 300.00 ✅
Liquidaciones count: 1 ✅
```

---

### Prueba 2: Trazabilidad

**Verificación:**
```
Transacciones de monedero:
- commission   | +S/. 500.00 | Saldo: S/. 500.00
- liquidation  | -S/. 200.00 | Saldo: S/. 300.00 ✅

Liquidación #1 → wallet_transaction ID: 6 ✅
```

---

### Prueba 3: Método paymentMethodLabel()

```php
$liquidation->payment_method = 'transferencia';
$liquidation->paymentMethodLabel();
// "Transferencia Bancaria" ✅
```

---

## Archivos Creados/Modificados

### Nuevos
```
database/migrations/
└── 2026_04_20_110345_create_liquidations_table.php

app/Models/
└── Liquidation.php

app/Http/Controllers/
└── LiquidationController.php

resources/views/liquidations/
├── index.blade.php
├── create.blade.php
└── show.blade.php

docs/requirements/5-vendedores-comisiones/5.6-liquidaciones/
├── task.md
└── implementation.md
```

### Modificados
```
app/Models/Seller.php     (+ relación liquidations)
routes/web.php            (+ resource liquidations)
```

---

## Decisiones Técnicas

### 1. No permitir eliminación

**Decisión:** El método `destroy()` retorna error

**Razón:**
- Auditoría: cada pago debe quedar registrado
- Trazabilidad: eliminar rompería la cadena con wallet_transactions
- Solución: Si hay error, crear liquidación inversa (ajuste)

---

### 2. Solo editar campos no críticos

**Decisión:** `update()` solo permite editar reference, notes, payment_date

**Razón:**
- Cambiar `amount` rompería consistencia con monedero
- Cambiar `seller_id` generaría inconsistencias
- Campos editables son solo informativos

---

### 3. Validación de saldo en controller

**Decisión:** Validar `hasSufficientBalance()` antes de guardar

**Razón:**
- Evitar error en `deductFromWallet()`
- Mensaje de error más claro al usuario
- Prevenir transacciones fallidas

---

### 4. DB::transaction para atomicidad

**Decisión:** Usar transacción de BD al crear

**Razón:**
- Si falla deductFromWallet(), rollback de liquidation
- Garantiza consistencia: o se crea todo o nada
- Evita registros huérfanos

---

### 5. Alpine.js para saldo dinámico

**Decisión:** Mostrar saldo al seleccionar vendedor (sin AJAX)

**Razón:**
- UX: Usuario ve saldo inmediatamente
- Performance: No requiere request adicional
- Simple: Datos ya en blade con @js()

---

## Métodos de Pago Soportados

| Clave | Etiqueta |
|-------|----------|
| `efectivo` | Efectivo |
| `transferencia` | Transferencia Bancaria |
| `yape` | Yape |
| `plin` | Plin |
| `otro` | Otro |

**Extensible:** Agregar nuevos valores al enum en migración y método `paymentMethods()`

---

## Criterios de Aceptación

| Criterio | Estado |
|----------|--------|
| Tabla liquidations creada | ✅ |
| Modelo con relaciones | ✅ |
| CRUD completo | ✅ |
| Integración con monedero | ✅ |
| Validación de saldo | ✅ |
| Vistas con diseño Cambio J | ✅ |
| Filtros funcionales | ✅ |
| Trazabilidad completa | ✅ |

---

## Próximos Pasos

1. **REQ 5.7:** Dashboard del dueño (7h)
   - Usar liquidations para calcular pagos
   - Métricas con motor de cálculo
   - Gráficos de rendimiento

2. **Mejoras futuras (opcional):**
   - Exportar liquidaciones a PDF
   - Firma digital del vendedor
   - Notificación por email al vendedor

---

## Integración con REQ 5.5 (Monedero)

El método `pendingBalance()` en Seller ahora es correcto:

```php
public function pendingBalance()
{
    return $this->walletBalance(); // Ya incluye liquidaciones
}
```

**Explicación:**
- walletBalance() lee última transacción
- Esa transacción ya tiene descontadas las liquidaciones
- No es necesario restar manualmente

---

**Implementado por:** Claude Sonnet 4.5  
**Revisado por:** Anthony Filgueira
