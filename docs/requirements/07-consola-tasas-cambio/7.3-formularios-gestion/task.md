# Tarea 7.3: Simplificación del Modelo de Tasas

**Estado:** 🔄 EN PROGRESO  
**Horas estimadas:** 2h  
**Prioridad:** ALTA

---

## Descripción

Simplificar el modelo de tasas eliminando campos de margen que no se usan, manteniendo solo la estructura de pares con tasas directas.

---

## Cambios a Realizar

### 1. Eliminar campos no utilizados:
- `base_rate`
- `margin_type`
- `margin_value`
- `final_rate`
- `last_updated_by`
- `notes`

### 2. Mantener estructura simple:
- `currency_pair_id` → FK a currency_pairs
- `ves_rate` → Tasa específica del par hacia VES
- `usd_rate` → Referencia BCV USD→VES
- `eur_rate` → Referencia BCV EUR→VES
- `is_active` → Estado

### 3. Actualizar:
- Modelo ExchangeRate
- Seeders
- Vistas (simplificar)

---

**Fecha creación:** 2026-04-20
