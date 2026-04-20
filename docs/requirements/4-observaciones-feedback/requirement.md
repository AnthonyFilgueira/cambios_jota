# REQ 4: Gestión de Observaciones y Feedback Loop

**Módulo:** Workflow Operativo  
**Prioridad:** MEDIA  
**Horas estimadas:** 39h  
**Estado:** ✅ COMPLETO (8/8 tareas)

---

## Descripción General

Sistema de comunicación bidireccional entre admin y vendedores para gestionar observaciones, correcciones y validaciones de ventas. Incluye trazabilidad completa de cambios, notificaciones en tiempo real y gestión de comprobantes de transferencia.

---

## Problema que Resuelve

**Situación actual:**
- Admin recibe ventas de vendedores sin posibilidad de solicitar correcciones
- NO existe feedback loop cuando hay errores en los datos
- Falta trazabilidad de quién hizo qué cambios
- NO hay forma de cargar/visualizar comprobantes de transferencia

**Consecuencias:**
- Ventas incorrectas deben rechazarse completamente (no hay opción de corrección)
- Vendedores no saben qué está mal
- NO hay auditoría de cambios
- Proceso de aprobación ineficiente

---

## Solución Propuesta

### 1. Sistema de Observaciones (Admin → Vendedor)
- Botón "Solicitar Información Adicional" en cola de aprobación admin
- Campo de texto para especificar qué necesita corregirse
- Cambio automático de estado a `observed`
- Notificación al vendedor en tiempo real

### 2. Centro de Notificaciones para Vendedor
- Vista dedicada "Ventas Observadas" (filtro: `approval_status = 'observed'`)
- Mostrar observación del admin
- Botón de acción "Corregir Venta"

### 3. Re-edición Rápida
- Vista de edición pre-cargada con datos actuales
- Mostrar observación del admin en la parte superior
- Botón "Guardar y Re-enviar a Admin"
- Cambio de estado: `observed` → `pending_admin`

### 4. Módulo de Comprobantes
- Campo de carga de archivo en vista admin (después de aprobar)
- Almacenamiento en `storage/app/vouchers/`
- Relación: `sales.voucher_path` (nullable)
- Cambio de estado: `approved` → `completed` al cargar comprobante

### 5. Visor de Comprobantes
- Botón "Ver Comprobante" en historial de transacciones (cliente)
- Modal con imagen/PDF del comprobante
- Botón de descarga

### 6. Notificaciones al Cliente
- Email automático cuando venta pasa a `completed`
- Incluir enlace al comprobante

### 7. Log de Trazabilidad
- Tabla `sale_logs` con tracking de cambios:
  - `user_id` (quién hizo el cambio)
  - `sale_id` (qué venta)
  - `action` (approved, rejected, observed, corrected, completed)
  - `comment` (observación o nota)
  - `old_status` / `new_status`
  - `timestamp`
- Vista admin: "Historial de Cambios" por venta

---

## Tareas del Requerimiento

| # | Tarea | Horas | Estado |
|---|-------|-------|--------|
| 4.1 | Base de datos: estados y campos | 2h | ✅ COMPLETO |
| 4.2 | Sistema de trazabilidad (sale_logs) | 3h | ✅ COMPLETO |
| 4.3 | Funcionalidad "Observar venta" (Admin) | 3h | ✅ COMPLETO |
| 4.4 | Vista "Ventas Observadas" (Vendedor) | 2h | ✅ COMPLETO |
| 4.5 | Interfaz de re-edición rápida | 2h | ✅ COMPLETO |
| 4.6 | Módulo de carga de comprobante (Admin) | 3h | ✅ COMPLETO |
| 4.7 | Visor y descarga de comprobante | 2h | ✅ COMPLETO |
| 4.8 | Notificaciones al cliente (Email) | 3h | ✅ COMPLETO |

**Total implementado:** 20h (de 39h estimadas)  
**Ahorro:** 19h (tareas simplificadas y reutilización de código existente)

---

## Flujo de Estados Actualizado

```
pending_seller → (vendedor aprueba) → pending_admin
                                           ↓
                  ┌─────────────────────────┴─────────────────────────┐
                  ↓                                                     ↓
            (admin aprueba)                                    (admin observa)
                  ↓                                                     ↓
              approved                                              observed
                  ↓                                                     ↓
        (admin carga comprobante)                            (vendedor corrige)
                  ↓                                                     ↓
              completed                                          pending_admin
                                                                        ↓
                                                                  (loop hasta aprobar)
```

---

## Archivos a Crear/Modificar

### Nuevos archivos:
```
database/migrations/
└── xxxx_add_observed_state_and_fields_to_sales.php
└── xxxx_create_sale_logs_table.php

app/Models/
└── SaleLog.php

resources/views/sales/
├── observed.blade.php (vendedor)
├── edit-observed.blade.php (vendedor)
└── show-voucher.blade.php (cliente/admin)

routes/web.php (6 rutas nuevas)
```

### Archivos a modificar:
```
database/migrations/xxxx_add_approval_status_to_sales_table.php
  → Agregar estado 'observed' y 'completed'
  → Agregar columnas: voucher_path, admin_observation

app/Models/Sale.php
  → Agregar método observe()
  → Agregar método complete()
  → Agregar relación hasMany SaleLog

app/Http/Controllers/SaleController.php
  → Agregar método observe()
  → Agregar método observed() (vista vendedor)
  → Agregar método uploadVoucher()
  → Agregar método showVoucher()
```

---

## Decisiones Técnicas

### 1. Estados adicionales
- `observed`: Venta devuelta a vendedor para corrección
- `completed`: Venta aprobada + comprobante cargado

### 2. Almacenamiento de comprobantes
- Path: `storage/app/vouchers/{sale_id}_{timestamp}.{ext}`
- Formatos permitidos: jpg, jpeg, png, pdf
- Tamaño máximo: 5MB

### 3. Log de trazabilidad
- Tabla separada `sale_logs` (evita modificar `sales` constantemente)
- Inmutable: solo INSERT, nunca UPDATE/DELETE
- Observer en modelo Sale para auto-logging

### 4. Notificaciones
- Sistema de notificaciones existente (REQ 3.6)
- Email: usar queue para no bloquear

---

## Validaciones y Reglas de Negocio

1. **Observar venta:**
   - Solo admin puede observar
   - Solo ventas en estado `pending_admin`
   - Observación es obligatoria (min 10 caracteres)

2. **Corregir venta:**
   - Solo vendedor propietario
   - Solo ventas en estado `observed`
   - Al guardar, vuelve a `pending_admin`

3. **Cargar comprobante:**
   - Solo admin puede cargar
   - Solo ventas en estado `approved`
   - Al cargar, cambia a `completed`

4. **Ver comprobante:**
   - Admin: puede ver todos
   - Vendedor: solo sus ventas
   - Cliente: solo su venta (si tiene transaction_id asociado)

---

## Notas de Implementación

- Usar FormRequest para validaciones
- Implementar Observer pattern para auto-logging
- Reutilizar componente de notificaciones (REQ 3.6)
- Aplicar paleta Cambio J en todas las vistas
- Alpine.js para modales y confirmaciones

---

## Dependencias

- ✅ REQ 3: Sistema de aprobación multi-nivel (completo)
- ✅ REQ 3.6: Sistema de notificaciones (completo)
- ⏸️ REQ 2: Historial de transacciones (para vincular cliente)

---

**Creado:** 2026-04-20  
**Última actualización:** 2026-04-20
