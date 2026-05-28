# Resumen de Tests — Cambio J
> Rama: `feat/REQ-12-multi-mejoras` | Fecha: 2026-05-28 | PHP Suite: 38/38 PASS

## Resultado global del suite PHP

```
Tests:    38 passed (84 assertions)
Duration: ~21s
```

Antes de las correcciones de esta sesión: **2 tests fallaban**. Ahora: **38/38 PASS**.

---

## Índice de Tests

| Test | Área | Resultado | Archivo detallado |
|------|------|-----------|-------------------|
| TEST-001 | Autenticación (Login / Registro / Roles) | PASS ✅ | `TEST-001-autenticacion/informe.md` |
| TEST-002 | Formulario de Transacción Dinámico (Alpine.js) | PASS ✅ | `TEST-002-formulario-transaccion-dinamico/informe.md` |
| TEST-003 | Endpoints AJAX de Transacciones | PASS ✅ | `TEST-003-endpoints-ajax-transacciones/informe.md` |
| TEST-004 | Flujo Completo de Transacción (ciclo de vida) | PASS ✅ | `TEST-004-flujo-completo-transaccion/informe.md` |
| TEST-005 | Gestión de Tasas de Cambio | PASS ✅ | `TEST-005-gestion-tasas-de-cambio/informe.md` |
| TEST-006 | Países, Bancos y Cuentas del Negocio (REQ-11) | PASS ✅ | `TEST-006-paises-bancos-cuentas-negocio/informe.md` |
| TEST-007 | Panel del Vendedor (Bandeja / Aprobar / Observar) | PASS ✅ | `TEST-007-panel-vendedor-bandeja/informe.md` |
| TEST-008 | Wallet y Comisiones del Vendedor | PASS ✅ | `TEST-008-wallet-y-comisiones/informe.md` |
| TEST-009 | Reportes y Exportación (CSV / PDF) | PASS ✅ | `TEST-009-reportes-y-exportacion/informe.md` |
| TEST-010 | Multi-Corredor y Nuevos Países | PASS ✅ | `TEST-010-multi-corredor-nuevos-paises/informe.md` |

---

## Correcciones realizadas durante los tests

### 1. Bug: `senderBanks` no declarado en Alpine state (TEST-002)
**Commit:** `9f10be0`
- Añadido `senderBanks: []` y `loadingSenderBanks: false` a `transactionForm()`
- Cambiado `sellerAccountsDisplay: @json($sellerAccountsMapped)` → `sellerAccountsDisplay: []`
- Añadido `x-show="selectedRateId"` y placeholder en sección de cuentas

### 2. Bug: RegistrationTest fallaba (TEST-001)
**Commit:** `5889591`
- El rol `cliente` no existe en la BD de tests (RefreshDatabase no ejecuta seeders)
- Los campos `vendor_code` y `phone` son requeridos desde REQ-12 pero no estaban en el test
- **Fix:** `Role::firstOrCreate(['name' => 'cliente', ...])` + parámetros correctos

### 3. Bug: ExampleTest (welcome.blade.php) crasheaba con BD vacía (TEST-005)
**Commit:** `5889591`
- `collect($pairs)->first()['id']` cuando `$pairs` está vacío lanzaba `ErrorException` (PHP convierte null['id'] en warning → error en tests)
- **Fix:** `(collect($pairs)->firstWhere('is_active', true) ?? collect($pairs)->first())['id'] ?? 0`

### 4. Fix MoreCorridorsSeeder: columna `active` vs `is_active` (TEST-006, TEST-010)
**Commit:** `548d263`
- La tabla `business_accounts` usa `active` (no `is_active`)
- El seeder usaba el nombre incorrecto → `SQLSTATE[42S22]: Column not found`

---

## Estado de la BD en local (post-seeder)

| Entidad | Cantidad |
|---------|---------|
| Países activos | 6 (PE, VE, CL, CO, AR, BR) |
| Bancos activos totales | 43 |
| Tipos de documento | 15 |
| Métodos de pago | 10 |
| Cuentas del negocio | 11 |
| Tasas de cambio activas | 10 |
| Usuarios | 13 |
| Vendedores | 4 |
| Transacciones | 2 (1 completed, 1 pending) |

---

## Credenciales para pruebas manuales

| Rol | Email | Contraseña |
|-----|-------|-----------|
| super-admin | admin@cambiosj.com | (ver CREDENCIALES.md) |
| super-admin | cambiosjottaa@innodite.com | CambiosJota2026! |
| vendedor | pedro.martinez@cambiosj.com | (ver CREDENCIALES.md) |
| cliente | juan.perez@gmail.com | (ver CREDENCIALES.md) |

---

## Cómo ejecutar el suite completo

```bash
# Todos los tests PHP
./vendor/bin/sail artisan test

# Test específico
./vendor/bin/sail artisan test --filter=RegistrationTest

# Verificar datos en BD
./vendor/bin/sail artisan tinker
```

---

## Notas arquitectónicas importantes

1. **Alpine.js v3 + scopes anidados:** `$watch()` en `x-data` hijo NO observa propiedades del padre → todos los watchers en `transactionForm()` padre.
2. **Blade `@json()` con chaining:** `@json(collect()->map(...)->values())` falla con parser de Blade (cuenta paréntesis). Siempre pre-computar en bloque `@php`.
3. **RefreshDatabase en tests:** No ejecuta seeders → roles Spatie no existen → crear con `Role::firstOrCreate()` en `beforeEach`.
4. **Seeder idempotente:** `firstOrCreate` en todos los seeders → seguro ejecutar múltiples veces.
5. **Monedas vinculadas a países:** La lógica de qué cuentas mostrar depende de `CurrencyPair → from_currency → country_id`. Si una moneda no tiene `country_id` → no se filtran cuentas.
