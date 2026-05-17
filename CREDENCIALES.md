# CREDENCIALES DE PRUEBA — Cambio J
**URL local:** http://localhost

---

## Super-Admin (Dueño / Admin principal)

| Campo    | Valor                          |
|----------|-------------------------------|
| Email    | cambiosjottaa@innodite.com    |
| Password | CambiosJota2026!              |
| Rol      | super-admin (acceso total)    |

| Campo    | Valor                          |
|----------|-------------------------------|
| Email    | admin@cambiosj.com            |
| Password | password                       |
| Rol      | super-admin (demo)            |

**Acceso a:** Dashboard admin, Ventas, Transacciones, Vendedores, Países y Cuentas, Tasas, Divisas, Liquidaciones, Reportes, Roles y Permisos.

---

## Vendedores

| Nombre         | Email                          | Password | Código  | Comisión |
|----------------|-------------------------------|----------|---------|----------|
| Pedro Martínez | pedro.martinez@cambiosj.com   | password | VEND001 | 5%       |
| Ana López      | ana.lopez@cambiosj.com        | password | VEND002 | 7%       |
| Luis Torres    | luis.torres@cambiosj.com      | password | VEND003 | 10%      |
| Rosa Fernández | rosa.fernandez@cambiosj.com   | password | VEND004 | 5%       |

**Acceso a:** Mi Panel, Pendientes, Mis Ventas, Mi Monedero, Tasas.

---

## Clientes

| Nombre          | Email                        | Password |
|-----------------|------------------------------|----------|
| Juan Pérez      | juan.perez@gmail.com         | password |
| María González  | maria.gonzalez@gmail.com     | password |
| Carlos Rodríguez| carlos.rodriguez@gmail.com   | password |
| Ana Torres      | ana.torres@gmail.com         | password |

**Acceso a:** Inicio (dashboard), Mis Envíos, Iniciar Envío.

---

## Datos de prueba en BD

- **30 ventas** distribuidas: 4 pendientes · 6 aprobadas · 12 completadas · 4 rechazadas · 4 observadas
- **2 países:** Perú (7 bancos, 3 cuentas del negocio) + Venezuela (8 bancos)
- **3 cuentas del negocio** asignadas a los 4 vendedores:
  - BCP Principal · Interbank Corriente · BBVA Ahorros
- **Tasas activas:** PEN→VES, ARS→VES, CLP→VES

---

## Flujo de prueba recomendado

### Como Cliente
1. Login con `juan.perez@gmail.com` / `password`
2. Ir a **Iniciar Envío**
3. Ingresar código de vendedor: `VEND001`
4. Completar formulario y subir comprobante

### Como Vendedor
1. Login con `pedro.martinez@cambiosj.com` / `password`
2. Ver **Pendientes** → aprobar / observar ventas
3. Ver **Mi Monedero** → comisiones acumuladas

### Como Admin
1. Login con `cambiosjottaa@innodite.com` / `CambiosJota2026!`
2. Ver **Dashboard** → métricas generales
3. Ir a **Ventas → Pendientes de aprobación** → aprobar y subir comprobante
4. Ir a **Config → Países y cuentas** → gestionar bancos/cuentas
5. Ir a **Config → Roles y permisos** → ajustar permisos por rol
