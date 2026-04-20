# REQ 7: Consola de Tasas de Cambio

**Estado:** ✅ COMPLETADO  
**Duración:** 37h  
**Fecha finalización:** 2026-04-20

---

## Resumen

Módulo completo para gestionar tasas de cambio de divisas (PEN, ARS, CLP → VES), con:
- Sistema de pares de divisas configurable
- Una tasa activa por par (exclusividad automática)
- Historial completo de cambios (auditoría)
- Simulador público dinámico
- Integración con sistema de ventas (snapshots)

---

## Funcionalidades Implementadas

### 1. Gestión de Tasas

**Ruta:** `/exchange_rates`

**Características:**
- ✅ Vista index con filtros por divisa y estado
- ✅ Crear nuevas tasas con validaciones
- ✅ Editar tasas existentes
- ✅ Activar/desactivar tasas
- ✅ Protección: no editar si tiene transacciones

**Campos de cada tasa:**
```php
- currency_pair_id: Par de divisas (PEN→VES, ARS→VES, etc.)
- ves_rate: Tasa específica del par (ej: 173.71)
- usd_rate: Referencia BCV (ej: 479.78)
- eur_rate: Referencia BCV (ej: 565.98)
- is_active: Boolean (solo 1 activa por par)
```

### 2. Filtros Inteligentes

**Por defecto:** Solo muestra tasas activas

**Opciones:**
- ✓ Activas (actual) - Default
- ✕ Inactivas (historial)
- Todas (activas + historial)

**Beneficio:** Usuario ve solo tasas relevantes, historial disponible cuando se necesita.

### 3. Sistema de Auditoría

**Tabla:** `exchange_rate_history`

**Eventos registrados:**
- created: Al crear nueva tasa
- updated: Al modificar campos
- activated: Al activar tasa
- deactivated: Al desactivar
- deleted: Antes de eliminar

**Información capturada:**
```json
{
  "user_id": 1,
  "user_name": "Admin",
  "action": "updated",
  "old_values": {"ves_rate": 173.71},
  "new_values": {"ves_rate": 175.00},
  "ip_address": "192.168.1.1",
  "user_agent": "Mozilla/5.0...",
  "created_at": "2026-04-20 18:30:00"
}
```

**Vista de historial:**
- Timeline visual con iconos
- Colores por tipo de acción
- Últimos 20 cambios
- Usuario, IP, fecha

### 4. Validaciones

**A nivel de controlador:**
```php
'currency_pair_id' => 'required|exists:currency_pairs,id'
'ves_rate' => 'required|numeric|min:0.00001'
'usd_rate' => 'required|numeric|min:0.01'
'eur_rate' => 'required|numeric|min:0.01'
```

**A nivel de modelo:**
```php
// Evento boot() previene valores <= 0
if ($rate->ves_rate <= 0) {
    throw new InvalidArgumentException('Las tasas deben ser mayores a 0');
}
```

**Mensajes en español:**
- "La tasa VES debe ser mayor a 0"
- "Debes seleccionar un par de divisas"
- "El par de divisas seleccionado no existe"

### 5. Simulador Público

**Ruta:** `/` (welcome.blade.php)

**Características:**
- ✅ Selector de países (Perú, Argentina, Chile)
- ✅ Labels dinámicos según país
- ✅ Símbolos de moneda correctos
- ✅ Cálculo automático al cambiar
- ✅ Carga tasas desde BD
- ✅ Actualización en tiempo real

**Ejemplo:**
```
Usuario selecciona: 🇵🇪 Perú
Label: "En Sol Peruano (Directo)"
Símbolo: S/
Tasa: 1 PEN = 173.71 VES
Input: 100 PEN
Output: 17,371.00 VES
```

### 6. Integración con Ventas

**Snapshot automático:**
```php
$sale->exchange_rate_snapshot = [
    'ves_rate' => 173.71,
    'usd_rate' => 479.78,
    'eur_rate' => 565.98,
];
```

**Beneficio:** Ventas mantienen la tasa histórica aunque se cambie después.

---

## Estructura de Archivos

### Migraciones

```
database/migrations/
├── 2026_04_20_143438_create_currencies_table.php
├── 2026_04_20_144129_create_currency_pairs_table.php
├── 2026_04_16_124255_create_exchange_rates_table.php
├── 2026_04_20_154114_add_reference_rates_to_exchange_rates_table.php
├── 2026_04_20_174146_remove_margin_fields_from_exchange_rates_table.php
└── 2026_04_20_182309_create_exchange_rate_history_table.php
```

### Modelos

```
app/Models/
├── Currency.php
├── CurrencyPair.php
├── ExchangeRate.php
└── ExchangeRateHistory.php
```

### Controladores

```
app/Http/Controllers/
└── ExchangeRateController.php
```

### Observadores

```
app/Observers/
└── ExchangeRateObserver.php
```

### Vistas

```
resources/views/
├── welcome.blade.php (simulador público)
└── exchange_rates/
    ├── index.blade.php (listado con filtros)
    ├── create.blade.php (crear tasa)
    └── edit.blade.php (editar + historial)
```

### Seeders

```
database/seeders/
├── CurrencySeeder.php
├── CurrencyPairSeeder.php
└── ExchangeRateSeeder.php
```

---

## Rutas Registradas

```
GET    /exchange_rates              exchange_rates.index
POST   /exchange_rates              exchange_rates.store
GET    /exchange_rates/create       exchange_rates.create
GET    /exchange_rates/{id}         exchange_rates.show
GET    /exchange_rates/{id}/edit    exchange_rates.edit
PUT    /exchange_rates/{id}         exchange_rates.update
DELETE /exchange_rates/{id}         exchange_rates.destroy
POST   /exchange_rates/{id}/activate exchange_rates.activate
```

---

## Decisiones Técnicas

### 1. Solo 1 tasa activa por par

**Implementación:**
```php
public function activate()
{
    // Desactivar otras del mismo par
    self::where('currency_pair_id', $this->currency_pair_id)
        ->where('id', '!=', $this->id)
        ->update(['is_active' => false]);
    
    // Activar esta
    $this->update(['is_active' => true]);
}
```

**Beneficio:** Exclusividad automática, no hay ambigüedad.

### 2. Tasas viejas se mantienen

**Razón:** Snapshots de ventas antiguas necesitan las tasas históricas.

**Solución:** Filtrar por `is_active = true` en vistas, pero mantener en BD.

### 3. Historial con evento `deleting`

**Problema:** `deleted` causa error de FK constraint.

**Solución:** Usar `deleting` (ANTES de eliminar):
```php
public function deleting(ExchangeRate $exchangeRate): void
{
    // Se ejecuta ANTES de eliminar el registro
    ExchangeRateHistory::log(...);
}
```

### 4. Validación doble (controlador + modelo)

**Controlador:** Para errores de usuario (con mensajes claros)

**Modelo:** Para prevenir creación directa desde código

**Beneficio:** Seguridad en capas.

---

## Testing

### Checklist Manual

Documento: `docs/requirements/07-consola-tasas/7.6-testing-completo/testing_checklist.md`

**40 tests** cubriendo:
- Simulador dinámico
- CRUD de tasas
- Filtros
- Historial
- Integración con ventas

### Verificación Automática

Script: `test-req7.php`

**Verificaciones:**
- ✅ Modelos cargados
- ✅ Datos en BD
- ✅ Tasas activas correctas
- ✅ Exclusividad por par
- ✅ Historial funcional
- ✅ Rutas registradas

---

## Comandos Útiles

### Verificar estado

```bash
./vendor/bin/sail artisan tinker --execute="
ExchangeRate::where('is_active', true)->with('currencyPair')->get()
"
```

### Ver historial de tasa

```bash
./vendor/bin/sail artisan tinker --execute="
\$rate = ExchangeRate::find(1);
\$rate->history()->with('user')->get()
"
```

### Activar tasa

```bash
./vendor/bin/sail artisan tinker --execute="
\$rate = ExchangeRate::find(5);
\$rate->activate();
"
```

### Resetear datos de prueba

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

---

## Casos de Uso

### 1. Actualizar Tasa del Día

**Usuario:** Admin  
**Acción:** Ir a Consola de Tasas → Editar PEN→VES → Cambiar ves_rate a 175.00 → Guardar

**Resultado:**
- Tasa actualizada ✓
- Simulador público muestra nueva tasa ✓
- Historial registra cambio ✓
- Ventas futuras usan 175.00 ✓
- Ventas antiguas mantienen snapshot de 173.71 ✓

### 2. Agregar Nuevo Par (USD→VES)

**Pasos:**
1. Crear divisa USD en `currencies`
2. Crear par USD→VES en `currency_pairs`
3. Ir a Consola → Nueva Tasa
4. Seleccionar par USD→VES
5. Ingresar ves_rate (ej: 479.78)
6. Guardar

**Resultado:**
- Tasa creada y activada automáticamente ✓
- Aparece en listado ✓
- Simulador puede soportarla (con ajustes menores) ✓

### 3. Auditar Cambios

**Pregunta:** ¿Quién cambió la tasa ayer a las 3pm?

**Pasos:**
1. Ir a Editar Tasa
2. Scroll a "Historial de Cambios"
3. Ver timeline con usuario, IP, fecha

**O con código:**
```php
ExchangeRateHistory::whereDate('created_at', '2026-04-19')
    ->whereBetween('created_at', ['2026-04-19 15:00', '2026-04-19 16:00'])
    ->with('user')
    ->get();
```

---

## Mejoras Futuras

### 1. API REST

```php
GET /api/exchange-rates          // Listar activas
GET /api/exchange-rates/{pair}   // Tasa de par específico
```

**Uso:** Aplicaciones móviles o terceros.

### 2. Notificaciones

```php
// Al cambiar >5% enviar email
if ($changePercent > 5) {
    Mail::to('admin@cambio.com')->send(new RateChangedNotification($rate));
}
```

### 3. Gráficos Históricos

```
Chart.js con datos de exchange_rate_history
→ Ver evolución de tasa PEN→VES últimos 30 días
```

### 4. Restaurar Versión

```php
public function restore(ExchangeRateHistory $history)
{
    $rate = $history->exchangeRate;
    $rate->update($history->old_values);
}
```

**Uso:** Deshacer cambio accidental.

---

## Métricas de Implementación

**Archivos creados:** 15
- 6 migraciones
- 4 modelos
- 1 controlador
- 1 observer
- 3 vistas

**Archivos modificados:** 8
- AppServiceProvider
- SaleController
- routes/web.php
- welcome.blade.php
- etc.

**Líneas de código:** ~2,500
- Backend: 1,200
- Frontend: 800
- Documentación: 500

**Commits:** 7
- REQ 7.1-7.5: Implementación core
- REQ 7.6: Testing + correcciones
- REQ 7.7: Sistema de auditoría
- REQ 7.8: Ajustes finales

---

## Lecciones Aprendidas

### 1. Validación en capas

**Aprendizaje:** Validar en controlador (UX) Y modelo (seguridad).

**Implementación:**
- Controlador: Mensajes claros en español
- Modelo: Throw exceptions en eventos boot()

### 2. Eventos de Eloquent

**`deleted` vs `deleting`:**
- `deleted`: Después de eliminar (FK constraint ❌)
- `deleting`: Antes de eliminar (funciona ✓)

### 3. Filtros inteligentes

**UX:** Mostrar solo datos relevantes por defecto.

**Historial disponible pero oculto** evita confusión.

### 4. Snapshots para historialidad

**No depender de FK** para datos históricos.

**Guardar JSON snapshot** de datos críticos en transacciones.

---

## Conclusión

✅ **Módulo completo y funcional**
- CRUD completo de tasas
- Filtros y búsquedas
- Historial de auditoría
- Simulador público
- Validaciones robustas
- Integración con ventas

**Estado:** Listo para producción

---

**Documentado por:** Claude Sonnet 4.5  
**Fecha:** 2026-04-20  
**Versión:** 1.0
