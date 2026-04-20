# Tarea 6.6: Matriz Interactiva de Corredores

**Estimación:** 5h  
**Estado:** 🔄 EN PROGRESO

---

## Objetivo

Implementar vista tipo matriz Excel para gestionar asignaciones de corredores a pares de forma visual y eficiente, con guardado AJAX en tiempo real.

---

## Entregables

1. **Controller:** `CorridorMatrixController.php`
   - Método index: mostrar matriz
   - Método toggle: actualizar estado vía AJAX
   
2. **Vista:** `corridor-matrix/index.blade.php`
   - Tabla tipo matriz con Alpine.js
   - Filas = pares de divisas
   - Columnas = corredores
   - Celdas = checkboxes interactivos
   
3. **Ruta:** `/corridor-matrix`
   - GET para mostrar
   - POST para toggle AJAX

4. **Características:**
   - Guardado automático al hacer click
   - Feedback visual (spinner, color)
   - Sin recargar página
   - Responsive

---

## Consideraciones

- Usar Alpine.js para interactividad
- AJAX con fetch API
- CSRF token en requests
- Loading states
- Error handling
- Diseño Cambio J
