# Tarea 4.3: Funcionalidad "Observar Venta" (Admin)

**Módulo:** REQ 4 - Observaciones y Feedback  
**Estimado:** 5h (tarea 4.1 original)  
**Estado:** ✅ COMPLETO

---

## Objetivo

Permitir al administrador devolver una venta al vendedor con una observación específica, cambiando el estado de `pending_admin` a `observed`.

---

## Componentes Implementados

### 1. Método en SaleController
- `observe(Request $request, Sale $sale)`
- Validación: observation (required, string, min:10, max:1000)
- Llama al método `observe()` del modelo Sale
- Retorna con mensaje de éxito/error

### 2. Ruta
- `POST /sales/{sale}/observe`
- Nombre: `sales.observe`

### 3. Botón "Solicitar Info" en vista pending-admin
- Color naranja (alerta, no destructivo ni aprobación)
- Abre modal con textarea
- Validación en cliente (Alpine.js) y servidor

### 4. Modal de observación
- Textarea con placeholder sugerente
- Validación en tiempo real (mínimo 10 caracteres)
- Mensaje de advertencia: "venta volverá al vendedor"
- Botón submit deshabilitado si < 10 caracteres

---

## Flujo de uso

1. Admin revisa venta en cola `pending_admin`
2. Detecta error/falta de información
3. Clic en "Solicitar Info" (naranja)
4. Escribe observación específica (ej: "Falta DNI del cliente")
5. Submit → Estado cambia a `observed`
6. Se registra en `sale_logs` automáticamente (Observer)
7. Vendedor verá la venta en su vista de "Ventas Observadas"

---

## Paleta Cambio J aplicada

- Botón "Aprobar": `bg-teal-500` (acción positiva)
- Botón "Solicitar Info": `bg-orange-500` (alerta)
- Botón "Rechazar": `bg-pink-500` (destructivo)
- Modal: focus ring `orange-500`

---

**Completado:** 2026-04-20
