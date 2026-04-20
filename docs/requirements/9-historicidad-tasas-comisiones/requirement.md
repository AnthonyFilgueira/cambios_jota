# REQ 9: Historicidad de Tasas y Comisiones

**Estado:** ⏸️ PENDIENTE  
**Horas estimadas:** 8h  
**Prioridad:** ALTA (Bloquea integridad de datos históricos)  
**Fecha creación:** 2026-04-20

---

## Problema Identificado

### 🔴 Problema 1: Tasas de cambio editables

**Situación actual:**
- Admin puede EDITAR una tasa existente (`exchange_rates.update`)
- Las transacciones guardan `exchange_rate_id` (✅ correcto)
- PERO si admin edita la tasa, cambia el valor histórico en todas las transacciones que la usaron

**Ejemplo del problema:**
```
1. Tasa #1: ves_rate = 0.12 (activa)
2. Usuario hace 10 transacciones → guardan exchange_rate_id = 1
3. Admin EDITA Tasa #1 → ves_rate = 0.15
4. Las 10 transacciones ahora muestran ves_rate = 0.15 ❌
   (deberían seguir mostrando 0.12)
```

---

### 🔴 Problema 2: Comisiones de vendedores variables

**Situación actual:**
- Vendedor tiene `seller_commission = 5%` y `boss_commission = 2%`
- Ventas calculan comisión en tiempo real:
  ```php
  $sale->sellerCommissionAmount() 
  // = $sale->amount * ($sale->seller->seller_commission / 100)
  ```
- Si admin cambia comisión del vendedor a 10%, TODAS las ventas históricas muestran 10%

**Ejemplo del problema:**
```
1. Vendedor con seller_commission = 5%
2. Hace 20 ventas en enero
3. Admin cambia a seller_commission = 10% en febrero
4. Las 20 ventas de enero ahora muestran comisión de 10% ❌
   (deberían seguir mostrando 5%)
```

---

## Impacto

🚨 **CRÍTICO para:**
- Reportes financieros (comisiones históricas incorrectas)
- Liquidaciones de vendedores (pagos duplicados o incorrectos)
- Auditoría contable (valores históricos alterados)
- Reconciliación bancaria (montos de transacciones cambiantes)

---

## Solución Propuesta

### 1. Proteger tasas de cambio

#### Opción A: Impedir edición de tasas usadas
```php
// ExchangeRateController@update
if ($exchangeRate->transactions()->exists()) {
    return redirect()->back()->with('error', 
        'No se puede modificar una tasa con transacciones. Crea una nueva.');
}
```

#### Opción B: Snapshot en transacciones (adicional)
Agregar campos a `transactions`:
```php
$table->decimal('exchange_rate_snapshot', 10, 5);
// Valor exacto de ves_rate en el momento de la transacción
```

**Decisión:** Implementar AMBAS (A + B) para máxima seguridad.

---

### 2. Snapshot de comisiones en ventas

Agregar campos a `sales`:
```php
$table->decimal('seller_commission_percent', 5, 2);
$table->decimal('boss_commission_percent', 5, 2);
$table->decimal('seller_commission_amount', 10, 2);
$table->decimal('boss_commission_amount', 10, 2);
```

**Al crear venta:**
```php
Sale::create([
    'amount' => $request->amount,
    'seller_id' => $request->seller_id,
    'seller_commission_percent' => $seller->seller_commission,
    'boss_commission_percent' => $seller->boss_commission,
    'seller_commission_amount' => $amount * ($seller->seller_commission / 100),
    'boss_commission_amount' => $amount * ($seller->boss_commission / 100),
]);
```

**Modificar métodos del modelo Sale:**
```php
// Antes (incorrecto):
public function sellerCommissionAmount()
{
    return $this->amount * ($this->seller->seller_commission / 100);
}

// Después (correcto):
public function sellerCommissionAmount()
{
    return $this->seller_commission_amount; // Usar valor guardado
}
```

---

### 3. Facilitar creación de nuevas tasas

Agregar botón "Crear Nueva Tasa" que clone la tasa actual como base:
```blade
<a href="{{ route('exchange_rates.create', ['clone' => $rate->id]) }}" 
   class="btn-primary">
    <svg>...</svg>
    Nueva Tasa (basada en actual)
</a>
```

---

### 4. Seeder inicial Perú → Venezuela

```php
// database/seeders/ExchangeRateSeeder.php
ExchangeRate::create([
    'usd_rate' => 3.70,      // Tasa BCV USD/VES
    'eur_rate' => 4.10,      // Tasa BCV EUR/VES
    'ves_rate' => 0.1200,    // Tasa PEN → VES (ajustar según mercado)
    'is_active' => true,
]);
```

---

## Tareas de Implementación

| ID | Tarea | Tiempo | Prioridad |
|----|-------|--------|-----------|
| 9.1 | Migración: agregar campos snapshot a `sales` | 1h | Alta |
| 9.2 | Migración: agregar `exchange_rate_snapshot` a `transactions` | 30min | Alta |
| 9.3 | Modificar SaleController para guardar comisiones | 1h | Alta |
| 9.4 | Modificar métodos del modelo Sale (usar snapshots) | 1h | Alta |
| 9.5 | Validación: impedir editar/eliminar tasas usadas | 1h | Alta |
| 9.6 | Botón "Clonar tasa" en vista de tasas | 1h | Media |
| 9.7 | Crear ExchangeRateSeeder (Perú-Venezuela) | 30min | Alta |
| 9.8 | Testing: verificar historicidad | 1h | Media |
| 9.9 | Documentación y casos edge | 1h | Baja |

**Total estimado:** 8 horas

---

## Criterios de Aceptación

- [ ] No se pueden editar tasas que tienen transacciones asociadas
- [ ] No se pueden eliminar tasas que tienen transacciones asociadas
- [ ] Tabla `sales` guarda snapshot de comisiones en el momento de la venta
- [ ] Tabla `transactions` guarda snapshot de tasa de cambio usada
- [ ] Reportes de comisiones muestran valores históricos correctos
- [ ] Seeder crea tasa inicial Perú → Venezuela
- [ ] Botón "Crear nueva tasa" disponible en interfaz
- [ ] Cambiar comisión de vendedor NO afecta ventas pasadas

---

## Casos Edge a Considerar

1. **¿Qué pasa con tasas sin transacciones?**
   - Pueden editarse libremente
   
2. **¿Se puede desactivar una tasa usada?**
   - Sí, solo se protege edición/eliminación
   
3. **¿Qué pasa si necesito corregir una tasa?**
   - Crear nueva tasa + migrar transacciones manualmente (script admin)
   
4. **¿Cómo afecta a reportes existentes?**
   - Deben actualizarse para usar campos snapshot

---

## Notas Técnicas

### Relaciones afectadas:

```php
// Transaction.php
public function exchangeRate()
{
    return $this->belongsTo(ExchangeRate::class);
}

// Ahora también acceder a:
$transaction->exchange_rate_snapshot

// Sale.php  
public function seller()
{
    return $this->belongsTo(Seller::class);
}

// Ahora también acceder a:
$sale->seller_commission_percent
$sale->boss_commission_percent
```

---

## Próximo Paso

Este requerimiento debe implementarse **ANTES** de ir a producción, ya que afecta la integridad de datos financieros y contables.

**Sugerencia:** Implementar inmediatamente después de completar REQ 3, antes de acumular más transacciones y ventas en el sistema.

---

**Creado por:** Claude Sonnet 4.5  
**Solicitado por:** Usuario (Anthony)  
**Razón:** Proteger integridad de datos históricos para reportes financieros correctos
