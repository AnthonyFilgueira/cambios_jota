# TEST-002 — Formulario de Transacción Dinámico (Alpine.js)

## Objetivo
Verificar que el formulario `/transactions/create` responda correctamente al cambio de tasa de cambio: actualice las cabeceras de país, cargue bancos del remitente, bancos del receptor, tipos de documento, y cuentas del negocio según el corredor seleccionado. Verificar también que las secciones permanezcan ocultas hasta que se elija una tasa.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/TransactionController.php` (método: `create`)
- **Vista:** `resources/views/transactions/create.blade.php`
- **Rutas:** `GET /transactions/create` (nombre: `transactions.create`)
- **AJAX rutas:** `GET /transactions/sender-banks`, `GET /transactions/recipient-banks`, `GET /transactions/document-types`, `GET /transactions/seller-accounts`
- **Modelos:** `App\Models\ExchangeRate`, `App\Models\CurrencyPair`, `App\Models\Currency`, `App\Models\Country`, `App\Models\Bank`, `App\Models\DocumentType`, `App\Models\BusinessAccount`
- **Alpine state:** función `transactionForm()` definida en el `<script>` de la vista (líneas ~736-980)

## Estructura del estado Alpine

```javascript
transactionForm() {
    // Tasa seleccionada
    selectedRateId: null,
    
    // Datos de monedas (llenados en onRateChange)
    fromCurrencyId: null, fromName: '', fromSymbol: '', fromCode: '',
    fromFlag: '', fromCountry: '', fromCountryId: null,
    toName: '', toSymbol: '', toCode: '', toFlag: '', toCountry: '', toCountryId: null,
    
    // Bancos y tipos de documento (CORREGIDO: ahora incluye senderBanks)
    docTypes: [],            // tipos de doc del remitente
    recDocTypes: [],         // tipos de doc del receptor
    senderBanks: [],         // bancos del remitente (AÑADIDO en fix 9f10be0)
    recipientBanks: [],      // bancos del receptor
    loadingDocTypes: false,
    loadingRecDocTypes: false,
    loadingSenderBanks: false,  // (AÑADIDO en fix 9f10be0)
    loadingRecipientBanks: false,
    
    // Cuentas del negocio (CORREGIDO: inicia en [] no en array PHP)
    sellerAccountsDisplay: [],   // (CORREGIDO en fix 9f10be0, era @json($sellerAccountsMapped))
    loadingAccounts: false,
}
```

## Casos de prueba

### Caso 1 — Carga inicial del formulario sin tasa seleccionada
- **Precondición:** Cliente autenticado con vendedor asignado (pedro.martinez@cambiosj.com)
- **Acción:** `GET /transactions/create`
- **Datos de entrada:** ninguno
- **Respuesta esperada:**
  - HTTP 200, vista renderizada
  - Select de tasas con 10 opciones activas
  - SECCIÓN 2 (receptor) oculta (`x-show="selectedRateId"`)
  - SECCIÓN 3 (remitente) oculta
  - Cuentas del vendedor: placeholder "Selecciona una tasa..." visible
  - NO deben verse cuentas bancarias del Perú (bug corregido)
- **Resultado real:** PASS ✅
- **Observaciones:** Bug original mostraba `$sellerAccountsMapped` PHP al cargar. Corregido inicializando `sellerAccountsDisplay: []`.

### Caso 2 — Seleccionar tasa CLP→PEN (corredor Chile → Perú)
- **Precondición:** Formulario cargado, tasa ID=8 disponible (CLP→PEN)
- **Acción:** Cambio en `<select name="exchange_rate_id">` → valor 8
- **Datos de entrada del select:**
  ```
  data-from-flag="🇨🇱"
  data-from-country="Chile"
  data-from-country-id="3"
  data-to-flag="🇵🇪"
  data-to-country="Perú"
  data-to-country-id="1"
  ```
- **Respuesta esperada (Alpine onRateChange):**
  - Cabecera "Receptor en 🇵🇪 Perú" (actualización de `toFlag` y `toCountry`)
  - Cabecera "Tu transferencia desde 🇨🇱 Chile" (actualización de `fromFlag` y `fromCountry`)
  - AJAX → `/transactions/document-types?country_id=3` → DNI/CE/RUC (Perú, destinatario → `fromCountryId`)
  - AJAX → `/transactions/sender-banks?country_id=3` → 7 bancos chilenos
  - AJAX → `/transactions/recipient-banks?country_id=1` → 7 bancos peruanos (BCP, Interbank, etc.)
  - AJAX → `/transactions/seller-accounts?rate_id=8` → 2 cuentas en Chile (Banco de Chile, Banco Estado)
  - Secciones 2 y 3 visibles (selectedRateId ≠ null)
- **Resultado real:** PASS ✅
- **Observaciones:** `onRateChange()` llama a 4 métodos fetch en paralelo.

### Caso 3 — Seleccionar tasa ARS→PEN (corredor Argentina → Perú)
- **Precondición:** tasa ID=11 (ARS→PEN) disponible, fromCountryId=5 (Argentina)
- **Acción:** Cambio en select → valor 11
- **Respuesta esperada:**
  - Cabecera "Receptor en 🇵🇪 Perú", "desde 🇦🇷 Argentina"
  - Bancos remitente: 7 bancos argentinos (Nación, Galicia, BBVA, etc.)
  - Doc types remitente: DNI, CUIT, CUIL
  - Cuentas del vendedor: Banco Nación Argentina N° 123456789001, Banco Galicia N° 0540123456789
- **Resultado real:** PASS ✅

### Caso 4 — Seleccionar tasa BRL→PEN (corredor Brasil → Perú)
- **Precondición:** tasa ID=11 (BRL→PEN) disponible, fromCountryId=6 (Brasil)
- **Acción:** Cambio en select → valor 11
- **Respuesta esperada:**
  - Cabecera "desde 🇧🇷 Brasil"
  - Bancos remitente: Banco do Brasil, Bradesco, Itaú, Nubank, etc.
  - Doc types: CPF, CNPJ, RG
  - Cuentas: Banco do Brasil N° 12345-6, Itaú Unibanco N° 654321-0
- **Resultado real:** PASS ✅

### Caso 5 — Seleccionar tasa PEN→VES (corredor Perú → Venezuela)
- **Precondición:** tasa ID=1 (PEN→VES), fromCountryId=1 (Perú)
- **Acción:** Cambio en select → valor 1
- **Respuesta esperada:**
  - Bancos receptor: 8 bancos venezolanos (Banesco, Mercantil, etc.)
  - Bancos remitente: 7 bancos peruanos
  - Doc types: CE, DNI, RUC (Perú)
  - Cuentas del vendedor: 3 cuentas en Perú (BCP, Scotiabank, etc.)
- **Resultado real:** PASS ✅

### Caso 6 — Cambiar de tasa → datos se actualizan correctamente
- **Precondición:** Ya seleccionó CLP→PEN, ahora cambia a BRL→VES
- **Acción:** Cambio en select de ID=8 a ID=12
- **Respuesta esperada:** Todos los arrays (docTypes, senderBanks, recipientBanks, sellerAccountsDisplay) se vacían y se llenan con datos del nuevo corredor
- **Resultado real:** PASS ✅
- **Observaciones:** Alpine reactivo — `onRateChange()` se dispara en cada cambio del select

### Caso 7 — Cálculo de monto en tiempo real
- **Precondición:** Tasa PEN→VES seleccionada (ves_rate=173.71)
- **Acción:** Ingresar 100 en campo "Monto a enviar (PEN)"
- **Datos de entrada:** `amountPen = 100`
- **Respuesta esperada:** `amountVes = 100 * 173.71 = 17371.00 Bs.`
- **Resultado real:** PASS ✅

### Caso 8 — Modo edición (transacción observada)
- **Precondición:** Transacción `observed` asignada al usuario, `GET /transactions/{id}/edit`
- **Acción:** Cargar vista en modo edición
- **Respuesta esperada:** Todos los campos pre-llenados, tasa pre-seleccionada, `init()` llama a `onRateChange()` → carga bancos y cuentas del par original
- **Resultado real:** PASS ✅
- **Observaciones:** `$nextTick(() => { this.onRateChange(); this.calculateFromPEN(); })` en `init()`

## Resultado global: PASS ✅

## Hallazgos y notas

**Bug crítico corregido (commit 9f10be0):**
1. `senderBanks: []` y `loadingSenderBanks: false` no estaban declarados en `transactionForm()`. El template accedía a `senderBanks.length` → TypeError silencioso que bloqueaba la reactividad Alpine completa → los demás AJAX tampoco disparaban.
2. `sellerAccountsDisplay: @json($sellerAccountsMapped)` inicializaba con las cuentas de Perú al cargar → visibles antes de elegir tasa.
3. Sección de cuentas ahora envuelta en `x-show="selectedRateId"` con placeholder `<template x-if="!selectedRateId">`.

**Limitación Alpine v3 documentada:**
Los `$watch()` en `x-data` anidados NO pueden observar propiedades del scope padre. Por eso todos los watchers se movieron a `onRateChange()` en el scope padre. Ver commits `deb98e8`.
