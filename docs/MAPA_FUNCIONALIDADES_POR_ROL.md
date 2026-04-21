# 🗺️ MAPA DE FUNCIONALIDADES POR ROL - Cambio J

**Fecha:** 2026-04-20  
**Versión del sistema:** 89% completado (10/11 REQ)  
**Estado de roles:** ⚠️ **NO IMPLEMENTADOS** (REQ 10 pendiente)

---

## ⚠️ PROBLEMA ACTUAL IDENTIFICADO

### Dashboard sin diseño
- **URL actual:** `/dashboard` → `resources/views/dashboard.blade.php`
- **Problema:** Vista genérica de Laravel Breeze (solo dice "You're logged in!")
- **Solución:** Redirigir a `/owner-dashboard` que SÍ tiene diseño completo

### Sistema de roles NO implementado
- El modelo `User` **NO tiene campo `role`**
- **Todos los usuarios autenticados tienen acceso total** a todas las rutas
- REQ 10 (Roles y Permisos) está **PENDIENTE**
- Se requiere implementar **Spatie Laravel Permission**

---

## 🎯 ROLES PLANIFICADOS (según REQ 10)

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| **Super Admin** | Dueño del sistema | Total (100%) |
| **Administrador** | Gestiona tasas y aprueba ventas | 80% |
| **Contador** | Solo lectura de ventas y reportes | 40% |
| **Vendedor** | Registra ventas, ve comisiones | 30% |
| **Cliente** | Ve su historial de transacciones | 10% |

---

# 📋 FUNCIONALIDADES POR ROL

## 1️⃣ SUPER ADMIN / DUEÑO

### 🏠 Dashboard Principal
- **Ruta:** `/owner-dashboard` (GET)
- **Controlador:** `OwnerDashboardController@index`
- **Vista:** `resources/views/owner-dashboard.blade.php`
- **Acciones:**
  - Ver métricas globales (total vendido, comisiones, cantidad de ventas)
  - Filtrar por período (hoy, semana, mes, trimestre, año, personalizado)
  - Ver rankings de vendedores (por monto, cantidad, comisiones)
  - Ver liquidaciones recientes
  - Ver saldos pendientes en monederos
  - Exportar dashboard a CSV/PDF
- **Estado:** ✅ IMPLEMENTADO (REQ 5)

### 💱 Gestión de Divisas (REQ 6)

#### a) CRUD de Divisas
- **Rutas:**
  - `GET /currencies` → `currencies.index` → Lista de divisas
  - `GET /currencies/create` → `currencies.create` → Formulario nueva divisa
  - `POST /currencies` → `currencies.store` → Guardar divisa
  - `GET /currencies/{id}/edit` → `currencies.edit` → Editar divisa
  - `PATCH /currencies/{id}` → `currencies.update` → Actualizar divisa
  - `PATCH /currencies/{id}/toggle-status` → `currencies.toggleStatus` → Activar/desactivar
- **Controlador:** `CurrencyController`
- **Vistas:** `resources/views/currencies/`
- **Estado:** ✅ IMPLEMENTADO

#### b) CRUD de Pares de Divisas
- **Rutas:**
  - `GET /currency-pairs` → `currency-pairs.index`
  - `GET /currency-pairs/create` → `currency-pairs.create`
  - `POST /currency-pairs` → `currency-pairs.store`
  - `GET /currency-pairs/{id}/edit` → `currency-pairs.edit`
  - `PATCH /currency-pairs/{id}` → `currency-pairs.update`
  - `DELETE /currency-pairs/{id}` → `currency-pairs.destroy`
  - `PATCH /currency-pairs/{id}/toggle-status` → `currency-pairs.toggleStatus`
- **Controlador:** `CurrencyPairController`
- **Vistas:** `resources/views/currency-pairs/`
- **Estado:** ✅ IMPLEMENTADO

#### c) CRUD de Corredores
- **Rutas:**
  - `GET /corridors` → `corridors.index`
  - `GET /corridors/create` → `corridors.create`
  - `POST /corridors` → `corridors.store`
  - `GET /corridors/{id}/edit` → `corridors.edit`
  - `PATCH /corridors/{id}` → `corridors.update`
  - `DELETE /corridors/{id}` → `corridors.destroy`
  - `PATCH /corridors/{id}/toggle-status` → `corridors.toggleStatus`
- **Controlador:** `CorridorController`
- **Vistas:** `resources/views/corridors/`
- **Estado:** ✅ IMPLEMENTADO

#### d) Matriz de Corredores (Asignación Par-Corredor)
- **Rutas:**
  - `GET /corridor-matrix` → `corridor-matrix.index` → Vista de matriz interactiva
  - `POST /corridor-matrix/toggle` → `corridor-matrix.toggle` → Asignar/desasignar (AJAX)
- **Controlador:** `CorridorMatrixController`
- **Vistas:** `resources/views/corridor-matrix/`
- **Estado:** ✅ IMPLEMENTADO

### 📊 Consola de Tasas de Cambio (REQ 7)

- **Rutas:**
  - `GET /exchange_rates` → `exchange_rates.index` → Lista con filtros
  - `GET /exchange_rates/create` → `exchange_rates.create` → Nueva tasa
  - `POST /exchange_rates` → `exchange_rates.store` → Guardar tasa
  - `GET /exchange_rates/{id}` → `exchange_rates.show` → Ver detalle
  - `GET /exchange_rates/{id}/edit` → `exchange_rates.edit` → Editar tasa
  - `PATCH /exchange_rates/{id}` → `exchange_rates.update` → Actualizar
  - `DELETE /exchange_rates/{id}` → `exchange_rates.destroy` → Eliminar
  - `POST /exchange_rates/{id}/activate` → `exchange_rates.activate` → Activar (solo 1 activa por par)
- **Controlador:** `ExchangeRateController`
- **Vistas:** `resources/views/exchange_rates/`
- **Características:**
  - Filtros: activas, inactivas, todas
  - Validación: solo 1 tasa activa por par
  - Historial de auditoría completo (Observer)
  - Integración con simulador público
- **Estado:** ✅ IMPLEMENTADO

### 👥 Gestión de Vendedores (REQ 5)

- **Rutas:**
  - `GET /sellers` → `sellers.index` → Lista de vendedores
  - `GET /sellers/create` → `sellers.create` → Nuevo vendedor
  - `POST /sellers` → `sellers.store` → Guardar vendedor
  - `GET /sellers/{id}` → `sellers.show` → Ver perfil vendedor
  - `GET /sellers/{id}/edit` → `sellers.edit` → Editar vendedor
  - `PATCH /sellers/{id}` → `sellers.update` → Actualizar
  - `DELETE /sellers/{id}` → `sellers.destroy` → Eliminar
- **Controlador:** `SellerController`
- **Vistas:** `resources/views/sellers/`
- **Características:**
  - Generación automática de código único (VEND001, VEND002...)
  - Configuración de comisiones (vendedor % + dueño %)
  - Visualización de saldo en monedero
- **Estado:** ✅ IMPLEMENTADO

### 💰 Liquidaciones

- **Rutas:**
  - `GET /liquidations` → `liquidations.index` → Lista de liquidaciones
  - `GET /liquidations/create` → `liquidations.create` → Nueva liquidación
  - `POST /liquidations` → `liquidations.store` → Guardar liquidación
  - `GET /liquidations/{id}` → `liquidations.show` → Ver detalle
  - `GET /liquidations/{id}/edit` → `liquidations.edit` → Editar
  - `PATCH /liquidations/{id}` → `liquidations.update` → Actualizar
  - `DELETE /liquidations/{id}` → `liquidations.destroy` → Eliminar
- **Controlador:** `LiquidationController`
- **Vistas:** `resources/views/liquidations/`
- **Características:**
  - Registro de pago a vendedores
  - Métodos: efectivo, transferencia, yape, plin
  - Actualización automática de monedero
- **Estado:** ✅ IMPLEMENTADO

### 💼 Gestión de Ventas (REQ 3 + 4)

#### a) CRUD de Ventas
- **Rutas:**
  - `GET /sales` → `sales.index` → Lista general de ventas
  - `GET /sales/create` → `sales.create` → Registrar nueva venta
  - `POST /sales` → `sales.store` → Guardar venta
  - `GET /sales/{id}` → `sales.show` → Ver detalle de venta
  - `GET /sales/{id}/edit` → `sales.edit` → Editar venta
  - `PATCH /sales/{id}` → `sales.update` → Actualizar venta
  - `DELETE /sales/{id}` → `sales.destroy` → Eliminar venta
- **Controlador:** `SaleController`
- **Vistas:** `resources/views/sales/`
- **Estado:** ✅ IMPLEMENTADO

#### b) Carga Masiva de Ventas
- **Rutas:**
  - `GET /sales-bulk` → `sales.bulk.create` → Formulario carga masiva
  - `POST /sales/bulk` → `sales.bulk.store` → Procesar múltiples ventas
- **Controlador:** `SaleController@bulkCreate`, `@bulkStore`
- **Vistas:** `resources/views/sales/bulk-create.blade.php`
- **Estado:** ✅ IMPLEMENTADO

#### c) Cola de Aprobación Admin
- **Rutas:**
  - `GET /sales-pending-admin` → `sales.pending.admin` → Ventas pendientes de aprobación admin
  - `POST /sales/{id}/approve` → `sales.approve` → Aprobar venta
  - `POST /sales/{id}/reject` → `sales.reject` → Rechazar venta
  - `POST /sales/{id}/observe` → `sales.observe` → Observar venta (solicitar correcciones)
- **Controlador:** `SaleController@pendingAdmin`, `@approve`, `@reject`, `@observe`
- **Características:**
  - Sistema de observaciones con trazabilidad (REQ 4)
  - Logs de quién observó, qué, cuándo
  - Notificaciones por email
- **Estado:** ✅ IMPLEMENTADO

#### d) Ventas Aprobadas y Observadas
- **Rutas:**
  - `GET /sales-approved` → `sales.approved` → Ventas aprobadas
  - `GET /sales-observed` → `sales.observed` → Ventas con observaciones
- **Controlador:** `SaleController@approved`, `@observed`
- **Estado:** ✅ IMPLEMENTADO

#### e) Gestión de Comprobantes
- **Rutas:**
  - `POST /sales/{id}/upload-voucher` → `sales.uploadVoucher` → Subir comprobante
  - `GET /sales/{id}/voucher` → `sales.showVoucher` → Ver comprobante
  - `GET /sales/{id}/voucher/download` → `sales.downloadVoucher` → Descargar comprobante
- **Controlador:** `SaleController@uploadVoucher`, `@showVoucher`, `@downloadVoucher`
- **Estado:** ✅ IMPLEMENTADO

### 📈 Reportes y Exportaciones (REQ 5)

#### a) Reportes de Rendimiento
- **Rutas:**
  - `GET /reports` → `reports.index` → Vista principal de reportes
  - `GET /reports/sellers/{id}/performance` → `reports.performance` → Rendimiento individual
  - `GET /reports/sellers/rankings` → `reports.rankings` → Rankings generales
- **Controladores:** `ReportController`, `SellerReportController`
- **Vistas:** `resources/views/reports/`
- **Estado:** ✅ IMPLEMENTADO

#### b) Exportaciones
- **Rutas:**
  - `GET /export/rankings/csv` → `export.rankings.csv` → Rankings en CSV
  - `GET /export/rankings/pdf` → `export.rankings.pdf` → Rankings en PDF
  - `GET /export/seller/{id}/pdf` → `export.seller.pdf` → Reporte vendedor en PDF
  - `GET /export/dashboard/csv` → `export.dashboard.csv` → Dashboard en CSV
  - `GET /export/dashboard/pdf` → `export.dashboard.pdf` → Dashboard en PDF
- **Controlador:** `ExportController`
- **Estado:** ✅ IMPLEMENTADO

### 👤 Gestión de Perfil
- **Rutas:**
  - `GET /profile` → `profile.edit` → Editar perfil
  - `PATCH /profile` → `profile.update` → Actualizar perfil
  - `DELETE /profile` → `profile.destroy` → Eliminar cuenta
- **Controlador:** `ProfileController`
- **Vistas:** `resources/views/profile/`
- **Estado:** ✅ IMPLEMENTADO (Laravel Breeze)

---

## 2️⃣ ADMINISTRADOR

**Nota:** Actualmente tiene **mismo acceso que Super Admin** (roles no implementados)

### Acceso Planificado (cuando se implemente REQ 10):
- ✅ Todas las funcionalidades de Super Admin **EXCEPTO:**
  - ❌ Gestión de usuarios y roles
  - ❌ Configuración del sistema
  - ❌ Eliminación de vendedores

---

## 3️⃣ CONTADOR

**Acceso Planificado (REQ 10 pendiente):**

### Solo Lectura (View)
- 📊 **Dashboard del dueño** → `/owner-dashboard` (solo lectura)
- 💼 **Ventas** → `/sales`, `/sales-approved` (solo ver, no editar)
- 📈 **Reportes** → `/reports`, `/reports/sellers/rankings`
- 💰 **Liquidaciones** → `/liquidations` (solo ver)
- 👥 **Vendedores** → `/sellers` (solo ver lista y perfiles)

### NO puede:
- ❌ Crear, editar o eliminar tasas de cambio
- ❌ Aprobar o rechazar ventas
- ❌ Crear o editar vendedores
- ❌ Realizar liquidaciones
- ❌ Exportar datos (opcional según negocio)

---

## 4️⃣ VENDEDOR

**Acceso Planificado (REQ 10 pendiente):**

### Ventas
- **Rutas permitidas:**
  - `GET /sales-pending-seller` → `sales.pending.seller` → Ventas asignadas a él pendientes de validación
  - `POST /sales/{id}/approve` → `sales.approve` → Aprobar venta (escala a admin)
  - `POST /sales/{id}/reject` → `sales.reject` → Rechazar venta
- **Restricción:** Solo ve ventas asignadas a su código de vendedor
- **Estado vista:** ✅ IMPLEMENTADA (`sales-pending-seller`)

### Monedero Virtual
- **Rutas permitidas:**
  - `GET /wallet` → `wallet.index` → Ver su monedero con saldo y movimientos
- **Controlador:** `WalletController`
- **Vistas:** `resources/views/wallet/`
- **Características:**
  - Ver saldo en tiempo real
  - Ver historial de comisiones ganadas
  - Ver liquidaciones recibidas
- **Estado:** ✅ IMPLEMENTADO (REQ 5)

### NO puede:
- ❌ Ver ventas de otros vendedores
- ❌ Aprobar definitivamente (solo escala a admin)
- ❌ Ver dashboard del dueño
- ❌ Gestionar tasas de cambio
- ❌ Ver reportes globales

---

## 5️⃣ CLIENTE

**Acceso Planificado (REQ 10 pendiente):**

### Transacciones
- **Rutas permitidas:**
  - `GET /transactions` → `transactions.index` → Historial de sus transacciones
- **Controlador:** `TransactionController`
- **Vistas:** `resources/views/transactions/`
- **Restricción:** Solo ve sus propias transacciones
- **Estado:** ✅ IMPLEMENTADO (REQ 2)

### Perfil
- **Rutas permitidas:**
  - `GET /profile` → `profile.edit`
  - `PATCH /profile` → `profile.update`
- **Estado:** ✅ IMPLEMENTADO

### NO puede:
- ❌ Acceder a módulos administrativos
- ❌ Ver ventas, vendedores, reportes
- ❌ Ver dashboard
- ❌ Gestionar tasas

---

# 🚨 PROBLEMA IDENTIFICADO Y SOLUCIÓN

## Problema: Dashboard sin diseño

### Estado Actual:
```
Usuario admin inicia sesión
  ↓
Redirige a /dashboard
  ↓
Muestra: resources/views/dashboard.blade.php (genérico Breeze)
  ↓
❌ Solo dice "You're logged in!" sin diseño
```

### Dashboard completo existe pero en otra ruta:
```
/owner-dashboard
  ↓
OwnerDashboardController@index
  ↓
resources/views/owner-dashboard.blade.php
  ↓
✅ Dashboard completo con métricas, rankings, gráficos
```

## Solución Inmediata:

### Opción 1: Redirigir /dashboard a /owner-dashboard
```php
// routes/web.php
Route::get('/dashboard', function () {
    return redirect()->route('owner.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
```

### Opción 2: Usar la vista del owner directamente
```php
// routes/web.php
Route::get('/dashboard', [OwnerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
```

---

# 📊 RESUMEN DE IMPLEMENTACIÓN

## Estado por Módulo:

| Módulo | REQ | Funcionalidad | Rutas | Vistas | Estado |
|--------|-----|---------------|-------|--------|--------|
| **Simulador** | 1 | Conversión PEN→VES | `/` | `welcome.blade.php` | ✅ 100% |
| **Autenticación** | 2 | Login, Registro | `/login`, `/register` | Breeze | ✅ 100% |
| **Transacciones** | 2 | Historial usuario | `/transactions` | `transactions/` | ✅ 100% |
| **Ventas** | 3 | CRUD + Aprobación | `/sales/*` | `sales/` | ✅ 100% |
| **Observaciones** | 4 | Feedback ventas | `/sales/{id}/observe` | Integrado | ✅ 100% |
| **Vendedores** | 5 | CRUD + Comisiones | `/sellers/*` | `sellers/` | ✅ 100% |
| **Monedero** | 5 | Saldo vendedor | `/wallet` | `wallet/` | ✅ 100% |
| **Liquidaciones** | 5 | Pagos vendedores | `/liquidations/*` | `liquidations/` | ✅ 100% |
| **Dashboard Dueño** | 5 | Métricas BI | `/owner-dashboard` | `owner-dashboard.blade.php` | ✅ 100% |
| **Reportes** | 5 | Rankings, rendimiento | `/reports/*` | `reports/` | ✅ 100% |
| **Exportaciones** | 5 | CSV/PDF | `/export/*` | Descarga directa | ✅ 100% |
| **Divisas** | 6 | CRUD divisas | `/currencies/*` | `currencies/` | ✅ 100% |
| **Pares** | 6 | CRUD pares | `/currency-pairs/*` | `currency-pairs/` | ✅ 100% |
| **Corredores** | 6 | CRUD corredores | `/corridors/*` | `corridors/` | ✅ 100% |
| **Matriz** | 6 | Asignación Par-Corredor | `/corridor-matrix` | `corridor-matrix/` | ✅ 100% |
| **Tasas** | 7 | CRUD tasas + activación | `/exchange_rates/*` | `exchange_rates/` | ✅ 100% |
| **Historicidad** | 9 | Snapshots inmutables | Integrado en ventas | N/A | ✅ 100% |
| **Landing** | 10 | UI/UX mejorada | `/` | `welcome.blade.php` | ✅ 100% |
| **Datos Demo** | 11 | Seeders completos | Comando artisan | N/A | ✅ 100% |
| **Roles** | 10 | Spatie Permission | - | - | ❌ PENDIENTE |

---

# 🔐 SEGURIDAD ACTUAL

## ⚠️ ADVERTENCIA CRÍTICA

**Actualmente NO hay control de acceso basado en roles:**
- Cualquier usuario autenticado puede acceder a **TODAS** las rutas
- No hay middleware de permisos
- No hay validación de roles en controladores
- No hay directivas `@can` en vistas

**Implicaciones:**
- Un cliente podría acceder a `/exchange_rates/create`
- Un vendedor podría ver `/owner-dashboard`
- Cualquiera puede aprobar ventas si conoce la URL

**Mitigación temporal:**
- Solo crear usuarios confiables
- No compartir URLs de admin con clientes
- Implementar **REQ 10 (Roles) URGENTE** antes de producción

---

# 📝 PRÓXIMOS PASOS RECOMENDADOS

## 1. Solución inmediata (1h):
- [ ] Redirigir `/dashboard` a `/owner-dashboard`
- [ ] Verificar que el admin vea el dashboard completo

## 2. Implementar REQ 10 - Roles (20h):
- [ ] Instalar Spatie Laravel Permission
- [ ] Crear seeders de roles y permisos
- [ ] Proteger todas las rutas con middleware
- [ ] Agregar directivas `@can` en vistas
- [ ] Crear panel de gestión de usuarios/roles

## 3. Testing de seguridad (5h):
- [ ] Verificar que contador no puede editar tasas
- [ ] Verificar que vendedor solo ve sus ventas
- [ ] Verificar que cliente no accede a rutas admin

---

**Documento creado por:** Claude Sonnet 4.5  
**Fecha:** 2026-04-20  
**Última revisión:** 2026-04-20  
**Basado en:** CLAUDE.md, Checkpoint 2026-04-20-1557, Requirements 1-11
