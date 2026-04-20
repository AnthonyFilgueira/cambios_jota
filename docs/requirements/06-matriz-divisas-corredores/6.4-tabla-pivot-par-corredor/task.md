# Tarea 6.4: Tabla Pivot Par-Corredor

**Estimación:** 5h  
**Estado:** 🔄 EN PROGRESO

---

## Objetivo

Implementar tabla pivot para relación many-to-many entre pares de divisas y corredores, permitiendo asignar qué corredores pueden procesar cada par.

---

## Entregables

1. **Migración:** `create_corridor_currency_pair_table.php`
   - Foreign keys a corridors y currency_pairs
   - Campo is_enabled
   - Unique constraint (corredor + par)
   - Cascade on delete
   
2. **Actualizar modelos:**
   - `Corridor::currencyPairs()` - relación belongsToMany
   - `CurrencyPair::corridors()` - relación belongsToMany
   - Métodos de utilidad
   
3. **Seeder:** `CorridorCurrencyPairSeeder.php`
   - Asignar corredores a pares existentes
   - Configuración inicial realista

---

## Consideraciones

- Unique constraint: un corredor solo puede estar asignado una vez por par
- Cascade delete: si se elimina par o corredor, eliminar relaciones
- is_enabled permite deshabilitar sin eliminar
- Seeder debe usar datos existentes (currencies, pairs, corridors)
