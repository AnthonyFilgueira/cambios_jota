#!/usr/bin/env php
<?php

/**
 * Script de Verificación Automática - REQ 7
 *
 * Este script verifica el estado básico de las funcionalidades
 * implementadas en REQ 7 (Consola de Tasas).
 *
 * Uso: ./vendor/bin/sail artisan tinker < test-req7.php
 * O:   php test-req7.php (dentro del contenedor)
 */

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "  TEST AUTOMÁTICO - REQ 7: CONSOLA DE TASAS\n";
echo "════════════════════════════════════════════════════════════════\n\n";

$errors = 0;
$warnings = 0;
$passed = 0;

// Helper function
function test($name, $callback) {
    global $errors, $warnings, $passed;

    echo "🧪 Testing: $name\n";

    try {
        $result = $callback();

        if ($result === true) {
            echo "   ✅ PASS\n\n";
            $passed++;
        } elseif ($result === null) {
            echo "   ⚠️  WARNING: Verificación manual requerida\n\n";
            $warnings++;
        } else {
            echo "   ❌ FAIL: $result\n\n";
            $errors++;
        }
    } catch (Exception $e) {
        echo "   ❌ ERROR: " . $e->getMessage() . "\n\n";
        $errors++;
    }
}

echo "──────────────────────────────────────────────────────────────────\n";
echo " 1. MODELOS Y BASE DE DATOS\n";
echo "──────────────────────────────────────────────────────────────────\n\n";

test("ExchangeRate model exists", function() {
    return class_exists('App\Models\ExchangeRate');
});

test("CurrencyPair model exists", function() {
    return class_exists('App\Models\CurrencyPair');
});

test("Currency model exists", function() {
    return class_exists('App\Models\Currency');
});

test("exchange_rates table has data", function() {
    $count = \App\Models\ExchangeRate::count();
    if ($count === 0) {
        return "No hay tasas en la BD. Ejecutar: sail artisan db:seed --class=ExchangeRateSeeder";
    }
    echo "   → $count tasas encontradas\n";
    return true;
});

test("currency_pairs table has data", function() {
    $count = \App\Models\CurrencyPair::count();
    if ($count === 0) {
        return "No hay pares en la BD. Ejecutar: sail artisan db:seed";
    }
    echo "   → $count pares encontrados\n";
    return true;
});

test("currencies table has data", function() {
    $count = \App\Models\Currency::count();
    if ($count === 0) {
        return "No hay divisas en la BD. Ejecutar: sail artisan db:seed";
    }
    echo "   → $count divisas encontradas\n";
    return true;
});

echo "──────────────────────────────────────────────────────────────────\n";
echo " 2. ESTRUCTURA DE DATOS\n";
echo "──────────────────────────────────────────────────────────────────\n\n";

test("ExchangeRate has required fields", function() {
    $rate = \App\Models\ExchangeRate::first();
    if (!$rate) return "No hay tasas para verificar";

    $required = ['currency_pair_id', 'ves_rate', 'usd_rate', 'eur_rate', 'is_active'];
    foreach ($required as $field) {
        if (!isset($rate->$field)) {
            return "Campo faltante: $field";
        }
    }

    echo "   → Campos: currency_pair_id, ves_rate, usd_rate, eur_rate, is_active\n";
    return true;
});

test("ExchangeRate NO tiene campos de margen", function() {
    $rate = \App\Models\ExchangeRate::first();
    if (!$rate) return "No hay tasas para verificar";

    $deprecated = ['margin_usd', 'margin_eur', 'margin_purchase', 'margin_sale', 'margin_cash', 'margin_transfer'];
    foreach ($deprecated as $field) {
        if (isset($rate->$field)) {
            return "Campo obsoleto encontrado: $field (debió eliminarse en REQ 7.3)";
        }
    }

    echo "   → Campos de margen eliminados correctamente\n";
    return true;
});

test("CurrencyPair tiene relación con currencies", function() {
    $pair = \App\Models\CurrencyPair::first();
    if (!$pair) return "No hay pares para verificar";

    if (!$pair->fromCurrency || !$pair->toCurrency) {
        return "Relaciones fromCurrency/toCurrency no funcionan";
    }

    echo "   → {$pair->fromCurrency->code} → {$pair->toCurrency->code}\n";
    return true;
});

echo "──────────────────────────────────────────────────────────────────\n";
echo " 3. LÓGICA DE NEGOCIO\n";
echo "──────────────────────────────────────────────────────────────────\n\n";

test("ExchangeRate::getActive() funciona", function() {
    $active = \App\Models\ExchangeRate::getActive();
    if (!$active) {
        return "No hay tasa activa";
    }
    echo "   → Tasa activa: " . ($active->pair_name ?? 'Default') . "\n";
    return true;
});

test("ExchangeRate::getActiveForPair() funciona", function() {
    $pair = \App\Models\CurrencyPair::first();
    if (!$pair) return "No hay pares para verificar";

    $active = \App\Models\ExchangeRate::getActiveForPair($pair->id);
    if (!$active) {
        echo "   → No hay tasa activa para par {$pair->id} (puede ser normal)\n";
        return null; // warning
    }

    echo "   → Tasa activa para {$pair->display_name}: {$active->ves_rate}\n";
    return true;
});

test("ExchangeRate->calculateVesAmount() calcula correctamente", function() {
    $rate = \App\Models\ExchangeRate::where('is_active', true)->first();
    if (!$rate) return "No hay tasa activa para probar";

    $amount = 100;
    $result = $rate->calculateVesAmount($amount);
    $expected = $amount * $rate->ves_rate;

    if (abs($result - $expected) > 0.01) {
        return "Cálculo incorrecto: esperado $expected, obtenido $result";
    }

    echo "   → $amount × {$rate->ves_rate} = $result VES ✓\n";
    return true;
});

test("Solo una tasa activa por par", function() {
    $pairs = \App\Models\CurrencyPair::all();

    foreach ($pairs as $pair) {
        $activesCount = \App\Models\ExchangeRate::where('currency_pair_id', $pair->id)
            ->where('is_active', true)
            ->count();

        if ($activesCount > 1) {
            return "Par {$pair->display_name} tiene $activesCount tasas activas (debe ser 1)";
        }
    }

    echo "   → Regla de exclusividad verificada\n";
    return true;
});

echo "──────────────────────────────────────────────────────────────────\n";
echo " 4. RUTAS Y CONTROLADORES\n";
echo "──────────────────────────────────────────────────────────────────\n\n";

test("Ruta exchange_rates.index existe", function() {
    return \Route::has('exchange_rates.index');
});

test("Ruta exchange_rates.create existe", function() {
    return \Route::has('exchange_rates.create');
});

test("Ruta exchange_rates.edit existe", function() {
    return \Route::has('exchange_rates.edit');
});

test("ExchangeRateController existe", function() {
    return class_exists('App\Http\Controllers\ExchangeRateController');
});

echo "──────────────────────────────────────────────────────────────────\n";
echo " 5. VISTAS\n";
echo "──────────────────────────────────────────────────────────────────\n\n";

test("Vista exchange_rates/index.blade.php existe", function() {
    return file_exists(resource_path('views/exchange_rates/index.blade.php'));
});

test("Vista exchange_rates/create.blade.php existe", function() {
    return file_exists(resource_path('views/exchange_rates/create.blade.php'));
});

test("Vista exchange_rates/edit.blade.php existe", function() {
    return file_exists(resource_path('views/exchange_rates/edit.blade.php'));
});

test("Vista welcome.blade.php existe (simulador)", function() {
    return file_exists(resource_path('views/welcome.blade.php'));
});

test("welcome.blade.php contiene función simulador()", function() {
    $content = file_get_contents(resource_path('views/welcome.blade.php'));
    if (!str_contains($content, 'function simulador()')) {
        return "Función simulador() no encontrada en welcome.blade.php";
    }
    echo "   → Simulador Alpine.js presente\n";
    return true;
});

test("welcome.blade.php contiene selector de países", function() {
    $content = file_get_contents(resource_path('views/welcome.blade.php'));
    $hasCountries = str_contains($content, '🇵🇪') &&
                    str_contains($content, '🇦🇷') &&
                    str_contains($content, '🇨🇱');

    if (!$hasCountries) {
        return "Selector de países no encontrado (debe tener 🇵🇪 🇦🇷 🇨🇱)";
    }

    echo "   → Selector de países: Perú, Argentina, Chile ✓\n";
    return true;
});

echo "\n";
echo "════════════════════════════════════════════════════════════════\n";
echo "  RESUMEN\n";
echo "════════════════════════════════════════════════════════════════\n\n";

$total = $passed + $errors + $warnings;

echo "  ✅ Tests pasados:  $passed\n";
echo "  ❌ Tests fallados: $errors\n";
echo "  ⚠️  Warnings:      $warnings\n";
echo "  ──────────────────────────\n";
echo "  📊 Total:          $total\n\n";

if ($errors > 0) {
    echo "  ❌ HAY ERRORES QUE CORREGIR\n\n";
    exit(1);
} elseif ($warnings > 0) {
    echo "  ⚠️  VERIFICACIÓN MANUAL REQUERIDA\n\n";
    echo "  Ejecutar checklist manual en:\n";
    echo "  docs/requirements/07-consola-tasas/7.6-testing-completo/testing_checklist.md\n\n";
    exit(0);
} else {
    echo "  ✅ TODOS LOS TESTS AUTOMÁTICOS PASARON\n\n";
    echo "  Continuar con checklist manual en:\n";
    echo "  docs/requirements/07-consola-tasas/7.6-testing-completo/testing_checklist.md\n\n";
    exit(0);
}
