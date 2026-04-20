# Tarea 3.5: Vista cola de aprobación para admin

**Estado:** ✅ COMPLETO  
**Tiempo estimado:** 3h

---

## Objetivo

Crear vista `sales/pending-admin.blade.php` que muestre únicamente las ventas con estado `pending_admin`, permitiendo al administrador aprobar (estado final) o rechazar.

---

## Funcionalidades

### Filtrado
- Mostrar solo ventas con `approval_status = 'pending_admin'`
- Ordenar por fecha de venta (más antiguas primero = FIFO)
- Paginación de 20 registros

### Información mostrada
- ID de venta
- Fecha de venta
- Vendedor asignado
- Monto
- Estado (badge visual)
- Botones de acción: Aprobar Final / Rechazar

### Interactividad
- Botones con Alpine.js para confirmar acción
- Formularios POST a rutas `sales.approve` y `sales.reject`
- Mensajes flash de éxito/error
- Al aprobar: mensaje indica que es aprobación FINAL

---

## Archivos a crear/modificar

- `resources/views/sales/pending-admin.blade.php` (nuevo)
- `app/Http/Controllers/SaleController.php` (agregar método `pendingAdmin()`)
- `routes/web.php` (agregar ruta GET)

---

## Criterios de aceptación

- [ ] Vista creada con layout app
- [ ] Solo muestra ventas pending_admin
- [ ] Ordenamiento FIFO (oldest first)
- [ ] Botones Aprobar/Rechazar funcionales
- [ ] Confirmación antes de ejecutar acción
- [ ] Mensajes flash visibles
- [ ] Ruta agregada y accesible
- [ ] Paleta Cambio J aplicada
