# Tarea 12.1: Mejorar Vista de Vendedores

**Estado:** ⏸️ EN PROGRESO  
**Tiempo estimado:** 2h  
**Prioridad:** ALTA

---

## Objetivo

Mejorar la vista `sellers/index.blade.php` para mostrar métricas completas de cada vendedor usando los métodos ya disponibles en el modelo `Seller.php`.

---

## Problema Actual

Vista muy básica que solo muestra:
- Tabla con código, nombre, comisiones
- Botones editar/eliminar

**NO muestra:**
- Saldo del monedero
- Total de ventas
- Cantidad de ventas
- Comisiones ganadas
- Ticket promedio
- Links a monedero y reportes

---

## Solución

Transformar tabla básica en cards con:

1. **Header del vendedor:**
   - Nombre destacado
   - Código con badge
   - Botones: Ver Monedero | Ver Reportes | Editar

2. **Cards de métricas (5):**
   - Saldo Monedero → `$seller->walletBalance()`
   - Total Vendido → `$seller->totalSales()`
   - Cantidad Ventas → `$seller->salesCount()`
   - Comisiones Ganadas → `$seller->totalCommissionsEarned()`
   - Ticket Promedio → `$seller->averageTicket()`

3. **Footer del vendedor:**
   - Configuración de comisiones (vendedor % y jefe %)

4. **Diseño:**
   - Paleta Cambio J (morado, turquesa, rosa)
   - Glassmorphism en cards
   - Responsive (mobile + desktop)

---

## Archivos a Modificar

- `resources/views/sellers/index.blade.php`

---

## Métodos Disponibles (Seller.php)

```php
$seller->walletBalance()           // Saldo actual del monedero
$seller->totalSales()              // Total vendido en PEN
$seller->salesCount()              // Cantidad de ventas
$seller->totalCommissionsEarned()  // Comisiones totales ganadas
$seller->averageTicket()           // Ticket promedio por venta
```

---

## Criterios de Aceptación

- [x] Vista muestra 5 métricas por vendedor
- [x] Botón "Ver Monedero" linkea a `/wallet`
- [x] Botón "Ver Reportes" linkea a `/reports/sellers/{id}/performance`
- [x] Paleta Cambio J aplicada (morado, turquesa, rosa)
- [x] Responsive design
- [x] Mantiene botón "Editar" y "Eliminar"

---

**Creado:** 2026-05-02  
**Asignado a:** Claude Sonnet 4.5
