# Tarea 5.7: Dashboard del Dueño

**Horas estimadas:** 7h  
**Estado:** 🔄 EN PROGRESO

## Objetivo

Crear un dashboard completo para el dueño del negocio con métricas clave, rankings de vendedores y visualización de rendimiento del sistema.

## Alcance

### 1. Métricas Globales
- Total vendido (sistema completo)
- Total de comisiones (vendedores + dueño)
- Cantidad de ventas (total, aprobadas, pendientes)
- Conversión promedio
- Ticket promedio del sistema

### 2. Rankings de Vendedores
- Top vendedores por monto total
- Top vendedores por cantidad de ventas
- Top vendedores por comisiones generadas

### 3. Resumen de Liquidaciones
- Total liquidado en el período
- Saldo pendiente en monederos
- Liquidaciones por método de pago

### 4. Filtros
- Rango de fechas (hoy, semana, mes, año, custom)
- Vista comparativa vs. período anterior

### 5. Visualización
- Cards con métricas destacadas
- Tablas de rankings
- Uso de colores de la paleta Cambio J
- Diseño responsive con Tailwind

## Archivos a crear

- `app/Http/Controllers/OwnerDashboardController.php`
- `resources/views/owner-dashboard.blade.php`
- Ruta en `routes/web.php`

## Integración

Usar métodos existentes del modelo Seller (REQ 5.4):
- `Seller::globalTotalSales()`
- `Seller::globalSellerCommissions()`
- `Seller::globalBossCommissions()`
- `Seller::rankingByTotalSales()`
- `Seller::rankingBySalesCount()`
- `Seller::rankingByCommissions()`

## Criterios de aceptación

- ✅ Dashboard accesible solo para usuarios autenticados
- ✅ Métricas calculadas correctamente usando motor REQ 5.4
- ✅ Rankings actualizados en tiempo real
- ✅ Filtros funcionales por rango de fechas
- ✅ Diseño coherente con paleta Cambio J
- ✅ Responsive en móvil y desktop
