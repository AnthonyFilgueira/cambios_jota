# Tarea 7.2: Vista Unificada de Pares Activos

**Estado:** 🔄 EN PROGRESO  
**Horas estimadas:** 5h  
**Prioridad:** ALTA

---

## Descripción

Crear interfaz administrativa para visualizar y gestionar tasas de cambio de todos los pares activos, con filtros y búsqueda.

---

## Entregables

1. **Controlador:** `ExchangeRateController` con método `index()`
2. **Rutas:** Web routes para listado
3. **Vista:** `exchange-rates/index.blade.php`
4. **Funcionalidades:**
   - Listado de pares activos con tasas
   - Filtros por divisa origen/destino
   - Búsqueda rápida
   - Indicadores visuales (última actualización)
   - Botones de acción (editar, historial)

---

## Estructura de Vista

Tabla con columnas:
- Par (PEN → VES)
- Tasa Base
- Margen (tipo + valor)
- Tasa Final
- Ganancia Estimada
- Última Actualización
- Acciones

---

**Fecha creación:** 2026-04-20
