# Tarea 5.4: Motor de Cálculo de Ganancias Completo

**Estado:** 🔄 EN PROGRESO  
**Horas estimadas:** 7h  
**Prioridad:** ALTA

---

## Objetivo

Implementar un motor de cálculo robusto que proporcione métricas completas de rendimiento, ganancias y comisiones para vendedores y el dueño del negocio.

---

## Alcance

### 1. Métricas de Rendimiento (Seller model)
- Número de ventas por estado (pending, approved, completed, rejected)
- Ticket promedio por vendedor
- Tasa de conversión (aprobadas/totales)
- Ranking entre vendedores

### 2. Cálculos de Ganancias
- Comisión total del vendedor (histórico y por rango)
- Comisión total del dueño (histórico y por rango)
- Ganancia neta del vendedor (comisión - liquidaciones)
- Saldo pendiente de pago

### 3. Totales Acumulados
- Total vendido histórico
- Total vendido en rango de fechas
- Total de comisiones por vendedor (histórico)
- Total de comisiones del dueño (histórico)

### 4. Filtros y Agrupaciones
- Por rango de fechas
- Por estado de venta
- Por vendedor
- Totales globales

---

## Métodos a Implementar

### En Seller.php:
```
- salesCountBetween($start, $end, $status = null)
- averageTicketBetween($start, $end)
- conversionRateBetween($start, $end)
- totalHistoricSales()
- totalHistoricCommissions()
- pendingBalance()
- salesByStatus($status)
```

### En Sale.php (ya existen):
```
- sellerCommissionAmount() ✅
- bossCommissionAmount() ✅
```

---

## Criterios de Aceptación

- ✅ Cálculos precisos con snapshots de historicidad
- ✅ Métodos optimizados (sin N+1)
- ✅ Soporte para filtros por fecha y estado
- ✅ Manejo de casos edge (sin ventas, sin comisiones)
- ✅ Documentación inline de cada método

---

**Fecha creación:** 2026-04-20
