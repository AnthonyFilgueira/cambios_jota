# Tarea 4.4: Vista "Ventas Observadas" (Vendedor)

**Módulo:** REQ 4 - Observaciones y Feedback  
**Estimado:** 5h (tarea 4.4 + 4.5 original)  
**Estado:** ✅ COMPLETO

---

## Objetivo

Crear vista dedicada donde el vendedor vea las ventas devueltas por el admin (estado `observed`), con la observación visible y botón para corregir.

---

## Componentes Implementados

### 1. Método en SaleController
- `observed()`: Filtra ventas con `approval_status = 'observed'`
- Ordenamiento: `latest('updated_at')` (más recientes primero)
- Paginación: 20 por página

### 2. Ruta
- `GET /sales-observed`
- Nombre: `sales.observed`

### 3. Vista `observed.blade.php`
- Header naranja (color de alerta)
- Tabla con columnas: ID, Fecha, Vendedor, Monto, Observación, Acciones
- Observación destacada en card naranja
- Botón "Corregir Venta" (morado) que lleva a edit

### 4. Métodos `edit()` y `update()` en SaleController
- Vista `edit.blade.php` que muestra observación si viene de estado `observed`
- Al guardar desde `observed`, cambia automáticamente a `pending_admin`
- Limpia `admin_observation` al corregir

---

## Flujo de corrección

1. Admin observa venta → estado cambia a `observed`
2. Vendedor ve venta en `/sales-observed`
3. Clic en "Corregir Venta"
4. Vista edit muestra observación del admin en header naranja
5. Vendedor corrige datos
6. Submit → estado cambia a `pending_admin` automáticamente
7. Redirección a `/sales-observed` con mensaje de éxito

---

## Paleta Cambio J aplicada

- Header tabla: `bg-gradient-to-r from-orange-600 to-orange-500`
- Hover filas: `hover:bg-orange-50`
- Card observación: `bg-orange-100 border-orange-500`
- Botón "Corregir": `bg-purple-600` (acción principal)

---

**Completado:** 2026-04-20
