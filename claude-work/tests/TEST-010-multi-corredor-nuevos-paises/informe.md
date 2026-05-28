# TEST-010 — Multi-Corredor y Nuevos Países

## Objetivo
Verificar que los nuevos corredores (Chile, Colombia, Argentina, Brasil) estén correctamente configurados en la BD y disponibles en el formulario de transacciones. Comprobar que cada corredor muestra los datos del país correcto.

## Archivos involucrados
- **Seeder:** `database/seeders/MultiCorridorSeeder.php` (Chile + Colombia)
- **Seeder:** `database/seeders/MoreCorridorsSeeder.php` (Argentina + Brasil + cuentas CL/CO/AR/BR)
- **Controlador:** `app/Http/Controllers/TransactionController.php` (método: `create`, `getCurrencyPairs`)
- **Vista:** `resources/views/transactions/create.blade.php`
- **Modelos:** `Country`, `Currency`, `CurrencyPair`, `ExchangeRate`, `Bank`, `DocumentType`, `PaymentMethod`, `BusinessAccount`

## Resumen de pares activos disponibles

| Tasa ID | Corredor | ves_rate | Países involucrados |
|---------|----------|----------|---------------------|
| 1 | PEN → VES | 173.71 | Perú → Venezuela |
| 4 | ARS → VES | 3.00 | Argentina → Venezuela |
| 5 | CLP → PEN | 0.0045 | Chile → Perú |
| 6 | CLP → VES | 0.55 | Chile → Venezuela |
| 7 | COP → VES | 0.12 | Colombia → Venezuela |
| 8 | USD → PEN | 3.72 | USD → Perú |
| 9 | COP → PEN | 0.00082 | Colombia → Perú |
| 10 | ARS → PEN | 0.0031 | Argentina → Perú |
| 11 | BRL → PEN | 0.72 | Brasil → Perú |
| 12 | BRL → VES | 86.20 | Brasil → Venezuela |

## Datos completos por corredor nuevo

### 🇨🇱 Chile → 🇵🇪 Perú (CLP→PEN, tasa#5)
- **Bancos remitente:** Banco de Chile, Banco Estado, Santander Chile, BCI, Banco Falabella, Banco Itaú, BBVA Chile
- **Tipos de doc remitente:** RUT (Rol Único Tributario), RUN (Rol Único Nacional)
- **Métodos de pago:** Transferencia Bancaria
- **Cuentas del negocio:** Banco de Chile N° 00012345678 (ahorro), Banco Estado N° 98765432100 (corriente)
- **Bancos receptor (Perú):** BCP, Interbank, BBVA, Scotiabank, Yape, Plin, Nación

### 🇨🇴 Colombia → 🇻🇪 Venezuela (COP→VES, tasa#7)
- **Bancos remitente:** Bancolombia, Banco de Bogotá, Davivienda, BBVA Colombia, Banco Popular, Nequi/Bancolombia, Daviplata/Davivienda
- **Tipos de doc remitente:** CC (Cédula de Ciudadanía), CE (Cédula de Extranjería), NIT (Empresarial)
- **Métodos de pago:** Transferencia Bancaria, Nequi, Daviplata
- **Cuentas del negocio:** Bancolombia N° 12345678901 (ahorro), Davivienda N° 09876543210 (corriente)
- **Bancos receptor (Venezuela):** Bancamiga, Banco de Venezuela, Banesco, Mercantil, etc.

### 🇦🇷 Argentina → 🇵🇪 Perú (ARS→PEN, tasa#10)
- **Bancos remitente:** Banco Nación Argentina, Banco Provincia, Santander Argentina, BBVA Argentina, Banco Galicia, Banco Macro, Banco ICBC
- **Tipos de doc remitente:** DNI, CUIT, CUIL
- **Métodos de pago:** Transferencia Bancaria, CVU / Alias
- **Cuentas del negocio:** Banco Nación Argentina N° 123456789001 (ahorro), Banco Galicia N° 0540123456789 (corriente)

### 🇧🇷 Brasil → 🇵🇪 Perú (BRL→PEN, tasa#11)
- **Bancos remitente:** Banco do Brasil, Caixa Econômica Federal, Bradesco, Itaú Unibanco, Santander Brasil, Nubank, Inter
- **Tipos de doc remitente:** CPF, CNPJ, RG
- **Métodos de pago:** Transferencia Bancaria, PIX
- **Cuentas del negocio:** Banco do Brasil N° 12345-6 (ahorro), Itaú Unibanco N° 654321-0 (corriente)

## Casos de prueba

### Caso 1 — Países nuevos en BD
- **Acción:** `Country::where('active', true)->count()`
- **Respuesta esperada:** 6 países activos (PE, VE, CL, CO, AR, BR)
- **Resultado real:** PASS ✅ — 6 países confirmados

### Caso 2 — Bancos por país
- **Acción:** `Bank::where('active', true)->groupBy('country_id')->selectRaw('country_id, count(*) as total')`
- **Respuesta esperada:** Cada país tiene 7 u 8 bancos
- **Resultado real:** PASS ✅ — Perú:7, Venezuela:8, Chile:7, Colombia:7, Argentina:7, Brasil:7

### Caso 3 — Tipos de documento por país
- **Acción:** `DocumentType::where('active', true)` agrupado por país
- **Respuesta esperada:** Perú:3, Venezuela:4, Chile:2, Colombia:3, Argentina:3, Brasil:3
- **Resultado real:** PASS ✅

### Caso 4 — Cuentas del negocio asignadas a vendedores
- **Acción:** Verificar tabla pivot `business_account_seller`
- **Respuesta esperada:** 11 cuentas × 4 vendedores = 44 registros en pivot
- **Resultado real:** PASS ✅

### Caso 5 — Formulario muestra 10 tasas en el selector
- **Precondición:** Autenticado como cliente
- **Acción:** `GET /transactions/create`
- **Respuesta esperada:** `<select name="exchange_rate_id">` con 10 `<option>` (tasas activas)
- **Resultado real:** PASS ✅

### Caso 6 — Seleccionar CLP→PEN: cabecera dinámica
- **Acción:** `onRateChange()` con tasa ID=5
- **Datos leídos del select:**
  - `data-from-country="Chile"`, `data-from-flag="🇨🇱"`, `data-from-country-id=3`
  - `data-to-country="Perú"`, `data-to-flag="🇵🇪"`, `data-to-country-id=1`
- **Respuesta esperada Alpine:**
  - `fromCountry = "Chile"`, `fromFlag = "🇨🇱"`, `fromCountryId = 3`
  - `toCountry = "Perú"`, `toFlag = "🇵🇪"`, `toCountryId = 1`
  - Cabecera visible: "🇵🇪 Receptor en Perú" / "🇨🇱 Tu transferencia desde Chile"
- **Resultado real:** PASS ✅

### Caso 7 — AJAX sender-banks para Chile (country_id=3)
- **Acción:** `GET /transactions/sender-banks?country_id=3`
- **Respuesta esperada:** 7 bancos chilenos
- **Resultado real:** PASS ✅ (verificado con Tinker)

### Caso 8 — AJAX seller-accounts para corredor Chile (rate_id=5)
- **Precondición:** Cliente con seller_id=1 (Pedro Martínez)
- **Acción:** `GET /transactions/seller-accounts?rate_id=5`
- **Lógica:** ExchangeRate#5 → CLP → country_id=3 (Chile) → BusinessAccounts del vendedor en Chile
- **Respuesta esperada:** 2 cuentas (Banco de Chile, Banco Estado)
- **Resultado real:** PASS ✅

### Caso 9 — Monedas vinculadas a países
- **Acción:** `Currency::whereIn('code', ['ARS','BRL','CLP','COP'])->get(['code','country_id'])`
- **Respuesta esperada:** ARS→5, BRL→6, CLP→3, COP→4
- **Resultado real:** PASS ✅

### Caso 10 — Par COP→PEN apareció después de MoreCorridorsSeeder
- **Precondición:** Seeder ejecutado
- **Acción:** `CurrencyPair::whereHas('fromCurrency', fn($q) => $q->where('code','COP'))->whereHas('toCurrency', fn($q) => $q->where('code','PEN'))->first()`
- **Respuesta esperada:** Par activo con tasa `ves_rate=0.00082`
- **Resultado real:** PASS ✅

## Resultado global: PASS ✅

## Hallazgos y notas
- La moneda ARS estaba vinculada a Venezuela por error inicial (country_id de VES); corregida con `whereNull('country_id')` en el seeder para no sobreescribir.
- `MultiCorridorSeeder` y `MoreCorridorsSeeder` son idempotentes — pueden ejecutarse múltiples veces sin duplicar datos.
- Las tasas `CLP→VES` y `ARS→VES` están activas pero las de Chile y Colombia también incluyen pares hacia Perú (CLP→PEN, COP→PEN), dando flexibilidad multi-destino.
- El corredor `VES→PEN` (par ID=2) está en BD como `is_active=true` pero no tiene tasa activa de exchange rate en `is_active=true`. Puede ser intencional (flujo inverso no soportado aún).
