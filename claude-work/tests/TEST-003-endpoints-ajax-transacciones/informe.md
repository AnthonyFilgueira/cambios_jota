# TEST-003 — Endpoints AJAX de Transacciones

## Objetivo
Verificar que los 5 endpoints AJAX del `TransactionController` respondan con los datos correctos según los parámetros recibidos. Estos endpoints alimentan el formulario dinámico de creación de transacciones.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/TransactionController.php`
  - Métodos: `getDocumentTypes`, `getPaymentMethods`, `getSenderBanks`, `getRecipientBanks`, `getSellerAccounts`
- **Modelos:** `App\Models\DocumentType`, `App\Models\PaymentMethod`, `App\Models\Bank`, `App\Models\BusinessAccount`
- **Rutas:**
  - `GET /transactions/document-types?country_id={id}` → `transactions.documentTypes`
  - `GET /transactions/payment-methods?country_id={id}` → `transactions.paymentMethods`
  - `GET /transactions/sender-banks?country_id={id}` → `transactions.senderBanks`
  - `GET /transactions/recipient-banks?country_id={id}` → `transactions.recipientBanks`
  - `GET /transactions/seller-accounts?rate_id={id}` → `transactions.sellerAccounts`
- **Middleware:** Todos requieren autenticación (`auth`)

## Datos de referencia (BD en local)

| country_id | País | Bancos activos | Tipos de doc |
|-----------|------|---------------|-------------|
| 1 | 🇵🇪 Perú | 7 | 3 (CE, DNI, RUC) |
| 2 | 🇻🇪 Venezuela | 8 | 4 (E, G, J, V) |
| 3 | 🇨🇱 Chile | 7 | 2 (RUN, RUT) |
| 4 | 🇨🇴 Colombia | 7 | 3 (CC, CE, NIT) |
| 5 | 🇦🇷 Argentina | 7 | 3 (CUIL, CUIT, DNI) |
| 6 | 🇧🇷 Brasil | 7 | 3 (CNPJ, CPF, RG) |

## Casos de prueba

### Caso 1 — GET /transactions/document-types?country_id=5 (Argentina)
- **Precondición:** Autenticado
- **Acción:** AJAX GET con `country_id=5`
- **Respuesta esperada:** JSON array con 3 objetos `{ id, code, name, placeholder }`
- **Respuesta real:**
  ```json
  [
    { "id": 15, "code": "CUIL", "name": "Clave Única de Identificación Laboral", "placeholder": "20-12345678-9" },
    { "id": 14, "code": "CUIT", "name": "Clave Única de Identificación Tributaria", "placeholder": "20-12345678-9" },
    { "id": 13, "code": "DNI",  "name": "Documento Nacional de Identidad", "placeholder": "12345678" }
  ]
  ```
- **Resultado real:** PASS ✅

### Caso 2 — GET /transactions/document-types sin country_id
- **Precondición:** Autenticado
- **Acción:** AJAX GET sin parámetro `country_id`
- **Respuesta esperada:** JSON `[]` (array vacío)
- **Resultado real:** PASS ✅
- **Observaciones:** Controlador valida `if (!$countryId) return response()->json([]);`

### Caso 3 — GET /transactions/sender-banks?country_id=3 (Chile)
- **Precondición:** Autenticado
- **Acción:** AJAX GET con `country_id=3`
- **Respuesta esperada:** JSON array 7 bancos chilenos ordenados por nombre
- **Respuesta real:**
  ```json
  [
    { "id": 19, "name": "BBVA Chile" },
    { "id": 17, "name": "BCI" },
    { "id": 16, "name": "Banco de Chile" },
    { "id": 18, "name": "Banco Estado" },
    { "id": 21, "name": "Banco Falabella Chile" },
    { "id": 20, "name": "Banco Itaú Chile" },
    { "id": 15, "name": "Santander Chile" }
  ]
  ```
- **Resultado real:** PASS ✅

### Caso 4 — GET /transactions/recipient-banks?country_id=2 (Venezuela)
- **Precondición:** Autenticado
- **Acción:** AJAX GET con `country_id=2`
- **Respuesta esperada:** JSON array 8 bancos venezolanos
- **Respuesta real:**
  ```json
  [
    "Bancamiga", "Banco de Venezuela", "Banco del Tesoro", "Banesco",
    "Exterior", "Mercantil", "Provincial (BBVA)", "Venezuela Bicentenario"
  ]
  ```
- **Resultado real:** PASS ✅

### Caso 5 — GET /transactions/recipient-banks?country_id=1 (Perú)
- **Precondición:** Autenticado
- **Acción:** AJAX GET con `country_id=1`
- **Respuesta esperada:** JSON array 7 bancos peruanos (para corredores X→PEN)
- **Respuesta real:** BCP, Interbank, BBVA, Scotiabank, Yape, Plin, Banco de la Nación
- **Resultado real:** PASS ✅

### Caso 6 — GET /transactions/payment-methods?country_id=6 (Brasil)
- **Precondición:** Autenticado
- **Acción:** AJAX GET con `country_id=6`
- **Respuesta esperada:** JSON array con Transferencia Bancaria + PIX
- **Respuesta real:**
  ```json
  [
    { "id": 11, "code": "pix", "name": "PIX" },
    { "id": 10, "code": "transferencia_bancaria", "name": "Transferencia Bancaria" }
  ]
  ```
- **Resultado real:** PASS ✅

### Caso 7 — GET /transactions/seller-accounts?rate_id=8 (CLP→PEN)
- **Precondición:** Autenticado como cliente con vendedor asignado (seller_id=1, Pedro Martínez)
- **Acción:** AJAX GET con `rate_id=8`
- **Lógica del controlador:**
  1. Obtiene `ExchangeRate#8` → `currencyPair.from_currency_id` → Currency CLP → `country_id=3` (Chile)
  2. Obtiene `BusinessAccount` donde `country_id=3` y `sellers.seller_id = auth_user->assigned_seller_id`
  3. Retorna array de cuentas con `{ id, alias, bank_name, account_number, account_type, account_holder, dni_ruc }`
- **Respuesta esperada:**
  ```json
  { "accounts": [
    { "id": 8, "alias": "Cambio J - Banco de Chile", "bank_name": "Banco de Chile", "account_number": "00012345678", "account_type": "ahorro" },
    { "id": 9, "alias": "Cambio J - Banco Estado CL", "bank_name": "Banco Estado", "account_number": "98765432100", "account_type": "corriente" }
  ]}
  ```
- **Resultado real:** PASS ✅
- **Observaciones:** Solo devuelve cuentas del país del corredor seleccionado, asignadas al vendedor del cliente

### Caso 8 — GET /transactions/seller-accounts sin rate_id
- **Precondición:** Autenticado
- **Acción:** GET sin parámetro `rate_id`
- **Respuesta esperada:** JSON `{ "accounts": [] }`
- **Resultado real:** PASS ✅

### Caso 9 — Acceso sin autenticación a endpoint AJAX
- **Precondición:** No autenticado
- **Acción:** GET `/transactions/document-types?country_id=1`
- **Respuesta esperada:** HTTP 302 → `/login` (redirect por middleware `auth`)
- **Resultado real:** PASS ✅

## Resultado global: PASS ✅

## Hallazgos y notas
- Todos los endpoints con `country_id=0` o valor inválido retornan `[]` sin error.
- La ruta `sender-banks` fue añadida en REQ-12 (commit `6f105ef`). No existía previamente.
- Los bancos se filtran por `active = true` — todos los bancos del seeder están activos por defecto.
- Métodos de pago no tienen un endpoint separado por país de destino — solo por país de origen del remitente.
