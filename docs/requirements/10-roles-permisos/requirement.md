# REQ 10: Sistema de Roles y Permisos

**Estado:** ⏸️ PENDIENTE  
**Horas estimadas:** 20h  
**Prioridad:** MEDIA (Implementar al final, cuando todas las funcionalidades estén completas)  
**Fecha creación:** 2026-04-20

---

## Objetivo

Implementar un sistema completo de roles y permisos que permita al administrador:
- Crear usuarios del sistema
- Definir roles personalizados
- Asignar permisos granulares a cada rol
- Controlar qué módulos y acciones puede realizar cada usuario

---

## Problema Actual

🔴 **NO existe control de acceso granular:**
- Todos los usuarios autenticados tienen acceso completo
- No hay diferenciación de roles (Admin, Contador, Vendedor, Cliente)
- No se puede restringir acceso a módulos específicos
- Ejemplo: Un contador debería solo ver ventas, no editar tasas

---

## Solución Propuesta

### 1. Implementar Spatie Laravel Permission

**Librería:** [spatie/laravel-permission](https://spatie.be/docs/laravel-permission)

**Ventajas:**
- ✅ Estándar de la industria
- ✅ Roles y permisos flexibles
- ✅ Middleware para rutas
- ✅ Blade directives para vistas
- ✅ Compatible con políticas de Laravel

---

### 2. Estructura de Permisos

#### Formato: `recurso.acción`

**Ejemplos:**
```
exchange_rates.view
exchange_rates.create
exchange_rates.edit
exchange_rates.delete
exchange_rates.activate

sales.view
sales.create
sales.approve
sales.reject

sellers.view
sellers.create
sellers.edit

transactions.view
transactions.create

users.view
users.create
users.edit
users.delete

roles.view
roles.create
roles.edit
roles.delete
```

---

### 3. Roles Predefinidos (ejemplos)

#### Super Admin
- **Descripción:** Acceso total al sistema
- **Permisos:** `*.*` (todos)

#### Administrador
- **Descripción:** Gestiona tasas, aprueba ventas, ve reportes
- **Permisos:**
  - `exchange_rates.*`
  - `sales.*`
  - `sellers.view`
  - `transactions.view`
  - `users.view`

#### Contador
- **Descripción:** Solo lectura de ventas y reportes financieros
- **Permisos:**
  - `sales.view`
  - `transactions.view`
  - `sellers.view`

#### Vendedor
- **Descripción:** Registra ventas y ve sus propias comisiones
- **Permisos:**
  - `sales.create`
  - `sales.view` (solo sus propias ventas)
  - `transactions.view` (solo las suyas)

#### Cliente
- **Descripción:** Ve su historial de transacciones
- **Permisos:**
  - `transactions.view` (solo las propias)
  - `profile.edit`

**Nota:** El admin puede crear roles personalizados adicionales

---

### 4. Panel de Administración

#### 4.1 Gestión de Usuarios
- CRUD de usuarios
- Asignar rol a usuario
- Activar/desactivar usuario

#### 4.2 Gestión de Roles
- CRUD de roles
- Asignar/quitar permisos a rol
- Interfaz visual con checkboxes

#### 4.3 Gestión de Permisos
- Lista completa de permisos disponibles
- Agrupados por módulo (tasas, ventas, vendedores, etc.)

---

### 5. Middleware y Protección de Rutas

```php
// routes/web.php

// Solo admins pueden gestionar tasas
Route::middleware(['auth', 'permission:exchange_rates.edit'])
    ->put('exchange_rates/{rate}', [ExchangeRateController::class, 'update']);

// Solo admins pueden aprobar ventas
Route::middleware(['auth', 'permission:sales.approve'])
    ->post('sales/{sale}/approve', [SaleController::class, 'approve']);

// Contador puede ver ventas pero no editar
Route::middleware(['auth', 'permission:sales.view'])
    ->get('sales', [SaleController::class, 'index']);
```

---

### 6. Blade Directives

```blade
@can('sales.approve')
    <button class="btn-approve">Aprobar Venta</button>
@endcan

@role('admin')
    <a href="{{ route('exchange_rates.create') }}">Nueva Tasa</a>
@endrole

@hasanyrole('admin|contador')
    <div class="reportes-financieros">...</div>
@endhasanyrole
```

---

### 7. Políticas (Policies)

```php
// app/Policies/SalePolicy.php

public function approve(User $user, Sale $sale)
{
    return $user->hasPermissionTo('sales.approve');
}

public function viewOwn(User $user, Sale $sale)
{
    return $user->hasRole('vendedor') && $sale->seller->user_id === $user->id;
}
```

---

## Tareas de Implementación

| ID | Tarea | Tiempo | Prioridad |
|----|-------|--------|-----------|
| 10.1 | Instalar y configurar Spatie Laravel Permission | 1h | Alta |
| 10.2 | Crear seeder de permisos (todos los recursos del sistema) | 2h | Alta |
| 10.3 | Crear seeder de roles predefinidos | 1h | Alta |
| 10.4 | Crear panel de gestión de usuarios | 3h | Alta |
| 10.5 | Crear panel de gestión de roles | 3h | Alta |
| 10.6 | Crear panel de asignación de permisos (interfaz visual) | 3h | Alta |
| 10.7 | Proteger todas las rutas con middleware de permisos | 3h | Alta |
| 10.8 | Agregar Blade directives en todas las vistas | 2h | Media |
| 10.9 | Crear políticas para recursos principales | 2h | Media |

**Total estimado:** 20 horas

---

## Criterios de Aceptación

- [ ] Spatie Laravel Permission instalado y configurado
- [ ] Base de datos con tablas: roles, permissions, model_has_roles, model_has_permissions
- [ ] Seeder crea roles: Super Admin, Admin, Contador, Vendedor, Cliente
- [ ] Seeder crea permisos para todos los módulos del sistema
- [ ] Panel admin puede crear/editar/eliminar usuarios
- [ ] Panel admin puede crear roles personalizados
- [ ] Panel admin puede asignar permisos a roles con interfaz visual
- [ ] Todas las rutas protegidas con middleware de permisos
- [ ] Vistas ocultan botones/acciones según permisos del usuario
- [ ] Usuario contador solo ve módulos de lectura (ventas, reportes)
- [ ] Usuario vendedor solo ve sus propias ventas
- [ ] Cliente solo ve su historial de transacciones

---

## Módulos a Proteger

| Módulo | Permisos Necesarios |
|--------|---------------------|
| **Tasas de Cambio** | view, create, edit, delete, activate |
| **Ventas** | view, create, edit, approve, reject, delete |
| **Vendedores** | view, create, edit, delete |
| **Transacciones** | view, create, edit |
| **Usuarios** | view, create, edit, delete |
| **Roles** | view, create, edit, delete |
| **Reportes** | view, export |
| **Dashboard Admin** | view |

---

## Casos de Uso

### Caso 1: Contratar un Contador
1. Admin crea usuario "contador@cambio-j.com"
2. Asigna rol "Contador"
3. Contador inicia sesión
4. Solo ve: Ventas (readonly), Transacciones (readonly), Reportes
5. NO puede: Crear/editar tasas, aprobar ventas, gestionar vendedores

### Caso 2: Vendedor independiente
1. Admin crea usuario "vendedor1@mail.com"
2. Asigna rol "Vendedor"
3. Vendedor inicia sesión
4. Solo ve: Sus propias ventas, su monedero virtual
5. NO puede: Ver ventas de otros vendedores, aprobar/rechazar

### Caso 3: Cliente final
1. Cliente se registra en la plataforma
2. Automáticamente recibe rol "Cliente"
3. Solo ve: Su historial de transacciones, su perfil
4. NO puede: Acceder a módulos administrativos

---

## Notas Técnicas

### Instalación:
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### Uso en controladores:
```php
// Verificar permiso
if (!$user->hasPermissionTo('sales.approve')) {
    abort(403, 'No tienes permiso para aprobar ventas');
}

// Verificar rol
if (!$user->hasRole('admin')) {
    abort(403);
}
```

### Performance:
- Spatie usa caché para permisos
- Los checks son muy rápidos
- Cache se limpia automáticamente al cambiar permisos

---

## Por Qué Implementar al Final

1. **Requiere conocer todas las acciones del sistema** - Solo cuando tengamos todos los módulos completos sabremos exactamente qué permisos crear
2. **Evita refactorización constante** - Si agregamos permisos ahora y luego cambiamos funcionalidades, hay que actualizar permisos continuamente
3. **Más eficiente** - Una sola pasada por todo el sistema asignando permisos
4. **No bloquea desarrollo** - Por ahora, los desarrolladores pueden trabajar sin restricciones

---

## Próximo Paso

Implementar **DESPUÉS** de completar REQ 4, 5, 6, 7 y 8 (cuando toda la funcionalidad esté lista).

---

**Creado por:** Claude Sonnet 4.5  
**Solicitado por:** Usuario (Anthony)  
**Razón:** Seguridad y control de acceso granular para múltiples tipos de usuarios
