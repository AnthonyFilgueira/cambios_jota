# REQ 11: Sistema de Datos de Demostración

**Estado:** ⏸️ PENDIENTE  
**Horas estimadas:** 8h  
**Horas completadas:** 0h  
**Horas restantes:** 8h  
**Prioridad:** ALTA

---

## Descripción General

Crear un sistema completo de datos de demostración (seeders) que permita popular la base de datos con escenarios realistas para presentaciones, videos demo y pruebas del sistema completo.

---

## Objetivo

Generar datos de prueba que demuestren **todos los casos de uso posibles** del sistema Cambio J, incluyendo:
- Usuarios con diferentes roles
- Vendedores con variedad de comisiones
- Ventas en todos los estados posibles
- Clientes recurrentes y nuevos
- Transacciones de monedero
- Liquidaciones por diferentes métodos
- Tasas de cambio históricas
- Observaciones y feedback
- Escenarios edge case

---

## Alcance

### 1. Usuarios de Demostración

**Tipos de usuarios:**
- **Admin/Dueño:** 1 usuario
  - Email: `dueno@cambioj.com`
  - Password: `password`
  - Acceso completo al sistema

- **Vendedores:** 5-8 usuarios
  - Emails: `vendedor1@cambioj.com`, `vendedor2@cambioj.com`, etc.
  - Password: `password`
  - Diferentes niveles de actividad

- **Clientes:** 15-20 usuarios opcionales
  - Para simular historial de transacciones

### 2. Vendedores con Perfiles Variados

**Vendedor A - "Top Performer":**
- Comisión vendedor: 3%
- Comisión dueño: 2%
- 25+ ventas aprobadas
- Saldo monedero: S/. 800
- Sin liquidaciones recientes

**Vendedor B - "Consistente":**
- Comisión vendedor: 2.5%
- Comisión dueño: 2.5%
- 15 ventas aprobadas
- Varias liquidaciones registradas
- Saldo: S/. 200

**Vendedor C - "Nuevo":**
- Comisión vendedor: 2%
- Comisión dueño: 3%
- 3-5 ventas (algunas pendientes)
- Sin liquidaciones
- Saldo: S/. 50

**Vendedor D - "Problemático":**
- Ventas observadas
- Ventas rechazadas
- Historial de feedback

**Vendedor E - "Inactivo":**
- 0 ventas en últimos 30 días
- Saldo: S/. 0

### 3. Ventas en Todos los Estados

**Estado: pending_seller (5-8 ventas)**
- Creadas por vendedores
- Esperando subir comprobante
- Fechas: últimos 2 días

**Estado: pending_admin (8-12 ventas)**
- Comprobante subido
- Esperando aprobación del dueño
- Fechas: últimos 3-5 días

**Estado: approved (30-40 ventas)**
- Aprobadas por el dueño
- Comisiones acreditadas al monedero
- Fechas: distribuidas en últimos 60 días

**Estado: completed (20-30 ventas)**
- Finalizadas
- Fechas: hace 30-90 días

**Estado: rejected (3-5 ventas)**
- Rechazadas con motivo
- Ejemplos de motivos:
  - "Comprobante ilegible"
  - "Datos incorrectos"
  - "Transacción duplicada"

**Estado: observed (5-8 ventas)**
- Con observaciones
- Ejemplos:
  - "Verificar monto con cliente"
  - "Faltan datos del destinatario"
  - "Comprobante borroso - resubir"

### 4. Distribución Temporal

**Ventas por período:**
- Últimos 7 días: 15-20 ventas
- Últimos 30 días: 50-60 ventas
- Últimos 90 días: 100+ ventas
- Más de 90 días: 20-30 ventas (histórico)

**Horarios realistas:**
- Concentración en horario laboral (9am - 6pm)
- Algunos fines de semana
- Variación en días de quincena/fin de mes

### 5. Montos Variados

**Rangos de monto:**
- Pequeñas: S/. 100 - S/. 500 (40%)
- Medianas: S/. 500 - S/. 2,000 (40%)
- Grandes: S/. 2,000 - S/. 10,000 (15%)
- Muy grandes: S/. 10,000+ (5%)

**Ticket promedio esperado:** ~S/. 1,200

### 6. Clientes

**Tipos:**
- **Recurrentes (5-8 clientes):**
  - 3+ transacciones cada uno
  - Diferentes vendedores
  - Montos variables

- **Ocasionales (10-15 clientes):**
  - 1-2 transacciones
  - Distribuidos entre vendedores

**Datos realistas:**
- Nombres peruanos/venezolanos
- DNI/Cédula válidos (formato)
- Teléfonos con formato correcto
- Emails únicos

### 7. Monedero y Liquidaciones

**Transacciones de monedero:**
- Automáticas: Al aprobar cada venta
- Ajustes manuales: 2-3 casos
  - Corrección de error
  - Bono especial
  - Penalización

**Liquidaciones:**
- **Por efectivo:** 5-8 liquidaciones
- **Por transferencia:** 8-12 liquidaciones
- **Por Yape:** 3-5 liquidaciones
- **Por Plin:** 2-3 liquidaciones
- **Otro método:** 1-2 liquidaciones

**Montos:**
- Desde S/. 50 hasta S/. 2,000
- Fechas distribuidas en últimos 60 días

### 8. Tasas de Cambio

**Tasas históricas:**
- Tasa actual (activa): 1 registro
- Tasas anteriores: 10-15 registros
- Variación realista: Bs. 7.30 - Bs. 7.55 por PEN
- Fechas: distribuidas en últimos 90 días

### 9. Observaciones y Feedback

**Observaciones en ventas:**
- Variedad de mensajes
- Algunas resueltas (sale aprobada después)
- Algunas sin resolver (sale sigue observed)

**Tipos de observación:**
- "Verificar monto"
- "Comprobante borroso"
- "Falta información"
- "Cliente reportó diferencia"

### 10. Escenarios Edge Case

**Casos especiales a incluir:**
- Venta de monto muy alto (S/. 50,000)
- Venta de monto muy bajo (S/. 50)
- Vendedor con 0 ventas pero cuenta activa
- Cliente con transacciones en misma fecha
- Liquidación por monto exacto del saldo
- Liquidación parcial (queda saldo)
- Múltiples observaciones en misma venta
- Cambio de tasa en mismo día

---

## Tareas del Requerimiento

| # | Tarea | Horas | Descripción |
|---|-------|-------|-------------|
| 11.1 | Seeder de usuarios | 1h | Users (admin, vendedores, clientes) |
| 11.2 | Seeder de vendedores | 1h | Sellers con perfiles variados |
| 11.3 | Seeder de ventas | 2.5h | Sales en todos los estados + distribución temporal |
| 11.4 | Seeder de monedero | 1h | WalletTransactions con trazabilidad |
| 11.5 | Seeder de liquidaciones | 1h | Liquidations por diferentes métodos |
| 11.6 | Seeder de tasas | 0.5h | ExchangeRates históricas |
| 11.7 | Comando de reset completo | 1h | Artisan command para limpiar + seed |
| 11.8 | Documentación de datos | 1h | README con credenciales y escenarios |

**Total:** 9h

---

## Estructura de Archivos

```
database/seeders/
├── DemoSeeder.php              # Seeder principal (orquestador)
├── DemoUsersSeeder.php         # Usuarios (admin, vendedores)
├── DemoSellersSeeder.php       # Vendedores con perfiles
├── DemoSalesSeeder.php         # Ventas en todos los estados
├── DemoWalletSeeder.php        # Transacciones monedero
├── DemoLiquidationsSeeder.php  # Liquidaciones
├── DemoExchangeRatesSeeder.php # Tasas históricas
└── README.md                   # Documentación de datos demo

app/Console/Commands/
└── ResetDemoData.php           # Comando para reset + seed
```

---

## Comandos a Implementar

### Comando 1: Reset Completo
```bash
php artisan demo:reset
```
**Funcionalidad:**
1. Confirmar acción (prompt)
2. Limpiar todas las tablas
3. Ejecutar migraciones fresh
4. Ejecutar DemoSeeder
5. Mostrar resumen de datos creados

### Comando 2: Solo Seeders
```bash
php artisan db:seed --class=DemoSeeder
```
**Funcionalidad:**
- Ejecutar todos los seeders demo en orden
- No afecta estructura de BD

### Comando 3: Seeders Específicos
```bash
php artisan db:seed --class=DemoSalesSeeder
```
**Funcionalidad:**
- Regenerar solo un tipo de datos

---

## Datos de Acceso para Demo

### Usuario Admin/Dueño
```
Email: dueno@cambioj.com
Password: password
Rol: Administrador
Acceso: Completo
```

### Vendedores
```
Email: ana.gomez@cambioj.com    | Password: password | Top Performer
Email: carlos.ruiz@cambioj.com  | Password: password | Consistente
Email: lucia.torres@cambioj.com | Password: password | Nueva
Email: jose.silva@cambioj.com   | Password: password | Problemático
Email: maria.lopez@cambioj.com  | Password: password | Inactiva
```

---

## Métricas Esperadas (Post-Seed)

**Dashboard del Dueño (mes actual):**
- Total vendido: ~S/. 80,000 - S/. 100,000
- Comisiones vendedores: ~S/. 2,000 - S/. 2,500
- Comisiones dueño: ~S/. 2,000 - S/. 2,500
- Cantidad de ventas: 50-60
- Ticket promedio: ~S/. 1,200
- Total liquidado: S/. 3,000 - S/. 4,000
- Saldo en monederos: S/. 1,500 - S/. 2,000

**Rankings:**
- Top 1: Ana Gómez (~25 ventas, ~S/. 30,000)
- Top 2: Carlos Ruiz (~15 ventas, ~S/. 18,000)
- Top 3: Lucía Torres (~12 ventas, ~S/. 15,000)

**Estados de ventas:**
- Pending seller: 5-8
- Pending admin: 8-12
- Approved: 30-40
- Completed: 20-30
- Rejected: 3-5
- Observed: 5-8

---

## Beneficios

1. **Presentaciones:** Datos realistas para demos
2. **Testing:** Escenarios completos para QA
3. **Desarrollo:** Reset rápido para probar features
4. **Capacitación:** Entorno de práctica
5. **Videos:** Material para tutoriales/marketing
6. **Debugging:** Reproducir casos específicos

---

## Consideraciones

### Rendimiento
- Seeders optimizados con `insert()` en vez de `create()`
- Usar transacciones para rollback rápido
- Deshabilitar timestamps cuando no se necesiten

### Datos Realistas
- Usar Faker para nombres, emails, teléfonos
- Datos peruanos y venezolanos
- Fechas coherentes (venta antes de aprobación)
- Montos con decimales realistas

### Trazabilidad
- Mantener relaciones consistentes:
  - Sale → WalletTransaction (por comisión)
  - Liquidation → WalletTransaction (por pago)
- Saldos cuadrados (balance_after correcto)

### Idempotencia
- Comando `demo:reset` debe poder ejecutarse múltiples veces
- Limpiar datos previos antes de seed
- Truncate en orden correcto (foreign keys)

---

## Casos de Uso del Sistema Demo

### Para Video Demo
```bash
php artisan demo:reset
# Acceder como dueno@cambioj.com
# Mostrar dashboard con métricas reales
# Navegar a ventas pendientes
# Aprobar una venta
# Ver actualización de monedero
# Crear una liquidación
```

### Para Testing Manual
```bash
php artisan demo:reset
# Probar flujo de vendedor (ana.gomez@cambioj.com)
# Crear venta → Subir comprobante
# Probar flujo de admin (dueno@cambioj.com)
# Aprobar/Rechazar/Observar ventas
```

### Para Desarrollo
```bash
php artisan demo:reset
# Reset rápido del estado
# Probar nueva feature con datos frescos
```

---

## Próximos Pasos

1. Crear seeders en orden de dependencias
2. Implementar comando `demo:reset`
3. Documentar credenciales en README
4. Probar datos generados
5. Validar métricas del dashboard
6. Crear video demo del sistema

---

**Fecha creación:** 2026-04-20  
**Última actualización:** 2026-04-20  
**Solicitado por:** Usuario - Para demostración del sistema
