# REQ 3: Venta Indirecta - Workflow de Aprobación

**Estado:** 🔄 EN PROGRESO (40% → 100%)  
**Horas estimadas:** 30h  
**Prioridad:** CRÍTICA

---

## Objetivo

Implementar un sistema de aprobación multi-nivel para las ventas indirectas, permitiendo que los vendedores registren transacciones que deben ser validadas por el vendedor asignado y, posteriormente, por el administrador.

---

## Contexto

**Estado actual (40% completado):**
- ✅ Modelo `Sale` con migración
- ✅ Modelo `Seller` con migración  
- ✅ `SaleController` con CRUD + carga masiva
- ✅ Vistas: `sales/{index,create,bulk-create}.blade.php`

**Falta implementar (60%):**
- ❌ Workflow de aprobación multi-nivel
- ❌ Notificaciones a vendedores
- ❌ Tablero de aprobaciones
- ❌ Estados de transición
- ❌ Paleta de colores Cambio J

---

## Funcionalidad Requerida

### 1. Estados de Aprobación
- `pending_seller`: Venta creada, esperando validación del vendedor
- `pending_admin`: Vendedor aprobó, esperando validación del admin
- `approved`: Administrador aprobó la transacción
- `rejected`: Rechazada en cualquier etapa

### 2. Flujo de Aprobación
1. Usuario crea venta con código de vendedor
2. Vendedor recibe notificación
3. Vendedor puede: Aprobar → escala a admin, o Rechazar
4. Admin puede: Aprobar → completa flujo, o Rechazar
5. Usuario recibe confirmación final

### 3. Vistas Necesarias
- **Vendedor:** Tablero "Ventas Pendientes de Validar"
- **Admin:** Cola de aprobación ordenada por timestamp
- **Usuario:** Indicadores de estado en historial

### 4. Notificaciones
- Alerta al vendedor por nueva venta
- Alerta al admin por escalación
- Alerta al usuario por aprobación/rechazo

---

## Tareas

| ID | Tarea | Archivo | Estado |
|----|-------|---------|--------|
| 3.1 | Migración: campo approval_status | Migration | ⏸️ |
| 3.2 | Métodos approve/reject en Sale | Model | ⏸️ |
| 3.3 | Rutas y métodos approve/reject | Controller | ⏸️ |
| 3.4 | Vista: Tablero vendedor | Blade | ⏸️ |
| 3.5 | Vista: Cola aprobación admin | Blade | ⏸️ |
| 3.6 | Sistema de notificaciones básico | Controller | ⏸️ |
| 3.7 | Aplicar paleta Cambio J | CSS/Blade | ⏸️ |

---

## Stack Técnico

```yaml
Backend:  Laravel 12
Frontend: Alpine.js 3.x
Vistas:   Blade templates
Estilos:  Tailwind CSS + Paleta Cambio J
```

---

## Criterios de Aceptación

- [ ] Campo `approval_status` en tabla `sales`
- [ ] Métodos `approve()` y `reject()` en modelo `Sale`
- [ ] Rutas `/sales/{id}/approve` y `/sales/{id}/reject`
- [ ] Vista de vendedor muestra solo ventas `pending_seller`
- [ ] Vista de admin muestra solo ventas `pending_admin`
- [ ] Notificaciones visuales (sin email por ahora)
- [ ] Paleta Cambio J aplicada a todas las vistas de ventas
- [ ] Estados muestran colores distintivos (pending=amarillo, approved=verde, rejected=rojo)

---

## Notas de Implementación

- Reutilizar patrón Alpine.js de REQ 1 y 2
- Usar componentes Blade de navegación existentes
- Notificaciones: solo visuales en interfaz (no email/SMS)
- Estados con colores de la paleta: 
  - `pending_*`: cj-rosa o cj-turquesa
  - `approved`: verde Tailwind
  - `rejected`: rojo Tailwind

---

**Creado:** 2026-04-20  
**Actualizado:** 2026-04-20
