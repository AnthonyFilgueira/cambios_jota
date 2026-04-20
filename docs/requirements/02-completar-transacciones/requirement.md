# REQ 2: Completar Módulo de Transacciones

## Contexto de Negocio

El módulo de autenticación está al 90% (Laravel Breeze instalado). Falta implementar el **historial de transacciones** para que los usuarios puedan:
- Ver todas sus transacciones (envíos de dinero)
- Ver el estado de cada transacción (Pendiente/En proceso/Completado)
- Ver un widget de consumo acumulado
- Recibir notificaciones sobre cambios de estado

## Alcance Funcional

✅ **Ya implementado (90%):**
- Sistema de registro/login
- Perfil de usuario editable
- Recuperación de contraseña
- Verificación de email

❌ **Falta implementar (10%):**
- Modelo `Transaction` con campos necesarios
- `TransactionController` para listar transacciones
- Vista `transactions/index.blade.php` con Alpine.js
- Widget de consumo acumulado
- Badges de estado visual
- Rutas protegidas con middleware auth

## Stack Técnico

**Backend:**
- `app/Models/Transaction.php` (nuevo)
- `app/Http/Controllers/TransactionController.php` (nuevo)
- `database/migrations/xxxx_create_transactions_table.php` (nuevo)

**Frontend:**
- `resources/views/transactions/index.blade.php` (nuevo con Alpine.js)
- Aplicar paleta Cambio J (cj-morado, cj-turquesa, cj-rosa)

**Rutas:**
- `GET /transactions` → index
- Middleware: `auth`

## Campos del modelo Transaction

```php
- id (bigint, primary key)
- user_id (bigint, foreign key → users)
- seller_id (bigint, nullable, foreign key → sellers)
- amount_pen (decimal 10,2) // Monto enviado en soles
- amount_ves (decimal 12,2) // Monto recibido en bolívares
- exchange_rate_id (bigint, foreign key → exchange_rates)
- status (enum: 'pending', 'processing', 'completed', 'cancelled')
- notes (text, nullable)
- created_at, updated_at
```

## Tareas a implementar

### 2.1: Crear modelo Transaction con migración
- Generar modelo y migración
- Definir relaciones (belongsTo User, Seller, ExchangeRate)
- Definir fillable y casts

### 2.2: Crear TransactionController
- Método `index()` para listar transacciones del usuario autenticado
- Ordenar por created_at DESC
- Eager loading de relaciones

### 2.3: Crear vista transactions/index.blade.php
- Layout base con navegación
- Tabla responsive de transacciones
- Badges de estado con colores
- Alpine.js para interactividad

### 2.4: Crear widget de consumo acumulado
- Calcular suma total de amount_pen
- Mostrar en card destacada
- Formato con separadores de miles

### 2.5: Aplicar paleta Cambio J
- Usar cj-morado-profundo para headers
- Usar cj-turquesa para badges "Completado"
- Usar cj-rosa para badges "Pendiente"
- Usar cj-texto para contenido

### 2.6: Registrar rutas
- Agregar ruta protegida en routes/web.php
- Middleware auth
- Agregar enlace en navigation.blade.php

### 2.7: Crear seeder de datos de prueba
- Factory para Transaction
- Generar 10-15 transacciones de prueba
- Diferentes estados

## Criterios de Aceptación

1. [ ] Usuario autenticado puede acceder a `/transactions`
2. [ ] Se muestran todas las transacciones del usuario
3. [ ] Cada transacción muestra: fecha, monto PEN, monto VES, estado
4. [ ] Estados visibles con badges de colores (Pendiente/En proceso/Completado)
5. [ ] Widget muestra suma total de PEN enviados
6. [ ] Vista responsive mobile-first
7. [ ] Usa paleta de colores Cambio J
8. [ ] Ordenadas de más reciente a más antigua

## Plan de Pruebas

1. Crear usuario de prueba
2. Ejecutar seeder para crear transacciones
3. Login con usuario de prueba
4. Acceder a `/transactions`
5. Verificar que muestra transacciones correctamente
6. Verificar widget de consumo acumulado
7. Verificar badges de colores

## Dependencias

- Requiere: REQ 1 completado (modelo ExchangeRate) ✅
- Requiere: Autenticación funcionando ✅
- Requiere: Modelo Seller (existe) ✅

## Estimación

- **Total:** 5 horas
- **Distribución:** 7 tareas × ~40 min c/u
