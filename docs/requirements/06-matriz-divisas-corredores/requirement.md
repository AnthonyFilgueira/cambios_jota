# REQ 6: Matriz de Divisas y Corredores

**Estado:** 🔄 EN PROGRESO (0% completado)  
**Horas estimadas:** 33h  
**Horas completadas:** 0h  
**Horas restantes:** 33h  
**Prioridad:** ALTA

---

## Descripción General

Sistema de gestión de catálogo de divisas, pares de conversión y corredores de envío. Permite configurar qué monedas están disponibles, qué pares de conversión son válidos y qué corredores pueden procesarlos.

---

## Objetivo

Hacer el sistema escalable y dinámico:
- Agregar nuevas divisas sin modificar código
- Habilitar/deshabilitar pares de conversión
- Gestionar corredores por par
- Validar disponibilidad en tiempo real
- Integrar con el simulador (REQ 1)

---

## Alcance

### 1. Catálogo Maestro de Monedas

**Información por divisa:**
- Nombre (ej: "Nuevo Sol Peruano")
- Código ISO (ej: "PEN")
- Símbolo (ej: "S/.")
- País (ej: "Perú")
- Estado (activa/inactiva)
- Imagen/bandera (opcional)

**Divisas iniciales:**
- PEN (Nuevo Sol Peruano)
- VES (Bolívar Venezolano)
- USD (Dólar Estadounidense)
- EUR (Euro)
- COP (Peso Colombiano)
- ARS (Peso Argentino)

### 2. CRUD de Divisas

**Funcionalidades:**
- Listar todas las divisas
- Crear nueva divisa
- Editar divisa existente
- Activar/desactivar divisa
- No eliminar (soft delete o desactivar)

**Validaciones:**
- Código ISO único
- Símbolo único
- Nombre descriptivo

### 3. Pares de Conversión

**Definición:**
Un par es una combinación origen → destino.

**Ejemplos:**
- PEN → VES (habilitado)
- VES → PEN (habilitado)
- USD → PEN (habilitado)
- PEN → EUR (deshabilitado)

**Campos:**
- Divisa origen
- Divisa destino
- Estado (habilitado/deshabilitado)
- Corredores disponibles

### 4. Corredores de Envío

**Definición:**
Empresas/servicios que procesan las conversiones.

**Ejemplos:**
- Western Union
- MoneyGram
- Remitly
- Wise
- Cambio J Directo

**Información:**
- Nombre del corredor
- Logo
- Países que atiende
- Pares que procesa
- Comisión (opcional)
- Tiempo estimado

### 5. Matriz de Corredores

**Vista de administración:**
Tabla donde filas = pares y columnas = corredores.

```
              | Western Union | MoneyGram | Remitly | Cambio J
-----------------------------------------------------------------
PEN → VES     |      ✓        |     ✓     |    ✓    |    ✓
VES → PEN     |      ✓        |     ✓     |    ✗    |    ✓
USD → PEN     |      ✓        |     ✗     |    ✓    |    ✗
```

**Funcionalidad:**
- Click para habilitar/deshabilitar
- Ver corredores disponibles por par
- Filtros por divisa

### 6. API Dinámica para Simulador

**Endpoint:**
```
GET /api/available-pairs
```

**Respuesta:**
```json
{
  "pairs": [
    {
      "from": {"code": "PEN", "symbol": "S/.", "name": "Sol"},
      "to": {"code": "VES", "symbol": "Bs.", "name": "Bolívar"},
      "corridors": ["Western Union", "MoneyGram", "Cambio J"],
      "active": true
    }
  ]
}
```

**Integración con simulador:**
- Cargar divisas dinámicamente
- Mostrar solo pares habilitados
- Actualizar en tiempo real

### 7. Validación en Tiempo Real

**Al crear venta:**
1. Verificar que el par existe
2. Verificar que el par está habilitado
3. Verificar que hay al menos 1 corredor disponible
4. Rechazar si no cumple condiciones

---

## Tareas del Requerimiento

| # | Tarea | Horas | Descripción |
|---|-------|-------|-------------|
| 6.1 | Modelo y migración de divisas | 6h | Currency model + CRUD |
| 6.2 | Modelo y migración de pares | 5h | CurrencyPair model |
| 6.3 | Modelo y migración de corredores | 6h | Corridor model |
| 6.4 | Tabla intermedia par-corredor | 5h | Pivot table |
| 6.5 | Interfaz de gestión de divisas | 6h | CRUD completo |
| 6.6 | Interfaz matriz de corredores | 5h | Vista interactiva |

**Total:** 33h

---

## Estructura de Base de Datos

### Tabla: `currencies`
```sql
id, code, name, symbol, country, is_active, created_at, updated_at
```

### Tabla: `currency_pairs`
```sql
id, from_currency_id, to_currency_id, is_active, created_at, updated_at
```

### Tabla: `corridors`
```sql
id, name, description, logo_url, is_active, created_at, updated_at
```

### Tabla: `corridor_pair` (pivot)
```sql
id, corridor_id, currency_pair_id, is_enabled, created_at, updated_at
```

---

## Integración con Módulos Existentes

### REQ 1 - Simulador
- Cargar divisas desde BD (no hardcoded)
- Validar pares habilitados
- Mostrar corredores disponibles (opcional)

### REQ 3 - Ventas
- Validar par al crear venta
- Guardar corredor seleccionado
- Rechazar si par deshabilitado

### REQ 7 - Tasas
- Tasas por par de divisas
- Solo pares habilitados

---

## Casos de Uso

### Caso 1: Admin agrega nueva divisa
1. Accede a /currencies
2. Click "Nueva Divisa"
3. Ingresa: CLP, Peso Chileno, $, Chile
4. Guardar
5. Divisa disponible para crear pares

### Caso 2: Admin habilita par PEN → USD
1. Accede a /currency-pairs
2. Click "Nuevo Par"
3. Selecciona: Origen PEN, Destino USD
4. Habilita corredores: Western Union, Cambio J
5. Guardar
6. Par disponible en simulador

### Caso 3: Admin deshabilita corredor
1. Accede a matriz de corredores
2. Ubica par PEN → VES
3. Deshabilita "MoneyGram" para ese par
4. Cambio reflejado inmediatamente

### Caso 4: Cliente usa simulador
1. Accede a simulador
2. Ve solo divisas activas
3. Selecciona PEN
4. Ve solo destinos con pares habilitados
5. Selecciona VES
6. Simulador funciona correctamente

---

## Validaciones

✅ Código ISO único  
✅ No eliminar divisas en uso  
✅ Al menos 1 corredor por par  
✅ No duplicar pares (PEN→VES ya existe)  
✅ Validar existencia de divisas  

---

## UI/UX

### Gestión de Divisas
- Tabla con búsqueda
- Botón activar/desactivar
- Badge de estado
- Banderas de países (opcional)

### Gestión de Pares
- Lista filtrable
- Origen → Destino visual
- Estado claro
- Corredores asignados

### Matriz de Corredores
- Tabla interactiva
- Checkboxes para habilitar
- Guardado automático (AJAX)
- Visual feedback

---

## Mejoras Futuras

1. **Importación masiva:**
   - CSV de divisas
   - API externa para tasas

2. **Geo-restricciones:**
   - Limitar pares por país del cliente
   - Detectar ubicación

3. **Costos por corredor:**
   - Comisión diferenciada
   - Comparador de precios

4. **Tiempos estimados:**
   - Mostrar duración por corredor
   - "Entrega en 24h"

---

**Fecha creación:** 2026-04-20  
**Última actualización:** 2026-04-20
