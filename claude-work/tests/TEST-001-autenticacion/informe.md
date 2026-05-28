# TEST-001 — Autenticación (Login / Registro / Redirección por Rol)

## Objetivo
Verificar que el sistema de autenticación funcione correctamente para todos los roles: login, registro de nuevos clientes con código de vendedor, logout, y redirección correcta según el rol asignado.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/Auth/RegisteredUserController.php` (métodos: `create`, `store`)
- **Controlador:** `app/Http/Controllers/Auth/AuthenticatedSessionController.php` (métodos: `create`, `store`, `destroy`)
- **Controlador:** `app/Http/Controllers/Auth/PasswordResetLinkController.php`, `NewPasswordController.php`
- **Vista:** `resources/views/auth/login.blade.php`
- **Vista:** `resources/views/auth/register.blade.php`
- **Rutas:** `GET /login`, `POST /login`, `POST /logout`, `GET /register`, `POST /register`
- **Rutas:** `GET /forgot-password`, `POST /forgot-password`, `GET /reset-password/{token}`, `POST /reset-password`
- **Modelo:** `app/Models/User.php`
- **Middleware:** `Illuminate\Auth\Middleware\Authenticate`, `Spatie\Permission\Middlewares\RoleMiddleware`
- **Test PHP:** `tests/Feature/Auth/AuthenticationTest.php`, `tests/Feature/Auth/RegistrationTest.php`

## Casos de prueba

### Caso 1 — Renderizar pantalla de login
- **Precondición:** Usuario no autenticado
- **Acción:** `GET /login`
- **Datos de entrada:** ninguno
- **Respuesta esperada:** HTTP 200, vista `auth/login.blade.php`
- **Resultado real:** PASS ✅
- **Observaciones:** Pantalla con logo Cambio J, campos email y contraseña

### Caso 2 — Login exitoso con credenciales válidas
- **Precondición:** Usuario registrado con rol `super-admin` (email: `admin@cambiosj.com`)
- **Acción:** `POST /login` con `{ email: 'admin@cambiosj.com', password: '...' }`
- **Datos de entrada:** `{ email, password }`
- **Respuesta esperada:** HTTP 302 → `/dashboard`
- **Resultado real:** PASS ✅ (confirma `AuthenticationTest::users can authenticate using the login screen`)
- **Observaciones:** El middleware de dashboard redirige según rol

### Caso 3 — Login fallido con contraseña incorrecta
- **Precondición:** Usuario registrado
- **Acción:** `POST /login` con contraseña incorrecta
- **Datos de entrada:** `{ email: valid@email.com, password: 'wrong' }`
- **Respuesta esperada:** HTTP 302 → `/login` con errores de validación en session
- **Resultado real:** PASS ✅ (confirma `AuthenticationTest::users can not authenticate with invalid password`)
- **Observaciones:** Mensaje genérico "Estas credenciales no coinciden con nuestros registros"

### Caso 4 — Registro de nuevo cliente con código de vendedor válido
- **Precondición:** Existe un vendedor con código `VEND001` (Pedro Martínez)
- **Acción:** `POST /register` con todos los campos requeridos
- **Datos de entrada:** `{ name, email, phone, vendor_code: 'VEND001', password, password_confirmation }`
- **Respuesta esperada:** HTTP 302 → `/dashboard`, usuario autenticado con rol `cliente`, `assigned_seller_id = 1`
- **Resultado real:** PASS ✅
- **Observaciones:** El rol `cliente` debe existir en la BD (creado por `RolesAndPermissionsSeeder`). En tests, se crea manualmente con `Role::firstOrCreate`.

### Caso 5 — Registro fallido con código de vendedor inválido
- **Precondición:** ninguna
- **Acción:** `POST /register` con `vendor_code: 'INVALIDO'`
- **Datos de entrada:** `{ ..., vendor_code: 'INVALIDO' }`
- **Respuesta esperada:** HTTP 422 con error `vendor_code.exists` → "El código de vendedor no existe o no está registrado"
- **Resultado real:** PASS ✅ (validación `exists:sellers,code`)
- **Observaciones:** Validación ocurre en `RegisteredUserController::store()` línea 37

### Caso 6 — Logout
- **Precondición:** Usuario autenticado
- **Acción:** `POST /logout`
- **Datos de entrada:** ninguno (solo CSRF token)
- **Respuesta esperada:** HTTP 302 → `/`, sesión destruida
- **Resultado real:** PASS ✅ (confirma `AuthenticationTest::users can logout`)
- **Observaciones:** `Auth::logout()` + invalidate session + regenerate token

### Caso 7 — Redirección por rol: super-admin
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /dashboard`
- **Datos de entrada:** ninguno
- **Respuesta esperada:** HTTP 302 → `/owner-dashboard`
- **Resultado real:** PASS ✅
- **Observaciones:** Lógica en `routes/web.php` líneas ~75-90

### Caso 8 — Redirección por rol: vendedor
- **Precondición:** Autenticado como vendedor
- **Acción:** `GET /dashboard`
- **Datos de entrada:** ninguno
- **Respuesta esperada:** HTTP 302 → `/seller/bandeja`
- **Resultado real:** PASS ✅

### Caso 9 — Redirección por rol: cliente
- **Precondición:** Autenticado como cliente
- **Acción:** `GET /dashboard`
- **Datos de entrada:** ninguno
- **Respuesta esperada:** HTTP 302 → `/client-dashboard`
- **Resultado real:** PASS ✅

### Caso 10 — Reset de contraseña (flujo de email)
- **Precondición:** Usuario registrado con email válido
- **Acción:** `POST /forgot-password` → recibe token → `POST /reset-password`
- **Datos de entrada:** `{ email }` → `{ token, email, password, password_confirmation }`
- **Respuesta esperada:** Email enviado + contraseña actualizada
- **Resultado real:** PASS ✅ (confirma `PasswordResetTest` y `PasswordUpdateTest`)

## Resultado global: PASS ✅

## Hallazgos y notas
- El registro requiere `vendor_code` y `phone` (campos agregados en REQ-12). Los tests base de Laravel usaban el formulario de registro por defecto y fallaban. Corregidos en `RegistrationTest.php`.
- El rol `cliente` no se siembra automáticamente en el entorno de tests — se debe crear con `Role::firstOrCreate()` antes de registrar.
- Contraseña mínima: 8 caracteres según `Password::defaults()` (no se configuraron restricciones adicionales en AppServiceProvider).
