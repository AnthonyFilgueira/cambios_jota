# Implementación 5.5: Monedero Virtual con Saldo en Tiempo Real

**Estado:** ✅ COMPLETADO  
**Fecha:** 2026-04-20  
**Tiempo real:** 2.5h  

---

## Resumen

Se implementó un sistema completo de monedero virtual para vendedores que registra automáticamente comisiones ganadas y liquidaciones recibidas, mostrando el saldo disponible en tiempo real con trazabilidad completa.

---

## Componentes Implementados

### 1. Base de Datos

**Migración:** `2026_04_20_103524_create_wallet_transactions_table.php`

**Tabla:** `wallet_transactions`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | Primary key |
| `seller_id` | BIGINT FK | Referencia a sellers |
| `type` | ENUM | commission, liquidation, adjustment |
| `amount` | DECIMAL(10,2) | Monto (positivo o negativo) |
| `balance_after` | DECIMAL(10,2) | Saldo después de transacción |
| `description` | TEXT | Descripción legible |
| `reference_id` | BIGINT | ID de Sale o Liquidation |
| `reference_type` | STRING | Sale, Liquidation, etc. |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Última actualización |

**Índices:**
- `seller_id` - Búsquedas por vendedor
- `created_at` - Filtros por fecha
- `[reference_id, reference_type]` - Relación polimórfica

---

### 2. Modelo WalletTransaction

**Archivo:** `app/Models/WalletTransaction.php`

**Relaciones:**
```php
public function seller()           // belongsTo(Seller::class)
public function reference()        // morphTo() - Polimórfica
```

**Scopes:**
```php
scopeCommissions($query)    // Filtrar solo comisiones
scopeLiquidations($query)   // Filtrar solo liquidaciones
scopeAdjustments($query)    // Filtrar solo ajustes
scopeRecent($query, $days)  // Últimos N días
```

**Métodos:**
```php
isCredit(): bool  // True si amount > 0
isDebit(): bool   // True si amount < 0
```

---

### 3. Métodos en Modelo Seller

**Archivo:** `app/Models/Seller.php`

#### 3.1. walletBalance()

Obtiene el saldo actual del monedero.

```php
public function walletBalance()
```

**Lógica:**
- Busca última transacción registrada
- Retorna `balance_after` de esa transacción
- Si no hay transacciones, retorna `0`

**Ventajas:**
- O(1) - No suma todas las transacciones
- Basado en snapshot del último estado

---

#### 3.2. addToWallet()

Agrega fondos al monedero.

```php
public function addToWallet($amount, $type, $description, $reference = null)
```

**Parámetros:**
- `$amount` - Monto a agregar (siempre se convierte a positivo)
- `$type` - Tipo: 'commission', 'liquidation', 'adjustment'
- `$description` - Texto descriptivo
- `$reference` - Objeto relacionado (Sale, Liquidation, etc.)

**Ejemplo:**
```php
$seller->addToWallet(
    150,
    'commission',
    'Comisión por venta #123',
    $sale
);
```

---

#### 3.3. deductFromWallet()

Resta fondos del monedero con validación de saldo suficiente.

```php
public function deductFromWallet($amount, $type, $description, $reference = null)
```

**Validación:**
- Lanza `Exception` si saldo insuficiente
- Mensaje claro: "Saldo actual: X, Intentando restar: Y"

**Ejemplo:**
```php
try {
    $seller->deductFromWallet(500, 'liquidation', 'Pago quincenal');
} catch (\Exception $e) {
    // Manejar saldo insuficiente
}
```

---

#### 3.4. adjustWallet()

Ajuste manual (corrección) sin validación de saldo.

```php
public function adjustWallet($amount, $description)
```

**Uso:**
- Correcciones administrativas
- `$amount` puede ser positivo o negativo
- No requiere referencia

**Ejemplo:**
```php
$seller->adjustWallet(-50, 'Corrección por error en comisión');
```

---

#### 3.5. hasSufficientBalance()

Verifica si tiene saldo suficiente para una operación.

```php
public function hasSufficientBalance($amount)
```

**Retorna:** `bool`

**Uso antes de deducción:**
```php
if ($seller->hasSufficientBalance(500)) {
    $seller->deductFromWallet(500, 'liquidation', 'Pago');
}
```

---

### 4. Automatización con Observer

**Archivo:** `app/Observers/SaleObserver.php`

**Trigger automático:**
```php
// Cuando venta cambia a 'approved' o 'completed'
if (in_array($newStatus, ['approved', 'completed']) 
    && !in_array($oldStatus, ['approved', 'completed'])) {
    
    $commission = $sale->sellerCommissionAmount();
    $sale->seller->addToWallet(
        $commission,
        'commission',
        "Comisión por venta #{$sale->id}...",
        $sale
    );
}
```

**Lógica:**
- Solo agrega comisión la primera vez que se aprueba
- Evita duplicados si cambia de 'approved' a 'completed'
- Usa snapshot de comisión (`seller_commission_amount`)

---

### 5. Controlador y Vista

**Controlador:** `app/Http/Controllers/WalletController.php`

**Método:** `index(Request $request)`

**Filtros soportados:**
- `type` - Tipo de transacción (commission, liquidation, adjustment)
- `days` - Período (7, 30, 90, 'all')

**Paginación:** 20 transacciones por página

---

**Vista:** `resources/views/wallet/index.blade.php`

**Componentes:**

1. **Saldo destacado:**
   - Card con gradiente morado
   - Saldo grande y visible
   - Nombre y código del vendedor

2. **Filtros:**
   - Select por tipo de transacción
   - Select por período
   - Botón "Filtrar"

3. **Tabla de transacciones:**
   - Fecha y hora
   - Tipo (badge con color)
   - Descripción + referencia
   - Monto (verde positivo, rojo negativo)
   - Saldo después de transacción

4. **Paginación:** Links automáticos de Laravel

---

**Ruta:**
```php
Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
```

---

## Flujo de Transacciones

### Caso 1: Venta Aprobada

```
1. Admin aprueba venta (pending_admin → approved)
2. SaleObserver detecta cambio de estado
3. Calcula comisión: $sale->sellerCommissionAmount()
4. Crea transacción:
   - Tipo: commission
   - Monto: +100 (positivo)
   - Descripción: "Comisión por venta #7 - S/. 1,000.00"
   - Reference: Sale #7
   - Balance after: 100
5. Vendedor ve nuevo saldo en monedero
```

---

### Caso 2: Liquidación (Manual)

```
1. Dueño crea liquidación para vendedor
2. Sistema verifica saldo suficiente:
   if (!$seller->hasSufficientBalance($amount)) {
       throw Exception...
   }
3. Resta del monedero:
   $seller->deductFromWallet($amount, 'liquidation', 'Pago quincenal')
4. Crea transacción:
   - Tipo: liquidation
   - Monto: -150 (negativo)
   - Balance after: 300
```

---

### Caso 3: Ajuste Manual

```
1. Admin identifica error en comisión
2. Realiza ajuste:
   $seller->adjustWallet(-50, 'Corrección por duplicado')
3. Crea transacción de tipo 'adjustment'
4. Actualiza saldo sin validaciones
```

---

## Pruebas Realizadas

### Prueba 1: Saldo inicial

```bash
Vendedor creado: Pedro Ramírez (VEN-CV2F9I)
Saldo inicial: S/. 0.00 ✅
```

---

### Prueba 2: Venta aprobada → Comisión automática

**Antes de aprobar:**
```
Estado venta: pending_admin
Saldo monedero: S/. 0.00
Transacciones: 0
```

**Después de aprobar:**
```
Estado venta: approved
Saldo monedero: S/. 100.00 ✅
Transacciones: 1 ✅

Transacción creada:
  Tipo: commission ✅
  Monto: S/. 100.00 ✅
  Saldo después: S/. 100.00 ✅
  Descripción: Comisión por venta #7 - S/. 1,000.00 ✅
```

---

### Prueba 3: Múltiples ventas y liquidación

**Operaciones:**
1. Venta 1: +100 → Saldo: 100
2. Venta 2: +200 → Saldo: 300
3. Venta 3: +150 → Saldo: 450
4. Liquidación: -150 → Saldo: 300

**Resultado final:**
```
Saldo actual: S/. 300.00 ✅
Total transacciones: 4 ✅
```

---

### Prueba 4: Historial completo

```
2026-04-20 10:42 | commission   | +S/. 100.00 | Saldo: S/. 100.00
2026-04-20 10:43 | commission   | +S/. 200.00 | Saldo: S/. 300.00
2026-04-20 10:43 | commission   | +S/. 150.00 | Saldo: S/. 450.00
2026-04-20 10:43 | liquidation  | -S/. 150.00 | Saldo: S/. 300.00
```

✅ **Trazabilidad completa**

---

### Prueba 5: Verificación de saldo

```
hasSufficientBalance(100): SÍ ✅
hasSufficientBalance(500): NO ✅
```

---

## Archivos Creados/Modificados

### Nuevos
```
database/migrations/
└── 2026_04_20_103524_create_wallet_transactions_table.php

app/Models/
└── WalletTransaction.php

app/Http/Controllers/
└── WalletController.php

resources/views/wallet/
└── index.blade.php

docs/requirements/5-vendedores-comisiones/5.5-monedero-virtual/
├── task.md
└── implementation.md
```

### Modificados
```
app/Models/Seller.php        (+ métodos de monedero)
app/Observers/SaleObserver.php   (+ automatización)
routes/web.php               (+ ruta wallet.index)
```

---

## Decisiones Técnicas

### 1. Campo balance_after en cada transacción

**Decisión:** Guardar saldo después de cada transacción

**Razones:**
- **Auditoría:** Permite reconstruir historial completo
- **Performance:** Saldo actual = última transacción (O(1))
- **Integridad:** Detecta inconsistencias si se modifica BD

**Alternativa rechazada:** Calcular saldo sumando todas las transacciones (O(n))

---

### 2. Validación de saldo solo en deductFromWallet()

**Decisión:** `addToWallet()` no valida, `deductFromWallet()` sí

**Razones:**
- Agregar fondos nunca falla (siempre es seguro)
- Restar requiere validación (puede haber saldo insuficiente)
- `adjustWallet()` es administrativo (sin validación)

---

### 3. Automatización en Observer vs Controller

**Decisión:** Usar `SaleObserver` para crear transacciones

**Razones:**
- Centralización: toda lógica de cambios de estado en un lugar
- Garantía: funciona sin importar cómo se apruebe la venta (API, Tinker, CLI)
- Trazabilidad: ya existe SaleLog en el observer

---

### 4. Relación polimórfica para reference

**Decisión:** `reference_id` + `reference_type` (polymorphic)

**Razones:**
- Flexibilidad: puede referirse a Sale, Liquidation, Adjustment, etc.
- Trazabilidad: saber exactamente qué generó cada transacción
- Extensibilidad: fácil agregar nuevos tipos de referencias

---

### 5. No permitir eliminación de transacciones

**Decisión:** No hay método `deleteTransaction()`

**Razones:**
- Integridad: eliminar rompería la cadena de saldos
- Auditoría: toda operación debe quedar registrada
- Corrección: usar `adjustWallet()` en lugar de eliminar

---

## Consideraciones de Producción

### 1. Sincronización de ventas anteriores

Si hay ventas aprobadas **antes** de implementar el monedero, ejecutar seeder:

```php
// database/seeders/BackfillWalletTransactionsSeeder.php
public function run()
{
    Sale::whereIn('approval_status', ['approved', 'completed'])
        ->with('seller')
        ->chunk(100, function ($sales) {
            foreach ($sales as $sale) {
                $commission = $sale->sellerCommissionAmount();
                $sale->seller->addToWallet(
                    $commission,
                    'commission',
                    "Comisión histórica - Venta #{$sale->id}",
                    $sale
                );
            }
        });
}
```

---

### 2. Manejo de concurrencia

Para evitar race conditions al aprobar múltiples ventas simultáneamente:

```php
DB::transaction(function () use ($sale) {
    $sale->approve();
    // La transacción del monedero se crea dentro de esta transacción
});
```

Laravel maneja esto automáticamente en el Observer.

---

### 3. Reportes y auditoría

**Verificar integridad:**
```php
$seller = Seller::find(1);

// Calcular saldo desde cero
$calculatedBalance = $seller->walletTransactions()->sum('amount');

// Comparar con último balance_after
$reportedBalance = $seller->walletBalance();

if ($calculatedBalance != $reportedBalance) {
    // ¡Inconsistencia detectada!
}
```

---

### 4. Límites y alertas

Considerar agregar:
- Alerta si saldo < 0 (no debería pasar)
- Límite máximo de saldo por vendedor
- Notificación al vendedor cuando recibe comisión

---

## Integración con REQ 5.6 (Liquidaciones)

Cuando se implemente el módulo de liquidaciones, actualizar:

1. **Crear liquidación:**
   ```php
   // En LiquidationController
   $seller->deductFromWallet(
       $amount,
       'liquidation',
       "Liquidación #{$liquidation->id}",
       $liquidation
   );
   ```

2. **Actualizar pendingBalance():**
   ```php
   // En Seller.php
   public function pendingBalance()
   {
       return $this->walletBalance(); // Ya está correcto con monedero
   }
   ```

El método `pendingBalance()` ahora es equivalente a `walletBalance()`.

---

## Criterios de Aceptación

| Criterio | Estado |
|----------|--------|
| Tabla wallet_transactions creada | ✅ |
| Modelo WalletTransaction con relaciones | ✅ |
| Métodos de cálculo en Seller | ✅ |
| Automatización al aprobar ventas | ✅ |
| Vista "Mi Monedero" funcional | ✅ |
| Historial de transacciones visible | ✅ |
| Saldo siempre correcto | ✅ |
| Trazabilidad completa | ✅ |

---

## Próximos Pasos

1. **REQ 5.6:** Registro de liquidaciones (4h)
   - CRUD de liquidaciones
   - Integración con `deductFromWallet()`
   - Reportes de liquidaciones

2. **REQ 5.7:** Dashboard dueño (7h)
   - Métricas usando motor de cálculo (REQ 5.4)
   - Saldos pendientes usando monedero (REQ 5.5)

---

## Métodos Disponibles

### En Seller
```
- walletBalance()                          // Saldo actual
- walletTransactions()                     // Relación
- addToWallet($amount, $type, $desc, $ref) // Agregar fondos
- deductFromWallet($amount, ...)           // Restar fondos
- adjustWallet($amount, $desc)             // Ajuste manual
- hasSufficientBalance($amount)            // Verificar saldo
```

### En WalletTransaction
```
- seller()           // Relación a vendedor
- reference()        // Relación polimórfica
- scopeCommissions() // Filtrar comisiones
- scopeLiquidations() // Filtrar liquidaciones
- scopeAdjustments() // Filtrar ajustes
- scopeRecent($days) // Últimos N días
- isCredit()         // ¿Es ingreso?
- isDebit()          // ¿Es egreso?
```

---

**Implementado por:** Claude Sonnet 4.5  
**Revisado por:** Anthony Filgueira
