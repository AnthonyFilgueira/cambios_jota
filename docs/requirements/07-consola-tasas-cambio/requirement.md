# REQ 7: Consola de Gestión de Tasas y Margen de Utilidad

**Estado:** 🔄 EN PROGRESO (0% completado)  
**Horas estimadas:** 37h  
**Horas completadas:** 0h  
**Horas restantes:** 37h  
**Prioridad:** ALTA

---

## Descripción General

Sistema centralizado para gestionar tasas de cambio por par de divisas, configurar márgenes de utilidad y visualizar ganancias en tiempo real. Integra con el simulador público para actualización instantánea y mantiene historial de cambios.

---

## Objetivo

Permitir al administrador:
- Gestionar tasas base por par de divisas
- Configurar márgenes de ganancia (global o por par)
- Visualizar ganancia estimada por operación
- Actualizar tasas del simulador en tiempo real
- Mantener historial de cambios de tasas

---

## Alcance

### 1. Vista Unificada de Tasas

**Listado de pares activos:**
- Solo pares habilitados (REQ 6)
- Tasa base actual
- Margen configurado
- Tasa final al cliente
- Última actualización

**Funcionalidades:**
- Filtros por divisa origen/destino
- Búsqueda rápida
- Ordenamiento por columnas
- Indicadores visuales (cambios recientes)

### 2. Gestión de Tasa Base

**Por cada par:**
- Input de tasa base (del mercado)
- Fuente de la tasa (opcional)
- Fecha/hora de última actualización
- Validaciones de rangos razonables

**Actualizaciones:**
- Manual (input directo)
- Importación CSV (opcional)
- API externa (futuro)

### 3. Configuración de Márgenes

**Tipos de margen:**
- **Global:** Un % para todos los pares
- **Por par:** % específico por cada par
- **Monto fijo:** Agregar cantidad fija

**Opciones:**
- Margen en porcentaje (ej: 3%)
- Margen en puntos (ej: +0.05)
- Prioridad: par > global

### 4. Cálculo de Tasa Final

**Fórmula:**
```
Tasa Cliente = Tasa Base + (Tasa Base × Margen%)
```

**Ejemplo:**
```
Tasa Base PEN→VES: 0.15
Margen: 3%
Tasa Cliente: 0.15 + (0.15 × 0.03) = 0.1545
```

**Cálculo inverso:**
```
Si cliente envía 1000 PEN:
- A tasa base: recibe 150 VES
- A tasa cliente: recibe 154.5 VES
- Ganancia Cambio J: 4.5 VES
```

### 5. Visualizador de Ganancias

**Por operación simulada:**
- Input: monto que enviaría cliente
- Output: ganancia estimada en PEN/VES/USD
- Porcentaje de ganancia

**Dashboard de tasas:**
- Total operaciones hoy con esta tasa
- Ganancia acumulada estimada
- Comparación vs tasa anterior

### 6. Integración con Simulador

**Actualización en tiempo real:**
- Al cambiar tasa/margen → actualizar simulador
- Cache de tasas con TTL corto
- Sincronización automática

**Endpoint API:**
```
GET /api/exchange-rates
Response: {
  "PEN_VES": 0.1545,
  "VES_PEN": 6.47,
  ...
}
```

### 7. Historial de Cambios

**Log de auditoría:**
- Usuario que cambió
- Par afectado
- Tasa anterior → Tasa nueva
- Margen anterior → Margen nuevo
- Fecha/hora
- Motivo (opcional)

**Reportes:**
- Cambios en último día/semana/mes
- Comparación de tasas históricas
- Exportación a CSV

---

## Tareas del Requerimiento

| # | Tarea | Horas | Estado | Descripción |
|---|-------|-------|--------|-------------|
| 7.1 | Vista unificada de pares activos | 5h | ⏸️ PENDIENTE | Listado con tasa actual |
| 7.2 | Input de tasa base | 4h | ⏸️ PENDIENTE | Formulario por par |
| 7.3 | Selector de margen | 5h | ⏸️ PENDIENTE | Global o por par |
| 7.4 | Cálculo inverso | 7h | ⏸️ PENDIENTE | Tasa base + margen |
| 7.5 | Visualizador de ganancia | 5h | ⏸️ PENDIENTE | Simulador de ganancia |
| 7.6 | Actualización en simulador | 6h | ⏸️ PENDIENTE | Integración tiempo real |
| 7.7 | Log de historial | 5h | ⏸️ PENDIENTE | Auditoría de cambios |

**Total:** 37h (0h completadas, 37h restantes)

---

## Estructura de Base de Datos

### Tabla: `exchange_rates` (ya existe, modificar)
```sql
id, from_currency_id, to_currency_id, rate, source, is_active, created_at, updated_at
```

**Agregar campos:**
- `margin_type` (enum: 'none', 'percentage', 'fixed')
- `margin_value` (decimal)
- `final_rate` (decimal, calculado)
- `last_updated_by` (user_id)
- `notes` (text, motivo del cambio)

### Tabla nueva: `exchange_rate_history`
```sql
id, exchange_rate_id, user_id, old_rate, new_rate, old_margin, new_margin, reason, created_at
```

### Tabla: `global_settings` (nueva, opcional)
```sql
id, key, value, type, description, created_at, updated_at
```

Para guardar margen global, fuente de tasas preferida, etc.

---

## Integración con Módulos Existentes

### REQ 1: Simulador
- Cargar tasas desde BD (no hardcoded)
- Usar `final_rate` en cálculos
- Mostrar última actualización

### REQ 6: Matriz de Divisas
- Solo mostrar pares activos
- Validar que par tenga corredores
- Deshabilitar tasa si par inactivo

### REQ 3: Ventas
- Guardar tasa usada en cada venta
- Calcular ganancia real vs estimada
- Reportes de márgenes efectivos

### REQ 5: Dashboard Dueño
- Mostrar ganancia por tasas
- Comparar margen configurado vs real
- Alertas si margen bajo

---

## Casos de Uso

### Caso 1: Admin actualiza tasa PEN → VES
1. Accede a consola de tasas
2. Localiza par PEN → VES
3. Ingresa nueva tasa base: 0.16 (antes 0.15)
4. Margen global 3% se aplica automáticamente
5. Tasa final calculada: 0.1648
6. Click "Guardar"
7. → Actualiza BD
8. → Registra en historial
9. → Simulador refleja cambio inmediatamente

### Caso 2: Admin configura margen específico
1. Par USD → PEN tiene margen global 3%
2. Admin quiere margen 5% solo para este par
3. Edita par, cambia margen a 5%
4. → Tasa final se recalcula
5. → Cambio aplicado solo a USD → PEN
6. → Otros pares mantienen margen global

### Caso 3: Admin visualiza ganancia
1. Ingresa monto simulado: 1000 PEN
2. Sistema calcula:
   - A tasa base: 160 VES
   - A tasa cliente: 164.8 VES
   - **Ganancia: 4.8 VES (~30 PEN)**
3. Muestra porcentaje: 3%

### Caso 4: Auditoría de cambios
1. Admin accede a historial
2. Filtra por par PEN → VES
3. Ve que tasa cambió 3 veces hoy
4. Identifica quién y cuándo
5. Exporta reporte a CSV

---

## Validaciones

✅ Tasa base > 0  
✅ Margen >= 0 y <= 100%  
✅ Tasa final != tasa base (si hay margen)  
✅ Par debe estar activo (REQ 6)  
✅ Solo admins pueden editar tasas  
✅ Registrar cambios en historial  
✅ Validar formato numérico (decimales)

---

## UI/UX

### Vista principal (Consola de Tasas)
- Tabla con 6 columnas:
  - Par
  - Tasa Base
  - Margen
  - Tasa Final
  - Ganancia %
  - Acciones
- Botones: Editar, Historial
- Filtros: Divisa origen/destino
- Botón global: "Configurar Margen Global"

### Modal de edición
- Input tasa base (numérico)
- Radio: Margen global / Margen específico
- Input margen (%)
- Preview tasa final
- Textarea motivo (opcional)
- Botones: Guardar / Cancelar

### Visualizador de ganancia
- Input monto simulado
- Display ganancia calculada
- Display % de margen
- Comparación con tasa anterior

---

## Mejoras Futuras

1. **Integración con APIs externas:**
   - Sunat, BCR, Yahoo Finance
   - Actualización automática cada X horas

2. **Alertas automáticas:**
   - Si tasa varía más de X% vs mercado
   - Si margen cae por debajo de mínimo

3. **Márgenes dinámicos:**
   - Por rango de monto (mayor monto = menor margen)
   - Por tipo de cliente (frecuente vs nuevo)

4. **Dashboard de tendencias:**
   - Gráfico de evolución de tasas
   - Comparación vs competencia
   - Predicción de ganancia

---

**Fecha creación:** 2026-04-20  
**Última actualización:** 2026-04-20
