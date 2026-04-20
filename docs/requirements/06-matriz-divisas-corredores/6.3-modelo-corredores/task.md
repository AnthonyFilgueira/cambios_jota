# Tarea 6.3: Modelo de Corredores

**Estimación:** 6h  
**Estado:** 🔄 EN PROGRESO

---

## Objetivo

Implementar el modelo de corredores de envío (empresas que procesan las conversiones de divisas) con CRUD básico.

---

## Entregables

1. **Migración:** `create_corridors_table.php`
   - Campos: name, description, logo_url, is_active
   
2. **Modelo:** `Corridor.php`
   - Scopes (active, inactive)
   - Métodos de utilidad
   
3. **Seeder:** `CorridorSeeder.php`
   - Western Union
   - MoneyGram
   - Remitly
   - Wise
   - Cambio J Directo
   
4. **Controller:** `CorridorController.php`
   - CRUD completo
   - Método toggleStatus
   
5. **Vistas:**
   - `corridors/index.blade.php`
   - `corridors/create.blade.php`
   - `corridors/edit.blade.php`

---

## Consideraciones

- Usar diseño Cambio J (morados + Tailwind)
- Validar nombre único
- No eliminar, solo desactivar
- Logo opcional por ahora
