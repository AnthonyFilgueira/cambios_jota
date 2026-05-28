# INC-003 — Solución

## Causa raíz confirmada
El controlador cargaba todas las cuentas sin filtrar por país. No existía la cadena
ExchangeRate → fromCurrency → country_id. No había endpoint AJAX para refrescar cuentas.

## Archivos modificados
- database/migrations/2026_05_28_045353_link_currencies_to_countries.php
- app/Http/Controllers/TransactionController.php — getSellerAccounts()
- routes/web.php — GET /transactions/seller-accounts
- resources/views/transactions/create.blade.php — sección cuentas reactiva Alpine.js

## Commits
- fb18f48 — Endpoint AJAX + ruta
- 4f41827 — Alpine.js AJAX en create.blade.php
