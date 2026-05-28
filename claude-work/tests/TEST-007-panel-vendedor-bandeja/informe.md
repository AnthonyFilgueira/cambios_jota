# TEST-007 — Panel del Vendedor (Bandeja / Aprobar / Observar / Denegar)

## Objetivo
Verificar que el panel del vendedor funcione correctamente: bandeja de transacciones, visualización de solicitudes individuales, y las acciones de aprobar, observar (solicitar corrección) y denegar.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/SellerTransactionController.php`
  - Métodos: `bandeja`, `show`, `miCodigo`, `approve`, `observe`, `deny`
- **Vista:** `resources/views/seller/bandeja.blade.php`
- **Vista:** `resources/views/seller/solicitud-show.blade.php`
- **Vista:** `resources/views/seller/mi-codigo.blade.php`
- **Modelo:** `app/Models/Transaction.php`, `app/Models/Seller.php`
- **Rutas:**
  - `GET /seller/bandeja` → `seller.bandeja`
  - `GET /seller/solicitud/{id}` → `seller.solicitud.show`
  - `GET /seller/mi-codigo` → `seller.mi-codigo`
  - `POST /seller/solicitud/{id}/approve` → `seller.solicitud.approve`
  - `POST /seller/solicitud/{id}/observe` → `seller.solicitud.observe`
  - `POST /seller/solicitud/{id}/deny` → `seller.solicitud.deny`
- **Middleware:** `role:vendedor` — solo vendedores pueden acceder

## Casos de prueba

### Caso 1 — Acceso a la bandeja del vendedor
- **Precondición:** Autenticado como `pedro.martinez@cambiosj.com` (rol: vendedor)
- **Acción:** `GET /seller/bandeja`
- **Respuesta esperada:** HTTP 200, lista de transacciones `pending` y `observed` asignadas a este vendedor
- **Resultado real:** PASS ✅
- **Observaciones:** La bandeja aparece también en el link del menú de navegación (`/seller/bandeja`)

### Caso 2 — Ver detalle de solicitud
- **Precondición:** TX#2 en `pending` asignada a Pedro Martínez
- **Acción:** `GET /seller/solicitud/2`
- **Respuesta esperada:** HTTP 200, datos completos de la transacción (cliente, monto, comprobante, datos del receptor)
- **Resultado real:** PASS ✅

### Caso 3 — Aprobar transacción
- **Precondición:** TX en `pending`, vendedor correcto autenticado
- **Acción:** `POST /seller/solicitud/{id}/approve`
- **Datos de entrada:** ninguno (solo CSRF)
- **Respuesta esperada:** HTTP 302 → `/seller/bandeja`, `status=processing`
- **Resultado real:** PASS ✅

### Caso 4 — Observar transacción (solicitar corrección al cliente)
- **Precondición:** TX en `pending`, vendedor autenticado
- **Acción:** `POST /seller/solicitud/{id}/observe`
- **Datos de entrada:** `{ observation: "El comprobante no es legible. Favor resubir." }`
- **Respuesta esperada:** HTTP 302, `status=observed`, nota guardada en `observation`, cliente notificado
- **Resultado real:** PASS ✅
- **Observaciones:** El cliente verá un banner naranja en el formulario de edición con el texto de observación

### Caso 5 — Denegar transacción
- **Precondición:** TX en `pending` o `observed`, vendedor autenticado
- **Acción:** `POST /seller/solicitud/{id}/deny`
- **Datos de entrada:** `{ reason: "Datos del receptor incorrectos" }` (opcional)
- **Respuesta esperada:** HTTP 302, `status=cancelled`
- **Resultado real:** PASS ✅

### Caso 6 — Vendedor no puede ver solicitudes de otro vendedor
- **Precondición:** TX#2 asignada a VEND001, autenticado como VEND002
- **Acción:** `GET /seller/solicitud/2`
- **Respuesta esperada:** HTTP 403 o HTTP 302 → `/seller/bandeja`
- **Resultado real:** PASS ✅

### Caso 7 — Mi código de vendedor
- **Precondición:** Autenticado como vendedor
- **Acción:** `GET /seller/mi-codigo`
- **Respuesta esperada:** HTTP 200, código del vendedor visible (ej: VEND001), instrucciones para compartir
- **Resultado real:** PASS ✅
- **Observaciones:** Los clientes se registran con este código para ser asignados al vendedor

### Caso 8 — Redirección automática al dashboard → bandeja
- **Precondición:** Autenticado como vendedor
- **Acción:** `GET /dashboard`
- **Respuesta esperada:** HTTP 302 → `/seller/bandeja`
- **Resultado real:** PASS ✅

## Resultado global: PASS ✅

## Hallazgos y notas
- Los modales de Aprobar/Observar/Denegar están fuera del scope `x-data` correcto (fix commit `903f77b`). Esto se resolvió moviendo los modales al scope correcto en `solicitud-show.blade.php`.
- La bandeja filtra por `seller_id` del vendedor autenticado → vendedores solo ven sus propias solicitudes.
- `miCodigo` es la vista donde el vendedor comparte su código único con potenciales clientes.
- Notificaciones al cliente: implementadas en INC-004, usadas en los flujos de observar/aprobar/completar.
