# INC-003 — Diagnóstico

## Causa raíz
El controlador carga TODAS las cuentas activas del vendedor (`$seller->businessAccounts->where('active', true)`)
sin aplicar ningún filtro por país. Además, no existe un endpoint AJAX para recargar
las cuentas cuando el usuario cambia el par de divisa en el formulario.

## Cadena de datos que debe existir
```
ExchangeRate → CurrencyPair → fromCurrency → country_id → Country
                                                              ↓
                                                   filtrar BusinessAccount por country_id
```

## Problema adicional
La tabla `currencies` no tiene `country_id` FK hacia `countries`, por lo que no es posible
hacer el join directo. Este es el mismo problema identificado en INC-002.

## Archivos afectados

### 1. `app/Http/Controllers/TransactionController.php`
- Método `create()` (~línea 107): carga `$sellerAccounts` sin filtro de país
- No existe método para retornar cuentas filtradas por exchange_rate_id

### 2. `resources/views/transactions/create.blade.php`
- Las cuentas se renderizan una sola vez al cargar la página (Blade server-side)
- No hay lógica de AJAX al cambiar el par de divisa

### 3. `routes/web.php`
- No existe ruta para el endpoint AJAX de cuentas filtradas

## Solución planificada
1. (Depende de INC-002 TASK-001) Una vez que `currencies` tenga `country_id`, la cadena queda completa
2. Nuevo método `getSellerAccounts()` en `TransactionController`:
   - Recibe: `seller_code` + `exchange_rate_id`
   - Retorna: JSON con cuentas del vendedor filtradas por el país de la divisa de origen
3. Nueva ruta: `GET /transactions/seller-accounts`
4. En `create.blade.php`: Al cambiar el par de divisa, hacer fetch a este endpoint y actualizar la lista con Alpine.js
