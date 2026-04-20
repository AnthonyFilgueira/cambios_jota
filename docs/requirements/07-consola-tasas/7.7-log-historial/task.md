# Tarea 7.7: Log de Historial de Cambios

**Duración estimada:** 5h  
**Estado:** En progreso  

## Objetivo

Implementar un sistema de auditoría completo para registrar todos los cambios en las tasas de cambio, permitiendo:
1. Trazabilidad total de modificaciones
2. Saber quién cambió qué y cuándo
3. Ver valores anteriores vs. nuevos
4. Cumplir con requisitos de auditoría

## Alcance

### 1. Tabla de historial (2h)
- Migración `exchange_rate_history`
- Modelo `ExchangeRateHistory`
- Relación con `ExchangeRate` y `User`

### 2. Registro automático (2h)
- Observer para ExchangeRate
- Eventos: created, updated, activated, deactivated
- Guardar valores anteriores y nuevos

### 3. Vista de historial (1h)
- Tabla en vista `edit` o modal
- Timeline de cambios
- Filtros por fecha/usuario

## Estructura de datos

```sql
exchange_rate_history:
- id
- exchange_rate_id (FK)
- user_id (FK, nullable)
- action (created, updated, activated, deactivated)
- old_values (JSON)
- new_values (JSON)
- ip_address
- user_agent
- created_at
```

## Resultado esperado

Al editar una tasa, se registra automáticamente:
```json
{
  "action": "updated",
  "user": "Admin",
  "changes": {
    "ves_rate": {"from": 173.71, "to": 175.00},
    "usd_rate": {"from": 479.78, "to": 480.00}
  },
  "timestamp": "2026-04-20 18:30:00"
}
```
