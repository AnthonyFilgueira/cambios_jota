# Tarea 4.6: Módulo de Carga de Comprobante (Admin)

**Módulo:** REQ 4 - Observaciones y Feedback  
**Estimado:** 5h (tarea 4.2 original)  
**Estado:** ✅ COMPLETO

---

## Objetivo

Permitir al admin cargar el comprobante de transferencia de ventas aprobadas, cambiando automáticamente el estado a `completed`.

---

## Componentes Implementados

### 1. Método en SaleController
- `approved()`: Vista de ventas aprobadas pendientes de comprobante
- `uploadVoucher(Request $request, Sale $sale)`: Procesa upload

### 2. Rutas
- `GET /sales-approved` → Vista de ventas aprobadas
- `POST /sales/{sale}/upload-voucher` → Carga comprobante

### 3. Vista `approved.blade.php`
- Lista ventas con `approval_status = 'approved'`
- Botón "Cargar Comprobante" (morado) por cada venta
- Modal con input file y validación

### 4. Almacenamiento
- Directorio: `storage/app/vouchers/`
- Nombre archivo: `voucher_{sale_id}_{timestamp}.{ext}`
- Path guardado en `sales.voucher_path`

---

## Validaciones

**Servidor:**
- `required`: Archivo obligatorio
- `file`: Debe ser archivo
- `mimes:jpg,jpeg,png,pdf`: Solo imágenes o PDF
- `max:5120`: Máximo 5MB (5120 KB)

**Cliente:**
- Input file con `accept=".jpg,.jpeg,.png,.pdf"`

---

## Flujo de uso

1. Admin aprueba venta → estado `approved`
2. Vista `/sales-approved` lista ventas sin comprobante
3. Clic en "Cargar Comprobante"
4. Modal con input file
5. Seleccionar archivo (JPG/PNG/PDF)
6. Submit → Archivo se almacena en `storage/app/vouchers/`
7. Estado cambia automáticamente a `completed`
8. Se registra en `sale_logs` (Observer)

---

## Paleta Cambio J

- Header tabla: `bg-gradient-to-r from-teal-600 to-teal-500`
- Hover filas: `hover:bg-teal-50`
- Botón "Cargar": `bg-purple-600`
- Badge info: `bg-teal-50 border-teal-400`

---

**Completado:** 2026-04-20
