# Tarea 3.3: Rutas y métodos approve/reject en SaleController

**Estado:** ✅ COMPLETO  
**Tiempo estimado:** 2h

---

## Objetivo

Exponer los métodos `approve()` y `reject()` del modelo Sale vía HTTP, implementando validaciones de autorización y respuestas apropiadas.

---

## Rutas a crear

- `POST /sales/{sale}/approve` → `SaleController@approve`
- `POST /sales/{sale}/reject` → `SaleController@reject`

**Middleware:** `auth` (solo usuarios autenticados)

---

## Métodos del controlador

### approve(Sale $sale)
- Validar que el usuario tiene permiso (vendedor o admin)
- Llamar a `$sale->approve()`
- Capturar excepciones
- Redirigir con mensaje de éxito/error

### reject(Sale $sale)
- Validar que el usuario tiene permiso
- Llamar a `$sale->reject()`
- Capturar excepciones
- Redirigir con mensaje de éxito/error

---

## Archivos a modificar

- `routes/web.php` (agregar 2 rutas)
- `app/Http/Controllers/SaleController.php` (agregar 2 métodos)

---

## Criterios de aceptación

- [ ] Rutas creadas y registradas
- [ ] Métodos implementados en controller
- [ ] Validación de estados (try/catch)
- [ ] Mensajes flash de éxito/error
- [ ] Redirección apropiada después de acción
