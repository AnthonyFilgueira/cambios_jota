# Tarea 5.2: Código Único Alfanumérico por Vendedor

**Estado:** ⏸️ PENDIENTE  
**Horas estimadas:** 4h  
**Prioridad:** ALTA

---

## Objetivo

Generar automáticamente un código único alfanumérico para cada vendedor al momento de su creación. Este código servirá como identificador legible y único para trazabilidad en ventas, reportes y liquidaciones.

---

## Alcance

1. **Base de datos:**
   - Agregar campo `code` (string, unique, nullable=false)
   - Index único en `code`

2. **Generación automática:**
   - Formato: `VEN-XXXXXX` (VEN + 6 caracteres alfanuméricos)
   - Generación en `boot()` del modelo
   - Validación de unicidad

3. **Vistas:**
   - Mostrar código en index de vendedores
   - Mostrar código en formularios (readonly)
   - Badge visual distintivo

4. **API/Controlador:**
   - El código NO es editable por usuario
   - Se genera automáticamente al crear

---

## Criterios de Aceptación

- ✅ Cada vendedor tiene un código único al crearse
- ✅ Formato consistente: VEN-XXXXXX
- ✅ No se permiten códigos duplicados
- ✅ El código es visible en todas las vistas
- ✅ El código NO es editable manualmente

---

**Fecha creación:** 2026-04-20
