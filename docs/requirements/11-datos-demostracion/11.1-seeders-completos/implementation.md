# REQ 11.1: Implementación de Seeder de Datos de Demostración

**Fecha:** 2026-04-20  
**Tiempo:** ~1h  
**Estado:** ✅ COMPLETADO

---

## Resumen

Implementación completa del `DemoDataSeeder.php` con **30 ventas** distribuidas en todos los estados posibles del sistema, siguiendo la especificación de `requirement-30-ventas.md`.

---

## Implementación

### Archivo Creado

**`database/seeders/DemoDataSeeder.php`**

### Estructura del Seeder

El seeder se divide en 4 métodos principales:

```php
1. crearUsuarios()      // 1 admin + 4 clientes
2. crearVendedores()    // 4 vendedores con códigos fijos
3. crearVentas()        // 30 ventas en 5 estados diferentes
4. crearObservaciones() // Logs de ventas observadas/rechazadas
```

---

## Datos Generados

### 1. Usuarios (5 total)

| Tipo | Nombre | Email | Password |
|------|--------|-------|----------|
| Admin | Admin Cambio J | cambios_jota@innodite.com | password |
| Cliente | Juan Pérez | juan.perez@gmail.com | password |
| Cliente | María González | maria.gonzalez@gmail.com | password |
| Cliente | Carlos Rodríguez | carlos.rodriguez@gmail.com | password |
| Cliente | Ana Torres | ana.torres@gmail.com | password |

### 2. Vendedores (4 total)

| Código | Nombre | Comisión Vendedor | Comisión Dueño |
|--------|--------|-------------------|----------------|
| VEND001 | Pedro Martínez | 5% | 15% |
| VEND002 | Ana López | 7% | 13% |
| VEND003 | Luis Torres | 10% | 10% |
| VEND004 | Rosa Fernández | 5% | 15% |

**Nota:** VEND004 (Rosa) se creó pero no tiene ventas asignadas (vendedor inactivo).

### 3. Ventas (30 total)

#### Distribución por Estado

| Estado | Cantidad | Porcentaje | Características |
|--------|----------|------------|-----------------|
| pending_seller | 4 | 13% | Sin comprobante, creadas recientemente |
| approved | 6 | 20% | Con snapshots de comisiones |
| completed | 12 | 40% | Con snapshots + comprobante |
| rejected | 4 | 13% | Con observación del motivo |
| observed | 4 | 13% | Con observación para corrección |

#### Distribución Temporal

| Período | Cantidad |
|---------|----------|
| Hoy (últimas 6h) | 8 ventas |
| Ayer | 2 ventas |
| Hace 2-3 días | 6 ventas |
| Hace 4-7 días | 6 ventas |
| Hace 8-13 días | 8 ventas |

#### Distribución por Monto

| Rango | Cantidad |
|-------|----------|
| S/ 100-500 | 13 ventas (43%) |
| S/ 501-1000 | 8 ventas (27%) |
| S/ 1001-2000 | 6 ventas (20%) |
| S/ 2001+ | 3 ventas (10%) |

**Monto Total:** S/ 38,500.00

### 4. Observaciones/Logs (8 registros)

Cada venta en estado `observed` o `rejected` tiene un registro en `sale_logs` con:
- Usuario que realizó la acción (admin)
- Cambio de estado
- Comentario/motivo

---

## Características Implementadas

### ✅ Snapshots de Comisiones

Para ventas en estado `approved` y `completed`:
```php
'seller_commission_percent' => 5.0,
'admin_commission_percent' => 15.0,
'seller_commission_amount' => 50.00,  // Calculado: 1000 * 5%
'admin_commission_amount' => 150.00,  // Calculado: 1000 * 15%
```

Esto asegura la **inmutabilidad** de las comisiones aunque cambien en el vendedor.

### ✅ Comprobantes

Ventas completadas tienen el campo `voucher_path` poblado:
```php
'voucher_path' => 'vouchers/demo_67890abcdef.pdf'
```

### ✅ Observaciones

Ventas observadas/rechazadas tienen el campo `admin_observation` con mensajes realistas:
- "Falta número de cuenta del destinatario"
- "Comprobante de identidad ilegible, reenviar"
- "Monto sospechoso, verificar identidad del cliente"
- etc.

### ✅ Fechas Realistas

Uso de `Carbon` para generar fechas distribuidas:
```php
Carbon::now()->subHours(2)   // Hoy -2h
Carbon::now()->subDays(5)    // Hace 5 días
```

---

## Comandos de Uso

### Ejecutar el Seeder

```bash
./vendor/bin/sail artisan db:seed --class=DemoDataSeeder
```

### Limpiar y Volver a Seedar

```bash
./vendor/bin/sail artisan migrate:fresh --seed
# O solo este seeder:
./vendor/bin/sail artisan migrate:fresh
./vendor/bin/sail artisan db:seed --class=DemoDataSeeder
```

---

## Salida del Seeder

```
🚀 Iniciando seeder de datos de demostración...
👤 Creando usuarios...
  ✓ Admin + 4 clientes creados
🏅 Creando vendedores...
  ✓ 4 vendedores creados (VEND001-VEND004)
💰 Creando 30 ventas...
  ✓ 30 ventas creadas
    - PENDING: 4
    - APPROVED: 6
    - COMPLETED: 12
    - REJECTED: 4
    - OBSERVED: 4
📝 Creando observaciones/logs...
  ✓ Logs de observaciones/rechazos creados
✅ Seeder completado exitosamente!

📊 RESUMEN DE DATOS CREADOS:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
👥 Usuarios: 6
🏅 Vendedores: 4
💰 Ventas: 30
📝 Logs: 8

🔑 CREDENCIALES DE ACCESO:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📧 Admin: cambios_jota@innodite.com
🔒 Password: password
```

---

## Decisiones Técnicas

### 1. Códigos Fijos para Vendedores

En lugar de dejar que el modelo auto-genere códigos aleatorios, se especifican códigos fijos (`VEND001-VEND004`) para facilitar:
- Identificación en demos
- Testing predecible
- Documentación clara

### 2. Simplificación del Esquema

El `requirement-30-ventas.md` incluía destinatarios venezolanos con datos completos (cédula, banco, cuenta), pero el esquema actual de `sales` no tiene esos campos. 

**Decisión:** Enfocar el seeder en los campos existentes en BD:
- `seller_id`, `amount`, `sale_date`, `approval_status`
- `voucher_path`, `admin_observation`
- Snapshots de comisiones

Los datos de destinatarios se pueden agregar en una futura migración si se necesitan.

### 3. Método `crearVenta()` Parametrizado

```php
private function crearVenta(
    $monto, 
    $estado, 
    $fecha, 
    $vendedorCode, 
    $conSnapshots = false, 
    $conComprobante = false, 
    $observacion = null
)
```

Permite crear ventas con diferentes combinaciones de datos de forma concisa.

### 4. Sin Faker

No se usó la librería Faker porque:
- Los datos son específicos según `requirement-30-ventas.md`
- Se prefiere consistencia para demos (mismos datos cada vez)
- Nombres y emails hardcodeados facilitan login en demos

---

## Casos Cubiertos

| Caso | Estado | Cubierto |
|------|--------|----------|
| Venta pendiente del vendedor | ✅ | 4 ventas |
| Venta aprobada con comisiones | ✅ | 6 ventas |
| Venta completada con comprobante | ✅ | 12 ventas |
| Venta rechazada con motivo | ✅ | 4 ventas |
| Venta observada con comentario | ✅ | 4 ventas |
| Snapshots de comisiones | ✅ | 18 ventas (approved + completed) |
| Logs de auditoría | ✅ | 8 logs |
| Vendedor sin ventas | ✅ | VEND004 |
| Ventas de diferentes montos | ✅ | S/ 100 - S/ 10,000 |
| Distribución temporal | ✅ | Últimos 13 días |

---

## Mejoras Futuras

1. **Migración de Destinatarios:**
   - Agregar tabla `recipients` con datos venezolanos
   - Relacionar `sales.recipient_id`
   - Campos: cédula, banco, cuenta, teléfono

2. **Tasas Históricas:**
   - Crear snapshots de tasas en ventas antiguas
   - Campo `exchange_rate_snapshot` en sales

3. **Monedero y Liquidaciones:**
   - Agregar `WalletTransaction` para comisiones acreditadas
   - Agregar `Liquidation` para pagos a vendedores

4. **Comando Artisan:**
   - `php artisan demo:reset` que haga `migrate:fresh + DemoDataSeeder`
   - Con confirmación interactiva

---

## Testing

### Verificación Manual

1. Login como admin: `cambios_jota@innodite.com` / `password`
2. Ver dashboard (debería mostrar 30 ventas)
3. Filtrar por estado (pending, approved, etc.)
4. Ver detalle de venta observada (debe tener comentario)
5. Ver detalle de venta completada (debe tener comprobante simulado)

### Verificación por Consola

```bash
./vendor/bin/sail artisan tinker
>>> \App\Models\Sale::count();
>>> \App\Models\Sale::where('approval_status', 'completed')->count();
>>> \App\Models\Seller::where('code', 'VEND001')->first()->name;
```

---

## Archivos Modificados

```
database/seeders/DemoDataSeeder.php          [EDITADO]
```

## Archivos Creados

```
docs/requirements/11-datos-demostracion/11.1-seeders-completos/task.md
docs/requirements/11-datos-demostracion/11.1-seeders-completos/implementation.md
```

---

## Resultado

✅ **Seeder completamente funcional** con 30 ventas realistas que cubren todos los estados del sistema.

El sistema ahora tiene datos de demostración consistentes para:
- Presentaciones a clientes
- Videos demostrativos
- Testing manual
- Capacitación de usuarios
- Desarrollo de nuevas features

---

**Próximo paso:** Agregar los archivos al git y hacer commit.
