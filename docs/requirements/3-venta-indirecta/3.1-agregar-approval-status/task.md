# Tarea 3.1: Agregar campo approval_status a tabla sales

**Estado:** ✅ COMPLETO  
**Tiempo estimado:** 1h

---

## Objetivo

Añadir un campo `approval_status` tipo ENUM a la tabla `sales` para gestionar el workflow de aprobación multi-nivel.

---

## Estados definidos

1. `pending_seller` - Venta creada, esperando validación del vendedor
2. `pending_admin` - Vendedor aprobó, esperando validación del admin
3. `approved` - Administrador aprobó la transacción
4. `rejected` - Rechazada en cualquier etapa

**Default:** `pending_seller`

---

## Archivos a modificar

- `database/migrations/YYYY_MM_DD_HHMMSS_add_approval_status_to_sales_table.php` (nuevo)
- `app/Models/Sale.php` (actualizar fillable y casts)

---

## Criterios de aceptación

- [ ] Migración creada correctamente
- [ ] Campo `approval_status` de tipo enum
- [ ] Default = 'pending_seller'
- [ ] Migración ejecutada sin errores
- [ ] Modelo actualizado con el nuevo campo
