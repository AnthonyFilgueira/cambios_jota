# INC-002 — Diagnóstico

## Causa raíz
Los nombres y símbolos de moneda están hardcodeados en las vistas Blade y en el Alpine.js store.
Aunque `getCurrencyPairs()` ya retorna `from_name` y `from_symbol` correctamente,
las vistas no usan estos valores — en su lugar usan literales fijos.

## Archivos afectados

### 1. `resources/views/transactions/create.blade.php`
- Label "En Soles (PEN) *" — hardcodeado
- Símbolo "S/." en múltiples spans
- Texto "Soles peruanos" en subtítulo
- El Alpine.js store no actualiza el símbolo/nombre al cambiar el par

### 2. `resources/views/welcome.blade.php`
- Alpine.js store: `symbol: 'S/'` hardcodeado (~línea 362)
- Texto "Soles peruanos" y "Bolívares venezolanos" hardcodeados

### 3. Dashboards y wallet
- `client-dashboard.blade.php`: `S/. {{ $t->amount_pen }}`
- `owner-dashboard.blade.php`: múltiples `S/.`
- `wallet/index.blade.php`: `S/` hardcodeado

### 4. Modelo y base de datos
- `currencies.country` es un campo de texto (VARCHAR), sin FK a la tabla `countries`
- Esto impide enlazar directamente una divisa con su país para filtrar cuentas (INC-003 relacionado)

## Solución planificada
1. Agregar `country_id` FK a la tabla `currencies` (migration)
2. Añadir `from_country_id`, `to_name`, `to_symbol`, `to_code` al array que retorna `getCurrencyPairs()`
3. En `create.blade.php`: usar Alpine.js para leer `data-from-name` y `data-from-symbol` del option seleccionado y actualizar las etiquetas dinámicamente
4. En `welcome.blade.php`: bindear el símbolo desde los pares pasados por el controlador
5. En dashboards: usar `$transaction->exchangeRate->currencyPair->fromCurrency->symbol`
