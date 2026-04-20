# Implementación 3.2: Métodos approve() y reject() en modelo Sale

**Fecha:** 2026-04-20  
**Estado:** ✅ COMPLETADO

---

## Resumen

Se implementaron los métodos `approve()` y `reject()` en el modelo `Sale` para gestionar las transiciones de estado del workflow de aprobación multi-nivel. Además, se agregaron métodos helper para verificar estados y permisos.

---

## Métodos implementados

### 1. approve()
**Lógica de transición:**
- `pending_seller` → `pending_admin` (vendedor aprueba, escala a admin)
- `pending_admin` → `approved` (admin aprueba, finaliza workflow)
- Otros estados: lanza excepción

```php
public function approve()
{
    if ($this->approval_status === 'pending_seller') {
        $this->approval_status = 'pending_admin';
        $this->save();
        return true;
    }

    if ($this->approval_status === 'pending_admin') {
        $this->approval_status = 'approved';
        $this->save();
        return true;
    }

    throw new \Exception("No se puede aprobar una venta con estado: {$this->approval_status}");
}
```

---

### 2. reject()
**Lógica de transición:**
- `pending_seller` → `rejected` (vendedor rechaza)
- `pending_admin` → `rejected` (admin rechaza)
- Otros estados: lanza excepción

```php
public function reject()
{
    if (in_array($this->approval_status, ['pending_seller', 'pending_admin'])) {
        $this->approval_status = 'rejected';
        $this->save();
        return true;
    }

    throw new \Exception("No se puede rechazar una venta con estado: {$this->approval_status}");
}
```

---

## Métodos helper agregados

### Verificadores de estado
- `isPendingSeller()`: Retorna true si estado = 'pending_seller'
- `isPendingAdmin()`: Retorna true si estado = 'pending_admin'
- `isApproved()`: Retorna true si estado = 'approved'
- `isRejected()`: Retorna true si estado = 'rejected'

### Verificadores de permisos
- `canBeApproved()`: Verifica si la venta puede ser aprobada
- `canBeRejected()`: Verifica si la venta puede ser rechazada

**Uso en controladores y vistas:**
```php
// En controlador
if ($sale->canBeApproved()) {
    $sale->approve();
}

// En Blade
@if($sale->isPendingSeller())
    <span class="badge badge-warning">Pendiente Vendedor</span>
@endif
```

---

## Flujo de estados completo

```
pending_seller
    ├─ approve() → pending_admin
    │                 ├─ approve() → approved (FINAL)
    │                 └─ reject() → rejected (FINAL)
    └─ reject() → rejected (FINAL)
```

---

## Validación de seguridad

✅ No se puede aprobar una venta ya aprobada  
✅ No se puede rechazar una venta ya rechazada  
✅ No se puede aprobar una venta ya rechazada  
✅ No se puede rechazar una venta ya aprobada  
✅ Excepciones claras para estados inválidos

---

## Archivo modificado

**`app/Models/Sale.php`**
- 8 métodos agregados
- Lógica de transiciones implementada
- Sin breaking changes en métodos existentes

---

## Próximo paso

**Tarea 3.3:** Implementar rutas y métodos en `SaleController` para exponer `approve()` y `reject()` vía HTTP, con validaciones de autorización.

---

**Implementado por:** Claude Sonnet 4.5  
**Tiempo real:** 8 minutos
