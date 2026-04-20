# Implementación 7.5: Simulador Dinámico por Pares

**Fecha:** 2026-04-20  
**Estado:** ✅ COMPLETADO  
**Tiempo real:** ~1h  

---

## Resumen

Actualización del simulador público para cargar pares dinámicamente desde BD y permitir al usuario seleccionar el país de origen (Perú, Argentina, Chile).

---

## Cambios Realizados

### 1. Seeder: Pares adicionales

**Archivo:** `database/seeders/ExchangeRateSeeder.php`

**Pares creados:**
```php
PEN→VES: 173.71 VES/PEN (activo)
ARS→VES: 2.50 VES/ARS (inactivo) 
CLP→VES: 0.55 VES/CLP (inactivo)
```

**Tasas BCV (iguales para todos):**
- USD: 479.78
- EUR: 565.98

---

### 2. Controlador: Cargar pares dinámicamente

**Archivo:** `routes/web.php`

**Antes:**
```php
Route::get('/', function () {
    $rates = ExchangeRate::getActive();
    return view('welcome', compact('rates'));
});
```

**Ahora:**
```php
Route::get('/', function () {
    $rates = ExchangeRate::getActive();

    $pairs = \App\Models\ExchangeRate::with(['currencyPair.fromCurrency', 'currencyPair.toCurrency'])
        ->whereNotNull('currency_pair_id')
        ->get()
        ->map(function($rate) {
            return [
                'id' => $rate->id,
                'from_code' => $rate->currencyPair->fromCurrency->code,
                'from_name' => $rate->currencyPair->fromCurrency->name,
                'from_country' => $rate->currencyPair->fromCurrency->country,
                'from_symbol' => $rate->currencyPair->fromCurrency->symbol,
                'flag' => $rate->currencyPair->fromCurrency->flag_emoji,
                'ves_rate' => $rate->ves_rate,
                'usd_rate' => $rate->usd_rate,
                'eur_rate' => $rate->eur_rate,
                'is_active' => $rate->is_active,
            ];
        });

    return view('welcome', compact('rates', 'pairs'));
});
```

---

### 3. Vista: Simulador dinámico

**Archivo:** `resources/views/welcome.blade.php`

#### A. Selector de país origen

**Antes (hardcoded):**
```blade
<div class="bg-cj-morado-claro p-4 flex items-center justify-between">
    <span>🇵🇪 Perú (PEN)</span>
    <span>→</span>
    <span>Venezuela (VES) 🇻🇪</span>
</div>
```

**Ahora (dinámico):**
```blade
<div class="bg-cj-morado-claro p-4">
    <label>Selecciona el país de origen</label>
    <select x-model="selectedPairId" @change="cambiarPar()">
        <template x-for="pair in pairs" :key="pair.id">
            <option :value="pair.id" x-text="`${pair.flag} ${pair.from_country} (${pair.from_code})`"></option>
        </template>
    </select>

    <!-- Indicador visual de ruta -->
    <div class="flex items-center justify-center gap-3">
        <span x-text="currentPair.flag"></span>
        <span x-text="currentPair.from_country"></span>
        <span>→</span>
        <span>Venezuela 🇻🇪</span>
    </div>
</div>
```

#### B. Label dinámico según moneda

**Antes:**
```blade
<label>En soles (Directo)</label>
<span class="symbol">S/</span>
```

**Ahora:**
```blade
<label>
    <span x-text="`En ${currentPair.from_name} (Directo)`"></span>
</label>
<span class="symbol" x-text="currentPair.from_symbol"></span>
```

**Ejemplos:**
- Perú: "En Sol Peruano (Directo)" → S/
- Argentina: "En Peso Argentino (Directo)" → $
- Chile: "En Peso Chileno (Directo)" → $

#### C. Resultado dinámico

**Antes:**
```blade
<div>S/ <span x-text="penEnviar"></span></div>
<div>Soles peruanos</div>
<div>1 PEN = <span x-text="tasas.ves"></span> VES</div>
```

**Ahora:**
```blade
<div>
    <span x-text="currentPair.from_symbol"></span> 
    <span x-text="montoEnviar"></span>
</div>
<div x-text="currentPair.from_name"></div>
<div>
    1 <span x-text="currentPair.from_code"></span> = 
    <span x-text="currentPair.ves_rate"></span> VES
</div>
```

---

### 4. Lógica Alpine.js actualizada

**Antes:**
```javascript
{
    tasas: { usd, eur, ves },
    inputPEN: '',
    penEnviar: 0,
    vesRecibir: 0,
    
    calcularDesdePEN() {
        this.penEnviar = inputPEN;
        this.vesRecibir = inputPEN * tasas.ves;
    }
}
```

**Ahora:**
```javascript
{
    pairs: @json($pairs),           // Todos los pares disponibles
    selectedPairId: defaultPairId,  // Par seleccionado
    currentPair: {},                // Par actual
    
    inputOrigen: '',                // Input dinámico
    montoEnviar: 0,
    vesRecibir: 0,
    
    init() {
        this.cambiarPar();          // Inicializar con par por defecto
    },
    
    cambiarPar() {
        this.currentPair = this.pairs.find(p => p.id == this.selectedPairId);
        this.limpiarInputs();
    },
    
    calcularDesdeOrigen() {
        this.montoEnviar = inputOrigen;
        this.vesRecibir = inputOrigen * currentPair.ves_rate;
    },
    
    calcularDesdeUSD() {
        const vesIntermedios = usd * currentPair.usd_rate;
        this.montoEnviar = vesIntermedios / currentPair.ves_rate;
        this.vesRecibir = vesIntermedios;
    }
}
```

---

## Funcionamiento

### Flujo de usuario:

1. **Usuario carga la página**
   - Se cargan 3 pares desde BD (PEN, ARS, CLP)
   - Por defecto se selecciona el par activo (PEN→VES)

2. **Usuario selecciona país**
   - Cambia selector a "🇦🇷 Argentina (ARS)"
   - → Label cambia a "En Peso Argentino (Directo)"
   - → Símbolo cambia a "$"
   - → Tasa cambia a "1 ARS = 2.50 VES"

3. **Usuario ingresa monto**
   - Escribe "100" en input
   - → Tú envías: $ 100.00 (Peso Argentino)
   - → Recibe: Bs. 250.00 (1 ARS = 2.50 VES)

4. **Usuario cambia a Chile**
   - Selecciona "🇨🇱 Chile (CLP)"
   - → Label: "En Peso Chileno (Directo)"
   - → Tasa: "1 CLP = 0.55 VES"
   - → Inputs se limpian automáticamente

---

## Datos de prueba

| País | Código | Tasa VES | Ejemplo |
|------|--------|----------|---------|
| 🇵🇪 Perú | PEN | 173.71 | 100 PEN = 17,371 VES |
| 🇦🇷 Argentina | ARS | 2.50 | 100 ARS = 250 VES |
| 🇨🇱 Chile | CLP | 0.55 | 100 CLP = 55 VES |

**Tasas BCV (referencias):**
- USD: 479.78
- EUR: 565.98

---

## Archivos modificados

| Archivo | Cambio |
|---------|--------|
| `database/seeders/ExchangeRateSeeder.php` | Agregar pares ARS y CLP |
| `routes/web.php` | Cargar pares en ruta principal |
| `resources/views/welcome.blade.php` | Selector dinámico + lógica Alpine |

**Total:** 3 archivos

---

## Testing pendiente

- [ ] Cargar simulador en navegador
- [ ] Seleccionar Perú → verificar S/ y tasa 173.71
- [ ] Seleccionar Argentina → verificar $ y tasa 2.50
- [ ] Seleccionar Chile → verificar $ y tasa 0.55
- [ ] Ingresar monto y verificar cálculos
- [ ] Probar inputs USD y EUR con cada par

---

## Beneficios

✅ **Escalable:** Fácil agregar nuevos pares desde admin  
✅ **Dinámico:** No requiere cambios de código para nuevos países  
✅ **UX clara:** Usuario ve inmediatamente el país y moneda  
✅ **Mantenible:** Tasas centralizadas en BD  

---

**Implementado por:** Claude Sonnet 4.5  
**Fecha:** 2026-04-20 19:15  
**Commit:** REQ 7.5: Simulador dinámico con selector de países
