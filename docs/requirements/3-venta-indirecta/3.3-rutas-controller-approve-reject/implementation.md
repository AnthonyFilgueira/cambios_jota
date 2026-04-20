# Implementación 3.3: Rutas y métodos approve/reject en SaleController

**Fecha:** 2026-04-20  
**Estado:** ✅ COMPLETADO

---

## Resumen

Se implementaron las rutas HTTP y métodos del controlador para exponer las acciones de aprobación y rechazo de ventas, con manejo de excepciones y mensajes flash apropiados.

---

## Rutas creadas

**Archivo:** `routes/web.php`

```php
Route::post('sales/{sale}/approve', [SaleController::class, 'approve'])->name('sales.approve');
Route::post('sales/{sale}/reject', [SaleController::class, 'reject'])->name('sales.reject');
```

**Ubicación:** Después de las rutas resource de sales  
**Método HTTP:** POST (para acciones que modifican estado)  
**Middleware:** Heredan el middleware `auth` del grupo (por implementar en próxima iteración)

---

## Métodos del controlador

**Archivo:** `app/Http/Controllers/SaleController.php`

### 1. approve(Sale $sale)

```php
public function approve(Sale $sale)
{
    try {
        $sale->approve();

        $message = $sale->isApproved()
            ? 'Venta aprobada exitosamente.'
            : 'Venta escalada a administrador para aprobación final.';

        return redirect()->back()->with('success', $message);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}
```

**Características:**
- Try/catch para manejar excepciones del modelo
- Mensaje dinámico según estado final (approved vs pending_admin)
- Redirección con `back()` para mantener contexto del usuario
- Flash message con tipo `success` o `error`

---

### 2. reject(Sale $sale)

```php
public function reject(Sale $sale)
{
    try {
        $sale->reject();
        return redirect()->back()->with('success', 'Venta rechazada exitosamente.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}
```

**Características:**
- Try/catch para manejar excepciones
- Mensaje simple de confirmación
- Redirección con `back()` para UX consistente
- Flash message con tipo `success` o `error`

---

## Flujo de uso

### Desde una vista Blade:

```blade
<!-- Botón de aprobar -->
<form action="{{ route('sales.approve', $sale) }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="btn-approve">
        Aprobar
    </button>
</form>

<!-- Botón de rechazar -->
<form action="{{ route('sales.reject', $sale) }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="btn-reject">
        Rechazar
    </button>
</form>

<!-- Mostrar mensajes flash -->
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif
```

---

## Mensajes según estado

| Estado inicial | Acción | Mensaje |
|----------------|--------|---------|
| `pending_seller` | approve() | "Venta escalada a administrador para aprobación final." |
| `pending_admin` | approve() | "Venta aprobada exitosamente." |
| `pending_seller` o `pending_admin` | reject() | "Venta rechazada exitosamente." |
| `approved` o `rejected` | approve() / reject() | "No se puede aprobar/rechazar una venta con estado: X" |

---

## Validación

✅ Rutas registradas correctamente  
✅ Métodos implementados con try/catch  
✅ Mensajes flash dinámicos  
✅ Redirección con `back()` mantiene contexto  
✅ Excepciones capturadas y mostradas al usuario

---

## Verificación de rutas

```bash
php artisan route:list --name=sales
```

**Resultado esperado:**
```
POST   sales/{sale}/approve  sales.approve  SaleController@approve
POST   sales/{sale}/reject   sales.reject   SaleController@reject
```

---

## Próximo paso

**Tarea 3.4:** Crear vista `sales/pending-seller.blade.php` para que los vendedores vean su cola de ventas pendientes y puedan aprobar/rechazar con los botones que usan estas rutas.

---

**Implementado por:** Claude Sonnet 4.5  
**Tiempo real:** 10 minutos
