# Tarea 3.4: Vista tablero de vendedor - Ventas Pendientes

**Estado:** ✅ COMPLETO  
**Tiempo estimado:** 3h

---

## Objetivo

Crear vista `sales/pending-seller.blade.php` que muestre únicamente las ventas con estado `pending_seller`, permitiendo al vendedor aprobar o rechazar con botones interactivos.

---

## Funcionalidades

### Filtrado
- Mostrar solo ventas con `approval_status = 'pending_seller'`
- Ordenar por fecha de venta (más recientes primero)
- Paginación de 20 registros

### Información mostrada
- ID de venta
- Monto
- Fecha de venta
- Nombre del vendedor asignado
- Estado (badge visual)
- Botones de acción: Aprobar / Rechazar

### Interactividad
- Botones con Alpine.js para confirmar acción
- Formularios POST a rutas `sales.approve` y `sales.reject`
- Mensajes flash de éxito/error

---

## Archivos a crear/modificar

- `resources/views/sales/pending-seller.blade.php` (nuevo)
- `app/Http/Controllers/SaleController.php` (agregar método `pendingSeller()`)
- `routes/web.php` (agregar ruta GET)
- `resources/views/layouts/navigation.blade.php` (agregar enlace)

---

## Criterios de aceptación

- [ ] Vista creada con layout app
- [ ] Solo muestra ventas pending_seller
- [ ] Botones Aprobar/Rechazar funcionales
- [ ] Confirmación antes de ejecutar acción
- [ ] Mensajes flash visibles
- [ ] Ruta agregada y accesible
- [ ] Enlace en navegación
