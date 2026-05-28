# TEST-006 — Países, Bancos y Cuentas del Negocio (REQ-11)

## Objetivo
Verificar que el catálogo centralizado de países, bancos, tipos de documento y cuentas del negocio esté correctamente poblado y que la gestión administrativa funcione. Este fue el requisito central de REQ-11.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/CountryController.php`
  - Métodos: `index`, `store`, `show`, `update`, `toggleActive`, `storeDocumentType`, `updateDocumentType`, `toggleDocumentType`, `destroyDocumentType`, `storePaymentMethod`, `updatePaymentMethod`, `togglePaymentMethod`, `destroyPaymentMethod`
- **Controlador:** `app/Http/Controllers/BankController.php` — métodos: `store`, `update`, `toggleActive`
- **Controlador:** `app/Http/Controllers/BusinessAccountController.php` — métodos: `store`, `update`, `toggleActive`, `assign`, `unassign`
- **Vista:** `resources/views/countries/index.blade.php`
- **Vista:** `resources/views/countries/show.blade.php`
- **Vista:** `resources/views/countries/_card.blade.php`
- **Modelos:** `Country`, `Bank`, `DocumentType`, `PaymentMethod`, `BusinessAccount`
- **Rutas:** `countries.*`, `banks.*`, `business-accounts.*`, `payment-methods.*`, `document-types.*`
- **Seeders:** `CountryBankSeeder`, `DocumentTypeSeeder`, `PaymentMethodSeeder`, `MultiCorridorSeeder`, `MoreCorridorsSeeder`

## Estado actual de datos (BD local)

### Países activos
| ID | País | Código | Rol | Bancos | Docs | Métodos Pago |
|----|------|--------|-----|--------|------|-------------|
| 1 | 🇵🇪 Perú | PE | origin | 7 | 3 | 2 |
| 2 | 🇻🇪 Venezuela | VE | destination | 8 | 4 | 0* |
| 3 | 🇨🇱 Chile | CL | origin | 7 | 2 | 1 |
| 4 | 🇨🇴 Colombia | CO | origin | 7 | 3 | 3 |
| 5 | 🇦🇷 Argentina | AR | origin | 7 | 3 | 2 |
| 6 | 🇧🇷 Brasil | BR | origin | 7 | 3 | 2 |

*Venezuela es destino — los métodos de pago se refieren al remitente, no al receptor.

### Cuentas del negocio (BusinessAccount) por país
| País | Banco | N° de cuenta | Tipo | Asignada a vendedores |
|------|-------|-------------|------|----------------------|
| Perú | BCP | (demo) | ahorro | 4 vendedores |
| Perú | Interbank | (demo) | ahorro | 4 vendedores |
| Perú | Scotiabank | (demo) | corriente | 4 vendedores |
| Chile | Banco de Chile | 00012345678 | ahorro | 4 vendedores |
| Chile | Banco Estado | 98765432100 | corriente | 4 vendedores |
| Colombia | Bancolombia | 12345678901 | ahorro | 4 vendedores |
| Colombia | Davivienda | 09876543210 | corriente | 4 vendedores |
| Argentina | Banco Nación | 123456789001 | ahorro | 4 vendedores |
| Argentina | Banco Galicia | 0540123456789 | corriente | 4 vendedores |
| Brasil | Banco do Brasil | 12345-6 | ahorro | 4 vendedores |
| Brasil | Itaú Unibanco | 654321-0 | corriente | 4 vendedores |

## Casos de prueba

### Caso 1 — Listar países (GET /countries)
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /countries`
- **Respuesta esperada:** HTTP 200, 6 países activos visibles en tarjetas
- **Resultado real:** PASS ✅

### Caso 2 — Ver detalle de país con todos sus datos (GET /countries/{id})
- **Precondición:** País Argentina (id=5) existe
- **Acción:** `GET /countries/5`
- **Respuesta esperada:** 
  - 7 bancos argentinos
  - 3 tipos de documento (DNI, CUIT, CUIL)
  - 2 métodos de pago (Transferencia, CVU/Alias)
  - 2 cuentas del negocio asignadas
- **Resultado real:** PASS ✅

### Caso 3 — Verificar que cada país origen tiene sus cuentas del negocio
- **Precondición:** Seeder ejecutado
- **Acción:** Query `BusinessAccount::with('country')->get()`
- **Respuesta esperada:** 11 cuentas, distribuidas: 3 Perú + 2 Chile + 2 Colombia + 2 Argentina + 2 Brasil
- **Resultado real:** PASS ✅

### Caso 4 — Todas las cuentas están asignadas a los 4 vendedores
- **Precondición:** 4 vendedores registrados (VEND001-VEND004)
- **Acción:** Verificar tabla pivot `business_account_seller`
- **Respuesta esperada:** Cada BusinessAccount tiene 4 registros en la tabla pivot
- **Resultado real:** PASS ✅
- **Observaciones:** `MoreCorridorsSeeder` usa `$account->sellers()->attach($seller->id)` con verificación de existencia previa

### Caso 5 — Activar/desactivar banco
- **Precondición:** Banco activo existe
- **Acción:** `PATCH /banks/{id}` con `{ active: false }`
- **Respuesta esperada:** Banco desactivado, ya no aparece en endpoints AJAX `/sender-banks`
- **Resultado real:** PASS ✅

### Caso 6 — Agregar tipo de documento a país
- **Precondición:** País Chile (id=3) tiene RUT y RUN
- **Acción:** `POST /countries/3/document-types` con `{ code: 'PASS', name: 'Pasaporte', placeholder: 'AB123456' }`
- **Respuesta esperada:** Nuevo tipo de doc creado para Chile
- **Resultado real:** PASS ✅

### Caso 7 — Filtrado por país activo en endpoint AJAX
- **Precondición:** `country_id=3` (Chile), 7 bancos activos
- **Acción:** `GET /transactions/sender-banks?country_id=3`
- **Respuesta esperada:** Array de 7 bancos chilenos
- **Resultado real:** PASS ✅ (verificado en TEST-003, Caso 3)

### Caso 8 — Asignar cuenta a vendedor
- **Precondición:** Cuenta del negocio existe (id=8), vendedor existe (id=2)
- **Acción:** `POST /business-accounts/8/assign` con `{ seller_id: 2 }`
- **Respuesta esperada:** Relación creada en tabla pivot
- **Resultado real:** PASS ✅

## Resultado global: PASS ✅

## Hallazgos y notas
- Venezuela (destino) no tiene métodos de pago porque esa lógica corresponde al país de origen del remitente.
- Arquitectura clave (REQ-11): `País → Bancos → Cuentas del negocio → asignadas a vendedores`. Cuando el cliente elige un corredor, el sistema filtra las cuentas por `country_id` del par seleccionado Y `seller_id` del vendedor asignado al cliente.
- Los seeders `MultiCorridorSeeder` y `MoreCorridorsSeeder` NO duplican datos — usan `firstOrCreate` en todos los modelos.
- Las cuentas se pueden gestionar desde el admin en `/countries/{id}` → pestaña "Cuentas del negocio".
