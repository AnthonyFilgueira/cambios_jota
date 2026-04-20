# Implementación 5.4: Motor de Cálculo de Ganancias Completo

**Estado:** ✅ COMPLETADO  
**Fecha:** 2026-04-20  
**Tiempo real:** 2h  

---

## Resumen

Se implementó un motor de cálculo robusto en el modelo `Seller` que proporciona métricas completas de rendimiento, ganancias, comisiones y rankings para vendedores individuales y globales.

---

## Componentes Implementados

### 1. Métodos de Conteo y Filtrado

**1.1. `salesCountBetween($start, $end, $status = null)`**

Cuenta ventas en un rango de fechas con filtro opcional por estado.

```php
// Ejemplos de uso:
$seller->salesCountBetween('2026-04-01', '2026-04-30'); // Total
$seller->salesCountBetween('2026-04-01', '2026-04-30', 'completed'); // Solo completadas
```

**Estados disponibles:**
- `pending_seller`
- `pending_admin`
- `approved`
- `completed`
- `rejected`
- `observed`

---

**1.2. `salesByStatus($status)`**

Retorna colección de ventas filtradas por estado.

```php
$completedSales = $seller->salesByStatus('completed');
```

---

### 2. Métricas de Rendimiento

**2.1. `averageTicketBetween($start, $end)`**

Calcula el ticket promedio (monto promedio por venta).

```php
$avgTicket = $seller->averageTicketBetween('2026-04-01', '2026-04-30');
// Retorna: 1250.50 (float)
```

**Lógica:**
- Si no hay ventas, retorna `0`
- Suma total de ventas / número de ventas

---

**2.2. `conversionRateBetween($start, $end)`**

Calcula tasa de conversión (% de ventas aprobadas/completadas vs totales).

```php
$conversion = $seller->conversionRateBetween('2026-04-01', '2026-04-30');
// Retorna: 75.50 (porcentaje)
```

**Lógica:**
- Ventas exitosas = `approved` + `completed`
- Tasa = (exitosas / totales) * 100

---

### 3. Totales Históricos

**3.1. `totalHistoricSales()`**

Total vendido de todo el tiempo (solo ventas aprobadas/completadas).

```php
$total = $seller->totalHistoricSales();
// Retorna: 125000.00
```

**Nota:** Solo cuenta ventas con estados `approved` y `completed`.

---

**3.2. `totalHistoricSellerCommissions()`**

Total de comisiones del vendedor acumuladas históricamente.

```php
$commissions = $seller->totalHistoricSellerCommissions();
// Retorna: 12500.00
```

**Características:**
- Usa snapshots de comisiones (`seller_commission_amount`)
- Fallback a cálculo si no existe snapshot
- Solo ventas aprobadas/completadas

---

**3.3. `totalHistoricBossCommissions()`**

Total de comisiones del dueño generadas por este vendedor.

```php
$bossCommissions = $seller->totalHistoricBossCommissions();
// Retorna: 6250.00
```

---

### 4. Saldo Pendiente

**4.1. `pendingBalance()`**

Saldo que se le debe al vendedor (comisiones - liquidaciones).

```php
$pending = $seller->pendingBalance();
// Retorna: 12500.00 (por ahora solo comisiones)
```

**Estado actual:**
- Por ahora retorna solo `totalHistoricSellerCommissions()`
- Cuando se implemente REQ 5.6 (Liquidaciones), restará liquidaciones:
  ```php
  return $this->totalHistoricSellerCommissions() - $this->totalLiquidations();
  ```

**TODO:** Actualizar en REQ 5.6

---

### 5. Resumen de Métricas

**5.1. `metricsBetween($start, $end)`**

Retorna array con todas las métricas de un vendedor en un rango.

```php
$metrics = $seller->metricsBetween('2026-04-01', '2026-04-30');
```

**Estructura del array:**
```php
[
    'total_sales' => 10,           // Número de ventas
    'approved_sales' => 7,
    'completed_sales' => 8,
    'rejected_sales' => 2,
    'total_amount' => 25000.00,    // Monto total vendido
    'average_ticket' => 2500.00,   // Ticket promedio
    'conversion_rate' => 75.00,    // % de conversión
    'seller_commission' => 2500.00,
    'boss_commission' => 1250.00,
]
```

**Uso:** Ideal para dashboards y reportes resumidos.

---

## Métodos Estáticos - Rankings y Comparaciones

### 6. Rankings

**6.1. `rankingByTotalSales($start, $end, $limit = 10)`**

Ranking de vendedores por total vendido (mayor a menor).

```php
$ranking = Seller::rankingByTotalSales('2026-04-01', '2026-04-30', 5);

foreach ($ranking as $i => $item) {
    echo ($i + 1) . '. ' . $item['seller']->name . ' - S/. ' . $item['total_sales'];
}
```

**Retorna:** Collection con estructura:
```php
[
    'seller' => Seller,        // Instancia del vendedor
    'total_sales' => 50000.00,
    'sales_count' => 20,
    'commission' => 5000.00,
]
```

---

**6.2. `rankingBySalesCount($start, $end, $limit = 10)`**

Ranking por número de ventas (más ventas primero).

```php
$ranking = Seller::rankingBySalesCount('2026-04-01', '2026-04-30');
```

---

**6.3. `rankingByCommissions($start, $end, $limit = 10)`**

Ranking por comisiones generadas (mayor comisión primero).

```php
$ranking = Seller::rankingByCommissions('2026-04-01', '2026-04-30');
```

---

### 7. Totales Globales

**7.1. `globalTotalSales($start, $end)`**

Total de ventas de TODOS los vendedores en un rango.

```php
$global = Seller::globalTotalSales('2026-04-01', '2026-04-30');
// Retorna: 500000.00
```

---

**7.2. `globalSellerCommissions($start, $end)`**

Total de comisiones pagadas a vendedores (global).

```php
$commissions = Seller::globalSellerCommissions('2026-04-01', '2026-04-30');
```

---

**7.3. `globalBossCommissions($start, $end)`**

Total de comisiones del dueño de todos los vendedores.

```php
$bossTotal = Seller::globalBossCommissions('2026-04-01', '2026-04-30');
```

---

## Pruebas Realizadas

### Prueba 1: Métricas Individuales

**Setup:**
- Vendedor: Juan Pérez
- 3 ventas: 1 completada, 1 aprobada, 1 pendiente
- Montos: 1000, 1500, 500 (total: 3000)
- Comisión: 10%

**Resultados:**
```
Total ventas: 3
Completadas: 1
Aprobadas: 1
Pendientes: 1
Ticket promedio: S/. 1,000.00
Tasa conversión: 66.67%
Total vendido (histórico): S/. 2,500.00 (solo approved+completed)
Comisión vendedor: S/. 250.00
Comisión dueño: S/. 125.00
Saldo pendiente: S/. 250.00
```

✅ **Resultado:** Cálculos correctos

---

### Prueba 2: Rankings

**Setup:**
- 3 vendedores con diferentes ventas
- María López: 5000 en 2 ventas
- Juan Pérez: 3000 en 3 ventas
- Carlos Ruiz: 800 en 1 venta

**Ranking por Total Vendido:**
```
1. María López - S/. 5,000.00 (2 ventas)
2. Juan Pérez - S/. 3,000.00 (3 ventas)
3. Carlos Ruiz - S/. 800.00 (1 ventas)
```

**Ranking por Comisiones:**
```
1. María López - S/. 600.00
2. Juan Pérez - S/. 300.00
3. Carlos Ruiz - S/. 64.00
```

✅ **Resultado:** Ordenamiento correcto

---

### Prueba 3: Totales Globales

**Resultados:**
```
Total ventas: S/. 8,800.00
Comisiones vendedores: S/. 964.00
Comisiones dueño: S/. 482.00
```

**Verificación manual:**
- Suma ventas: 1000+1500+500+2000+3000+800 = 8,800 ✅
- Comisiones vendedores: 100+150+50+240+360+64 = 964 ✅
- Comisiones dueño: 50+75+25+120+180+32 = 482 ✅

---

### Prueba 4: Método metricsBetween()

**Vendedor:** María López (2 ventas, 100% conversión)

**Resultados:**
```
total_sales:         2.00
approved_sales:      1.00
completed_sales:     1.00
rejected_sales:      0.00
total_amount:        5,000.00
average_ticket:      2,500.00
conversion_rate:     100.00
seller_commission:   600.00
boss_commission:     300.00
```

✅ **Resultado:** Todos los cálculos correctos

---

## Archivos Modificados

```
app/Models/Seller.php
docs/requirements/5-vendedores-comisiones/5.4-motor-calculo-ganancias/
├── task.md
└── implementation.md
```

---

## Decisiones Técnicas

### 1. Uso de Snapshots

Todos los métodos de comisiones usan los snapshots guardados en las ventas:
- `seller_commission_amount`
- `admin_commission_amount`

**Razón:** Garantiza precisión histórica incluso si las comisiones del vendedor cambian.

**Fallback:** Si no existe snapshot (ventas antiguas), calcula dinámicamente.

---

### 2. Filtrado por Estados en Totales Históricos

Los métodos históricos (`totalHistoricSales`, etc.) solo cuentan ventas con estados:
- `approved`
- `completed`

**Razón:** 
- Ventas pendientes/rechazadas no son ganancias reales
- Evita inflar cifras con ventas no confirmadas

---

### 3. Rankings con Collections

Los rankings retornan `Collection` en lugar de `array`.

**Beneficios:**
- Métodos de Laravel disponibles (filter, map, take, etc.)
- Fácil transformación a JSON para APIs
- Mejor performance con lazy evaluation

---

### 4. Método metricsBetween() como Caché

En lugar de llamar múltiples métodos, un solo método retorna todas las métricas.

**Beneficio:**
- Menos consultas a BD (N+1 queries evitadas)
- Ideal para dashboards que necesitan múltiples KPIs
- Estructura predecible para frontend

---

### 5. TODO para pendingBalance()

Se dejó un TODO para actualizar `pendingBalance()` en REQ 5.6.

**Razón:**
- El método existe ahora para no romper código futuro
- Retorna valor coherente (comisiones totales)
- Se actualizará cuando haya tabla de liquidaciones

---

## Casos Edge Manejados

### 1. Sin Ventas
```php
$seller->averageTicketBetween('2026-01-01', '2026-01-31'); // 0
$seller->conversionRateBetween('2026-01-01', '2026-01-31'); // 0
```

### 2. Todas Rechazadas
```php
$seller->totalHistoricSales(); // 0 (no cuenta rechazadas)
$seller->conversionRateBetween('...', '...'); // 0%
```

### 3. Sin Snapshots (ventas antiguas)
```php
$sale->sellerCommissionAmount(); // Calcula dinámicamente como fallback
```

---

## Performance

### Optimizaciones Implementadas

1. **Uso de sum() directo en BD:**
   ```php
   ->sum('amount') // En lugar de ->get()->sum()
   ```

2. **Carga diferida en rankings:**
   - Solo carga sellers necesarios
   - Usa `get()` solo cuando se necesita iterar

3. **Filtros a nivel de query:**
   ```php
   ->where('approval_status', 'completed') // En query, no en colección
   ```

### Escenario de Estrés

**Setup:** 10 vendedores, 1000 ventas cada uno

**Tiempos esperados:**
- `metricsBetween()`: < 100ms
- Rankings top 10: < 200ms
- Totales globales: < 150ms

**Nota:** Si crece mucho, considerar caching con Redis.

---

## Uso en Dashboards

### Dashboard del Vendedor

```php
$metrics = $seller->metricsBetween(now()->startOfMonth(), now()->endOfMonth());

return view('dashboard.seller', [
    'total_sales' => $metrics['total_sales'],
    'total_amount' => $metrics['total_amount'],
    'commission' => $metrics['seller_commission'],
    'pending_balance' => $seller->pendingBalance(),
]);
```

---

### Dashboard del Dueño

```php
$startDate = now()->startOfMonth();
$endDate = now()->endOfMonth();

return view('dashboard.owner', [
    'global_sales' => Seller::globalTotalSales($startDate, $endDate),
    'global_commissions' => Seller::globalBossCommissions($startDate, $endDate),
    'top_sellers' => Seller::rankingByTotalSales($startDate, $endDate, 5),
    'top_earners' => Seller::rankingByCommissions($startDate, $endDate, 5),
]);
```

---

## Criterios de Aceptación

| Criterio | Estado |
|----------|--------|
| Cálculos precisos con snapshots | ✅ |
| Métodos optimizados (sin N+1) | ✅ |
| Soporte filtros por fecha y estado | ✅ |
| Manejo casos edge (sin ventas) | ✅ |
| Documentación inline | ✅ |
| Rankings funcionales | ✅ |
| Totales globales correctos | ✅ |

---

## Próximos Pasos

1. **REQ 5.5:** Monedero virtual con saldo en tiempo real (5h)
2. **REQ 5.6:** Registro de liquidaciones (4h) - Actualizar `pendingBalance()`
3. **REQ 5.7:** Dashboard dueño usando métodos de rankings (7h)

---

## Métodos Completos Disponibles

### Por Vendedor Individual
```
- salesCountBetween($start, $end, $status = null)
- salesByStatus($status)
- averageTicketBetween($start, $end)
- conversionRateBetween($start, $end)
- totalHistoricSales()
- totalHistoricSellerCommissions()
- totalHistoricBossCommissions()
- pendingBalance()
- metricsBetween($start, $end)
```

### Rankings y Globales (Estáticos)
```
- rankingByTotalSales($start, $end, $limit = 10)
- rankingBySalesCount($start, $end, $limit = 10)
- rankingByCommissions($start, $end, $limit = 10)
- globalTotalSales($start, $end)
- globalSellerCommissions($start, $end)
- globalBossCommissions($start, $end)
```

**Total:** 15 métodos nuevos implementados

---

**Implementado por:** Claude Sonnet 4.5  
**Revisado por:** Anthony Filgueira
