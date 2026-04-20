# Tarea 5.8: Reportes de Rendimiento y Ranking

**Horas estimadas:** 5h  
**Estado:** 🔄 EN PROGRESO

## Objetivo

Crear sistema de reportes detallados de rendimiento por vendedor con métricas completas, comparaciones temporales y rankings históricos.

## Alcance

### 1. Vista de Reporte Individual por Vendedor
- Selección de vendedor
- Rango de fechas configurable
- Métricas completas del vendedor
- Comparación con período anterior
- Gráficos de tendencia (opcional)

### 2. Métricas del Vendedor
- Total vendido
- Cantidad de ventas por estado
- Comisiones generadas (vendedor + dueño)
- Ticket promedio
- Tasa de conversión (aprobadas/totales)
- Saldo en monedero
- Total liquidado
- Historial de liquidaciones

### 3. Rankings Históricos
- Ranking por monto (posición del vendedor)
- Ranking por cantidad (posición del vendedor)
- Ranking por comisiones (posición del vendedor)
- Ver top 10 completo

### 4. Comparaciones
- vs Período anterior
- vs Promedio del sistema
- vs Top performer
- Tendencias (mejorando/estable/decreciendo)

### 5. Vista de Rankings Global
- Tabla completa de todos los vendedores
- Múltiples métricas en columnas
- Filtros por período
- Ordenamiento por columna
- Exportar (REQ 5.9)

## Archivos a crear

- `app/Http/Controllers/SellerReportController.php`
- `resources/views/reports/seller-performance.blade.php`
- `resources/views/reports/seller-rankings.blade.php`
- Rutas en `routes/web.php`

## Integración

Usar métodos existentes del modelo Seller:
- `metricsBetween()` - REQ 5.4
- `rankingByTotalSales()` - REQ 5.4
- `rankingBySalesCount()` - REQ 5.4
- `rankingByCommissions()` - REQ 5.4
- `walletBalance()` - REQ 5.5
- `liquidations()` - REQ 5.6

## Criterios de aceptación

- ✅ Reportes accesibles solo para admin/dueño
- ✅ Métricas calculadas usando motor REQ 5.4
- ✅ Filtros de fecha funcionales
- ✅ Comparaciones con período anterior
- ✅ Rankings actualizados en tiempo real
- ✅ Diseño coherente con paleta Cambio J
- ✅ Responsive
