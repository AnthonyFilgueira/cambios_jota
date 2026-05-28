# TEST-005 — Gestión de Tasas de Cambio

## Objetivo
Verificar que los administradores puedan crear, editar, activar, desactivar y eliminar tasas de cambio, y que el simulador público y el formulario de transacciones reflejen solo las tasas activas.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/ExchangeRateController.php`
  - Métodos: `index`, `create`, `store`, `edit`, `update`, `destroy`, `activate`
- **Vista:** `resources/views/exchange_rates/index.blade.php`
- **Vista:** `resources/views/exchange_rates/create.blade.php`
- **Vista:** `resources/views/exchange_rates/edit.blade.php`
- **Modelo:** `app/Models/ExchangeRate.php`
  - Métodos clave: `getActive()`, `getDefault()`, `getActiveForPair()`, `calculateVesAmount()`
- **Rutas:**
  - `GET /exchange-rates` → `exchange_rates.index`
  - `GET /exchange-rates/create` → `exchange_rates.create`
  - `POST /exchange-rates` → `exchange_rates.store`
  - `GET /exchange-rates/{id}/edit` → `exchange_rates.edit`
  - `PUT /exchange-rates/{id}` → `exchange_rates.update`
  - `DELETE /exchange-rates/{id}` → `exchange_rates.destroy`
  - `POST /exchange-rates/{id}/activate` → `exchange_rates.activate`

## Estado actual de tasas (BD local)

| ID | Par | ves_rate | usd_rate | Activa |
|----|-----|----------|----------|--------|
| 1 | PEN→VES | 173.71 | 479.78 | ✅ |
| 2 | ARS→VES | 2.50 | 479.78 | ❌ (inactiva - duplicado) |
| 3 | CLP→VES | 0.55 | 479.78 | ❌ (inactiva - duplicado) |
| 4 | ARS→VES | 3.00 | 479.78 | ✅ |
| 5 | CLP→PEN | 0.0045 | 0.001 | ✅ |
| 6 | CLP→VES | 0.55 | 0.001 | ✅ |
| 7 | COP→VES | 0.12 | 0.0025 | ✅ |
| 8 | USD→PEN | 3.72 | 1.0 | ✅ |
| 9 | COP→PEN | 0.00082 | 0.00025 | ✅ |
| 10 | ARS→PEN | 0.0031 | 0.00083 | ✅ |
| 11 | BRL→PEN | 0.72 | 0.195 | ✅ |
| 12 | BRL→VES | 86.20 | 23.50 | ✅ |

**10 tasas activas** aparecen en el formulario de transacciones.

## Casos de prueba

### Caso 1 — Listar tasas (GET /exchange-rates)
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /exchange-rates`
- **Respuesta esperada:** HTTP 200, tabla con 12 tasas (10 activas + 2 inactivas)
- **Resultado real:** PASS ✅

### Caso 2 — Crear nueva tasa (POST /exchange-rates)
- **Precondición:** Existe el par USD→VES (o cualquier par activo)
- **Acción:** `POST /exchange-rates`
- **Datos de entrada:** `{ currency_pair_id: X, ves_rate: 170.50, usd_rate: 475.00, eur_rate: 560.00 }`
- **Respuesta esperada:** HTTP 302 → `/exchange-rates`, tasa creada con `is_active=false` por defecto
- **Resultado real:** PASS ✅
- **Observaciones:** Las tasas se crean inactivas — se activan manualmente con el botón "Activar"

### Caso 3 — Activar tasa
- **Precondición:** Tasa inactiva existente
- **Acción:** `POST /exchange-rates/{id}/activate`
- **Respuesta esperada:** `is_active=true`, otras tasas del mismo par desactivadas
- **Resultado real:** PASS ✅
- **Observaciones:** La activación es exclusiva — solo puede haber una tasa activa por par

### Caso 4 — El simulador público usa la tasa activa
- **Precondición:** Tasa activa para PEN→VES: ves_rate=173.71
- **Acción:** `GET /` (página pública)
- **Respuesta esperada:** Simulador muestra solo tasas activas, tasa PEN→VES preseleccionada
- **Resultado real:** PASS ✅
- **Observaciones:** `ExchangeRate::getActive()` → si no hay activa, usa `getDefault()` (ves=173.71, usd=479.78, eur=565.98)

### Caso 5 — Tasa activa aparece en selector del formulario
- **Precondición:** 10 tasas activas en BD
- **Acción:** `GET /transactions/create` (autenticado)
- **Respuesta esperada:** Select con 10 opciones (una por cada tasa activa)
- **Resultado real:** PASS ✅

### Caso 6 — Cálculo de monto
- **Precondición:** Tasa PEN→VES con ves_rate=173.71
- **Acción:** `ExchangeRate::find(1)->calculateVesAmount(100)`
- **Datos de entrada:** 100 PEN
- **Respuesta esperada:** 17371.0 VES
- **Resultado real:** PASS ✅

### Caso 7 — getDefault() cuando no hay tasas activas
- **Precondición:** BD vacía (test environment)
- **Acción:** `ExchangeRate::getActive()`
- **Respuesta esperada:** Objeto con `{ usd_rate: 479.77750, eur_rate: 565.98392, ves_rate: 173.71000 }`
- **Resultado real:** PASS ✅ (previene crash en página pública)

## Resultado global: PASS ✅

## Hallazgos y notas
- Hay 2 tasas duplicadas inactivas (IDs 2 y 3) del proceso de seeding iterativo. Son inofensivas (is_active=false) pero podrían confundir. Se pueden borrar desde `/exchange-rates`.
- La tasa `getDefault()` hardcodeada en el modelo garantiza que la página pública nunca crashe incluso si la BD no tiene tasas.
- En el TEST anterior (`ExampleTest`) corregimos `selectedPairId` en welcome.blade.php para manejar `$pairs` vacío.
