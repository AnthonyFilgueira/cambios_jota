# INC-004 — Notificaciones por correo incompletas

## Descripción del problema
Las notificaciones por correo electrónico del sistema son incompletas:
1. Cuando el vendedor observa/aprueba/deniega una transacción, el cliente NO recibe email
2. El dueño del negocio (admin/super-admin) nunca recibe notificaciones por correo
3. El vendedor no está informado de todos los cambios de estado

## Pasos para reproducir
1. Cliente crea una transacción
2. Vendedor la observa desde su panel → cliente NO recibe email de observación
3. Admin completa la transacción → dueño del negocio NO recibe ningún email

## Comportamiento esperado
- Nueva transacción → vendedor asignado recibe email ✅ + admin/super-admin también deben recibir
- Transacción observada (por vendedor o admin) → cliente recibe email + admin/super-admin informados
- Transacción aprobada/procesada → cliente recibe email + admin/super-admin informados
- Transacción completada → cliente recibe email + admin/super-admin informados
- Transacción cancelada → cliente recibe email + admin/super-admin informados

## Comportamiento actual
- Cuando admin observa: cliente recibe email ✅
- Cuando vendedor observa: cliente solo recibe notificación en base de datos (sin email) ❌
- Admin/super-admin nunca reciben emails de ningún evento ❌

## Entorno
- Local (desarrollo)
- Reportado: 2026-05-28
