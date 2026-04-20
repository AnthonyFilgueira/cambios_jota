# Implementación 4.3: Funcionalidad "Observar Venta"

## Archivos modificados

### 1. Controller: `app/Http/Controllers/SaleController.php`

```php
public function observe(Request $request, Sale $sale)
{
    $request->validate([
        'observation' => 'required|string|min:10|max:1000',
    ]);

    try {
        $sale->observe($request->observation);
        return redirect()->back()->with('success', 'Venta devuelta al vendedor con observación.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}
```

### 2. Rutas: `routes/web.php`

```php
Route::post('sales/{sale}/observe', [SaleController::class, 'observe'])->name('sales.observe');
```

### 3. Vista: `resources/views/sales/pending-admin.blade.php`

**Alpine.js data:**
```html
<tr x-data="{ showConfirm: false, action: '', showObserve: false, observation: '' }">
```

**Botón "Solicitar Info":**
```html
<button
    @click="showObserve = true"
    class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-white bg-orange-500 hover:bg-orange-600 transition-colors shadow-sm"
>
    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    Solicitar Info
</button>
```

**Modal completo:**
```html
<div
    x-show="showObserve"
    x-cloak
    @click.away="showObserve = false"
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-900 opacity-75"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
            <h3 class="text-lg font-semibold mb-4 text-orange-600">Solicitar Información Adicional</h3>
            
            <form action="{{ route('sales.observe', $sale) }}" method="POST">
                @csrf
                <textarea
                    name="observation"
                    x-model="observation"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                    required
                    minlength="10"
                    maxlength="1000"
                ></textarea>
                
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 rounded-md"
                    :disabled="observation.length < 10"
                >
                    Enviar Observación
                </button>
            </form>
        </div>
    </div>
</div>
```

---

## Validaciones

**Cliente (Alpine.js):**
- Botón submit deshabilitado si `observation.length < 10`
- Clase `opacity-50 cursor-not-allowed` cuando está deshabilitado

**Servidor (Laravel):**
- `required`: No puede estar vacío
- `string`: Debe ser texto
- `min:10`: Mínimo 10 caracteres
- `max:1000`: Máximo 1000 caracteres

---

## Auto-logging

El Observer `SaleObserver` detecta automáticamente el cambio de estado y registra:
```php
SaleLog::create([
    'sale_id' => $sale->id,
    'user_id' => Auth::id(),
    'action' => 'observed',
    'old_status' => 'pending_admin',
    'new_status' => 'observed',
    'comment' => $sale->admin_observation,
]);
```

---

**Fecha:** 2026-04-20
