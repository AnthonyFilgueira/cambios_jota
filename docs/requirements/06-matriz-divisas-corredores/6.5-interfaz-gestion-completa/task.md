# Tarea 6.5: Interfaz de Gestión Completa

**Estimación:** 6h  
**Estado:** 🔄 EN PROGRESO

---

## Objetivo

Implementar interfaz completa de gestión para pares de divisas con asignación de corredores, mejorando el CRUD existente de divisas.

---

## Entregables

1. **Controller:** `CurrencyPairController.php`
   - CRUD completo de pares
   - Asignación/desasignación de corredores
   - Toggle de estado
   
2. **Vistas de pares:**
   - `currency-pairs/index.blade.php` - Lista con filtros
   - `currency-pairs/create.blade.php` - Crear par
   - `currency-pairs/edit.blade.php` - Editar par + asignar corredores
   
3. **Mejoras a currencies:**
   - Mostrar pares asociados en index
   - Link rápido a crear par desde divisa

4. **Rutas:**
   - Resource routes para currency-pairs
   - Ruta custom para asignar corredores

---

## Consideraciones

- Validar que from_currency != to_currency
- Validar par único (no duplicar PEN→VES)
- Mostrar corredores disponibles con checkboxes
- Guardado de asignaciones en un solo submit
- Diseño Cambio J consistente
