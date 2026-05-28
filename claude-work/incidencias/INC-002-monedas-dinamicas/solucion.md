# INC-002 — Solución

## Causa raíz confirmada
Etiquetas y símbolos de moneda hardcodeados ("S/.", "Soles peruanos") en vistas Blade
y stores de Alpine.js. getCurrencyPairs() ya retornaba los datos correctos pero
las vistas no los usaban.

## Archivos modificados
- database/migrations/2026_05_28_044613_add_country_id_to_currencies_table.php
- app/Models/Currency.php — relación originCountry()
- app/Http/Controllers/TransactionController.php — getCurrencyPairs() con datos de destino
- resources/views/transactions/create.blade.php — Alpine.js dinámico
- resources/views/welcome.blade.php — simulador usa to_symbol y to_name
- routes/web.php — mapa $pairs incluye campos de destino
- app/Http/Controllers/ClientDashboardController.php — eager load
- resources/views/client-dashboard.blade.php y transactions/index.blade.php

## Commits
- ea6e668 — Migration + model Currency
- af536f7 — getCurrencyPairs() actualizado
- 47703ba — create.blade.php dinámico
- f7aef00 — welcome.blade.php + routes/web.php
- 237c16d — Dashboards y transactions/index
