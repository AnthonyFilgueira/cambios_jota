# Manual de Usuario — Cambio J

**Versión:** REQ-12 (mayo 2026)
**Stack:** Laravel 12 · Alpine.js · Blade · Tailwind CSS · MySQL

---

## Índice

1. [Introducción](#1-introducción)
2. [Acceso al sistema](#2-acceso-al-sistema)
3. [Roles y permisos](#3-roles-y-permisos)
4. [Guía del Cliente](#4-guía-del-cliente)
5. [Guía del Vendedor](#5-guía-del-vendedor)
6. [Guía del Administrador / Dueño](#6-guía-del-administrador--dueño)
7. [Ciclo de vida de una transacción](#7-ciclo-de-vida-de-una-transacción)
8. [Configuración del sistema](#8-configuración-del-sistema)
9. [Motor de Incentivos](#9-motor-de-incentivos)
10. [Reportes y exportaciones](#10-reportes-y-exportaciones)
11. [Campos nuevos de REQ-12](#11-campos-nuevos-de-req-12)
12. [Comandos de mantenimiento](#12-comandos-de-mantenimiento)

---

## 1. Introducción

**Cambio J** es una plataforma de remesas en línea que permite a clientes en Perú enviar dinero a familiares en Venezuela. El sistema gestiona el tipo de cambio PEN (soles) → VES (bolívares digitales) en tiempo real y automatiza el proceso de cobro, validación y pago.

### Flujo general

```
CLIENTE (Perú)          VENDEDOR             ADMINISTRADOR / DUEÑO
━━━━━━━━━━━━━━━━━       ━━━━━━━━━━━━━━━━     ━━━━━━━━━━━━━━━━━━━━━
Crea la transacción  →  Revisa y aprueba  →  Ejecuta el pago
Sube comprobante                              Sube voucher final
Recibe confirmación                          Acredita comisión
```

---

## 2. Acceso al sistema

| Entorno | URL |
|---------|-----|
| Local (Sail) | `http://localhost` |

### Credenciales de demostración

| Usuario | Contraseña | Rol |
|---------|-----------|-----|
| `admin@cambiosj.com` | `password` | super-admin |

> Para cargar datos de demostración: `./vendor/bin/sail artisan db:seed --class=DemoDataSeeder`

---

## 3. Roles y permisos

El sistema usa **5 roles** con Spatie/laravel-permission:

### super-admin
Acceso total al sistema, incluyendo gestión de roles y permisos. Igual que `admin` pero además puede asignar o modificar roles de otros usuarios.

### admin (Dueño)
Gestiona la operación completa del negocio:
- Aprobar/completar transacciones y subir vouchers finales
- Gestionar vendedores, liquidaciones y reportes
- Configurar tasas de cambio, países, bancos, incentivos
- Ver el dashboard con KPIs y exportar reportes

### contador
Solo lectura sobre datos financieros:
- Dashboard, reportes, rankings, exportaciones
- Ver transacciones, ventas, vendedores, liquidaciones
- No puede crear ni modificar nada

### vendedor
Panel propio para gestionar sus transacciones:
- Revisar la bandeja de solicitudes (aprobar / observar / denegar)
- Ver y registrar sus ventas propias
- Consultar su monedero (comisiones acreditadas)
- Ver su código único para compartir con clientes

### cliente
Solo puede operar sus propias transacciones:
- Crear nuevas transacciones de envío
- Ver el historial de sus transacciones
- Corregir transacciones en estado "Observada"
- Cancelar transacciones que aún no han sido completadas

---

## 4. Guía del Cliente

### 4.1 Crear una transacción

**Ruta:** `/transactions/create`

**Paso a paso:**

#### PASO 1 — Seleccionar la tasa de cambio
- Elige el par de divisas disponible (ej. `PEN → VES`)
- El sistema muestra la tasa vigente y actualiza todos los cálculos automáticamente

#### PASO 2 — Ingresar el código del vendedor
- Escribe el código único de tu vendedor (formato `VEN-XXXXXX`)
- El sistema busca las cuentas del negocio asignadas a ese vendedor filtradas por el país de origen
- Si el código es válido, aparecerán las cuentas disponibles para transferir

#### PASO 3 — Elegir método de pago *(REQ-12)*
- Los métodos disponibles se cargan automáticamente según el **país destino** del par seleccionado
- Ejemplos:
  - Si el destino es **Venezuela** → `Transferencia bancaria` / `Pago Móvil`
  - Si el destino es **Perú** → `Transferencia bancaria`

#### PASO 4 — Ingresar el número de operación *(REQ-12)*
- Solo aparece si el método de pago es **Transferencia bancaria**
- Es el número de referencia que da tu banco al hacer la transferencia
- Campo opcional pero recomendado para facilitar la conciliación

#### PASO 5 — Datos del remitente (quien envía)
- **Tipo de documento** *(REQ-12)*: se carga dinámicamente según el país de origen del par
  - Perú: DNI / CE / RUC
  - Venezuela: Cédula (V) / Extranjero (E) / Jurídico (J) / Gubernamental (G)
- **Número de documento**: ingresa el número sin prefijo ni guiones

#### PASO 6 — Datos del destinatario (quien recibe en Venezuela)
- **Tipo de documento** *(REQ-12)*: cargado según el país destino
- **Número de documento**
- **Banco destino**: nombre del banco venezolano
- **Número de cuenta**: solo si el método es transferencia bancaria
- **Tipo de cuenta**: Ahorro / Corriente
- **Teléfono**: número venezolano de contacto

#### PASO 7 — Ver el bono/incentivo *(REQ-12)*
- Si hay reglas de incentivo activas para la moneda seleccionada, aparece el preview del bono
- El bono aumenta el monto que recibe el familiar en bolívares (extra al receptor)
- Solo se muestran reglas aplicables al par de divisas activo

#### PASO 8 — Subir comprobante
- Adjunta la captura o PDF de la transferencia que realizaste
- Formatos aceptados: JPG, PNG, PDF (máx. 10 MB)

#### PASO 9 — Enviar
- La transacción queda en estado **Pendiente**
- Tu vendedor recibirá una notificación automática

---

### 4.2 Ver historial de transacciones

**Ruta:** `/transactions`

- Filtros por estado: Todos / Pendiente / Observada / En proceso / Completada / Cancelada
- Cada tarjeta muestra: monto enviado, monto a recibir, estado y fecha
- Puedes expandir cada transacción para ver el detalle completo, incluyendo el número de operación

### 4.3 Corregir una transacción observada

Cuando tu vendedor encuentra un problema, la transacción pasa a estado **Observada** con un comentario explicativo. Para corregirla:

1. Ingresa a tu historial de transacciones
2. Abre la transacción observada
3. Haz clic en "Corregir transacción"
4. Modifica los datos necesarios y/o sube un nuevo comprobante
5. Al guardar, la transacción vuelve a estado **Pendiente** y se notifica al vendedor

---

## 5. Guía del Vendedor

### 5.1 Bandeja de solicitudes

**Ruta:** `/seller/bandeja`

Es el panel principal del vendedor. Muestra todas las transacciones de sus clientes que requieren acción.

**Estadísticas en cabecera:**
- Solicitudes pendientes de revisión
- Transacciones del mes
- Volumen mensual en soles (PEN)

**Acciones disponibles por transacción:**

| Acción | Condición | Resultado |
|--------|-----------|-----------|
| **Aprobar** | Estado = Pendiente o Observada | Pasa a "En proceso" → el dueño ejecuta el pago |
| **Observar** | Estado = Pendiente | Devuelve al cliente con comentario (min. 10 chars) |
| **Denegar** | Estado = Pendiente o Observada | Cancela definitivamente + notifica al cliente |

> **Nota:** Denegar es permanente. Usar "Observar" cuando el cliente puede corregir algo.

### 5.2 Mi código de vendedor

**Ruta:** `/seller/mi-codigo`

- Muestra tu código único en formato `VEN-XXXXXX`
- Incluye un **código QR** listo para imprimir o compartir
- Los clientes lo ingresan en el formulario de transacción para que sus cuentas del negocio aparezcan automáticamente

**Estadísticas disponibles:**
- Solicitudes pendientes en bandeja
- Transacciones completadas este mes
- Volumen total del mes (PEN)
- Total de clientes activos
- Total histórico de envíos completados

### 5.3 Monedero (Wallet)

**Ruta:** `/wallet`

Muestra el saldo acumulado de comisiones y el historial de movimientos.

**Tipos de movimiento:**
- `commission` — Comisión base acreditada al completar cada transacción
- `extra_comision` — Bono extra de un incentivo activo
- `liquidation` — Retiro o liquidación procesado por el admin
- `adjustment` — Ajuste manual

---

## 6. Guía del Administrador / Dueño

### 6.1 Dashboard

**Ruta:** `/owner-dashboard`

Vista principal con KPIs del negocio. Filtrable por período:

| Período | Descripción |
|---------|-------------|
| Hoy | Solo el día actual |
| Esta semana | Lunes a hoy |
| Este mes | Mes en curso |
| Este año | Año en curso |
| Personalizado | Rango de fechas libre |

**KPIs mostrados:**
- Total vendido (PEN) y cantidad de operaciones
- Comisiones totales (vendedor + dueño)
- Transacciones por estado
- Top vendedores del período
- Evolución diaria del volumen

### 6.2 Gestión de transacciones

**Ruta:** `/transactions/manage`

Vista centralizada de **todas** las transacciones del sistema.

**Acciones del admin sobre cada transacción:**

| Acción | Cuándo usar |
|--------|-------------|
| **Marcar como "En proceso"** | Para escalar una transacción sin esperar al vendedor |
| **Observar** | Para devolver al cliente con un comentario (máx. 1000 chars) |
| **Subir voucher final** | Al confirmar que se realizó el pago en Venezuela |
| **Completar** | Cierra la transacción sin necesidad de voucher (casos expeditos) |
| **Cancelar** | Termina la transacción definitivamente |

> Al **completar** una transacción:
> 1. La comisión base del vendedor se acredita automáticamente en su wallet
> 2. Si hay reglas de tipo `extra_comision` activas para ese vendedor, también se acreditan
> 3. El cliente y el vendedor reciben una notificación por correo

### 6.3 Gestión de vendedores

**Ruta:** `/sellers`

- Crear / editar / eliminar vendedores
- Cada vendedor tiene:
  - Código único auto-generado (`VEN-XXXXXX`)
  - Comisión del vendedor (% del monto enviado)
  - Comisión del dueño (% del monto enviado)
- Ver rendimiento individual y rankings comparativos

---

## 7. Ciclo de vida de una transacción

### Estados posibles

| Estado | Símbolo | Significado |
|--------|---------|-------------|
| `pending` | 🟡 Pendiente | Recién creada, esperando revisión del vendedor |
| `observed` | 🔵 Observada | El vendedor requiere corrección del cliente |
| `processing` | 🟠 En proceso | Aprobada por el vendedor, esperando pago del dueño |
| `completed` | 🟢 Completada | Pago realizado, comisión acreditada |
| `cancelled` | 🔴 Cancelada | Terminada sin completar (permanente) |

### Diagrama de flujo

```
                           ┌─────────────────┐
                           │    PENDIENTE     │◄──────────────┐
                           └────────┬────────┘               │
                                    │                         │
              ┌─────────────────────┼──────────────────────┐  │
              │ Vendedor observa    │ Vendedor aprueba     │  │
              ▼                     ▼                      │  │
    ┌──────────────────┐   ┌─────────────────┐            │  │
    │    OBSERVADA     │   │   EN PROCESO    │            │  │
    └────────┬─────────┘   └────────┬────────┘            │  │
             │                      │                      │  │
             │ Cliente corrige       │ Admin completa       │  │
             └──────────────────────┘                      │  │
                                    │                      │  │
                                    ▼                      │  │
                         ┌──────────────────┐             │  │
                         │   COMPLETADA ✅   │             │  │
                         └──────────────────┘             │  │
                                                          │  │
  ─────────────────────────────────────────────────────   │  │
  Desde PENDIENTE, OBSERVADA o EN PROCESO:                │  │
                                                          │  │
  ┌─────────────┐  Cliente / Vendedor / Admin cancelan   │  │
  │  CANCELADA  │◄────────────────────────────────────────┘  │
  └─────────────┘                                            │
                                                             │
  ─────────────────────────────────────────────────────      │
  Desde OBSERVADA:                                           │
  Cliente corrige ──────────────────────────────────────────┘
```

### ¿Quién puede hacer qué?

| Acción | Cliente | Vendedor | Admin |
|--------|:-------:|:--------:|:-----:|
| Crear transacción | ✅ | ❌ | ❌ |
| Aprobar (→ En proceso) | ❌ | ✅ | ✅ |
| Observar (devolver para corregir) | ❌ | ✅ | ✅ |
| Denegar (= cancelar) | ❌ | ✅ | ✅ |
| Corregir observación | ✅ | ❌ | ❌ |
| Subir voucher final + completar | ❌ | ❌ | ✅ |
| Cancelar | ✅ (propias) | ✅ (propias) | ✅ (todas) |

---

## 8. Configuración del sistema

### 8.1 Países, Bancos y Cuentas

**Ruta:** `/countries`

Cada país tiene 4 pestañas de configuración:

#### Pestaña Bancos
Lista de instituciones financieras del país. Cada banco tiene:
- Nombre, código, estado (activo/inactivo)

#### Pestaña Cuentas del Negocio
Cuentas bancarias propias del negocio para recibir pagos.

| Campo | Descripción |
|-------|-------------|
| Banco | Institución bancaria (del listado del país) |
| Titular | Nombre del titular de la cuenta |
| Número de cuenta | Número completo |
| Tipo de cuenta | Ahorro / Corriente / Móvil |
| DNI/RUC del titular | Identificación fiscal |
| Alias | Nombre corto para identificarla en el sistema |

Las cuentas pueden **asignarse a vendedores específicos**. Cuando un cliente ingresa el código de un vendedor, solo verá las cuentas asignadas a ese vendedor filtradas por el país de origen.

#### Pestaña Tipos de Documento *(REQ-12)*

Configura qué tipos de documento se muestran en el formulario de transacción según el país.

| Campo | Descripción |
|-------|-------------|
| Código | Identificador corto (ej. `DNI`, `CE`, `V`) |
| Nombre | Nombre completo (ej. "Documento Nacional de Identidad") |
| Prefijo | Prefijo que se añade al número (ej. `V-`) |
| Placeholder | Texto de ayuda en el campo (ej. `00000000`) |

**Datos precargados:**

| País | Tipos de documento |
|------|--------------------|
| Perú | DNI · CE (Carné de Extranjería) · RUC |
| Venezuela | V (Cédula venezolana) · E (Extranjero) · J (Jurídico/Empresa) · G (Gubernamental) |

#### Pestaña Métodos de Pago *(REQ-12)*

Configura qué métodos de pago se ofrecen según el país destino de la transacción.

**Datos precargados:**

| País | Métodos |
|------|---------|
| Perú | Transferencia bancaria |
| Venezuela | Transferencia bancaria · Pago Móvil |

---

### 8.2 Tasas de cambio

**Ruta:** `/exchange_rates`

Solo una tasa puede estar **activa** por par de divisas. Cada tasa tiene:

| Campo | Descripción |
|-------|-------------|
| `ves_rate` | Tasa principal PEN → VES (bolívares por sol) |
| `usd_rate` | Tasa BCV USD → VES (para conversor USD) |
| `eur_rate` | Tasa BCV EUR → VES (para conversor EUR) |

> La tasa activa se muestra en el simulador público y en el formulario de transacción.

---

### 8.3 Monedas y pares de divisas

**Ruta:** `/currencies` · `/currency-pairs`

- Crear las monedas del sistema (PEN, VES, USD, EUR) con símbolo y flag emoji
- Crear los pares activos (ej. PEN ↔ VES) que luego se usan en las tasas de cambio

---

## 9. Motor de Incentivos

**Ruta:** `/admin/incentives`

El sistema de incentivos aplica bonos automáticamente a transacciones que cumplen ciertas condiciones. **No requiere intervención manual** en cada operación.

### 9.1 Tipos de incentivo

| Tipo | Qué hace | Cuándo se aplica |
|------|----------|-----------------|
| **Extra al receptor** | El familiar en Venezuela recibe más bolívares | Al crear la transacción |
| **Extra comisión al vendedor** | El vendedor recibe comisión adicional | Al completar la transacción |
| **Descuento de tasa** | Reduce la tasa aplicada | Configurado pero no activo |

### 9.2 Crear un incentivo

#### Selector de moneda *(REQ-12)*
Elige a qué moneda de origen aplica el incentivo:
- **Vacío / "Todas las monedas"** → se aplica a cualquier par (incentivo global)
- **Selecciona una moneda** → solo activa cuando el cliente usa ese par de divisas

> Ejemplo: Si creas un incentivo para `PEN`, solo se mostrará cuando el cliente esté enviando soles.

#### ¿Qué hace?
- 🎁 Extra al receptor
- ⭐ Extra comisión al vendedor

#### ¿A quién aplica?

| Opción | Descripción |
|--------|-------------|
| Todos los clientes | Cualquier persona con rol `cliente` |
| Clientes nuevos | Solo usuarios con 0 transacciones completadas |
| Cliente específico | Un ID de usuario concreto |
| Todos los vendedores | Cualquier persona con rol `vendedor` |
| Vendedor específico | Un ID de vendedor concreto |
| Clientes de un vendedor | Todos los clientes asignados a un vendedor específico |

#### ¿Cuánto?
- **Monto fijo** (ej. S/ 10): el símbolo se actualiza dinámicamente según la moneda seleccionada
- **Porcentaje** (ej. 5%): calculado sobre el monto enviado en PEN

#### Condiciones opcionales
- Monto mínimo de envío
- Máximo de usos totales (ej. primeros 100 clientes)
- Fecha de inicio y fin de vigencia
- Solo para clientes nuevos (checkbox)

### 9.3 Cómo se ve en el formulario *(REQ-12)*

El preview de incentivos en el formulario de transacción y en el simulador público ahora **filtra automáticamente** los bonos según el par de divisas seleccionado:

- Si el par es `PEN → VES`, solo se muestran reglas para `PEN` + las globales (sin moneda)
- El badge de la regla muestra la moneda aplicable o "Global"

### 9.4 Cálculo automático

Al crear la transacción:
```
amount_ves = (amount_pen + bonus_pen) × ves_rate
```

Al completar (para bonos de comisión):
```
wallet_vendedor += base_commission + bonus_comision
```

---

## 10. Reportes y exportaciones

**Ruta:** `/reports`

La sección de reportes tiene **dos pestañas**:

### Pestaña 1 — Transacciones

Vista general con filtros y totales.

**Filtros disponibles:**
- Rango de fechas (inicio / fin)
- Estado (Todos / Pendiente / En proceso / Completada / Cancelada)
- Vendedor específico

**Tarjetas de resumen:**
- Total de transacciones en el período
- Monto total en PEN
- Monto total en VES
- Transacciones completadas

**Exportar a Excel:** Descarga un archivo `.xlsx` con todas las columnas incluyendo el número de operación.

---

### Pestaña 2 — Conciliación Bancaria *(REQ-12)*

**Ruta:** `/reports/conciliation`

Diseñada para cruzar los pagos del sistema con el extracto bancario real.

**Muestra solo:**
- Transacciones de tipo **Transferencia bancaria**
- En estados **En proceso** o **Completada**

**Indicadores visuales:**
- Filas con fondo **amarillo** → transacciones sin número de operación (pendiente de registrar el Nº de referencia)
- Filas normales → tienen número de operación registrado

**Columnas exportadas:** ID · Fecha · Titular + Documento · Banco origen · Nº de cuenta · Nº de operación · Monto PEN · Estado

**Exportar a Excel:** Genera el archivo `.xlsx` para conciliación.

---

## 11. Campos nuevos de REQ-12

Todos los siguientes campos se incorporaron en la última actualización mayor del sistema:

### En transacciones

| Campo | Descripción | Dónde se muestra |
|-------|-------------|-----------------|
| `operation_number` | Número de referencia bancaria de la transferencia | Formulario (solo transferencia) · Confirmación · Detalle · Reporte de conciliación |
| `sender_document_type` | Tipo de documento del remitente (DNI, CE, V, E…) | Formulario · Detalle de la transacción |
| `sender_document_number` | Número del documento del remitente | Formulario · Detalle |
| `recipient_document_type` | Tipo de documento del destinatario | Formulario · Detalle |
| `recipient_document_number` | Número del documento del destinatario | Formulario · Detalle |

### En incentivos

| Campo | Descripción |
|-------|-------------|
| `currency_id` | Moneda a la que aplica la regla (null = todas) |

---

## 12. Comandos de mantenimiento

Todos los comandos se ejecutan con el prefijo `./vendor/bin/sail artisan`:

```bash
# Aplicar migraciones pendientes
./vendor/bin/sail artisan migrate

# Cargar tipos de documento (Perú y Venezuela)
./vendor/bin/sail artisan db:seed --class=DocumentTypeSeeder

# Cargar métodos de pago (transferencia / pago móvil por país)
./vendor/bin/sail artisan db:seed --class=PaymentMethodSeeder

# Cargar datos de demostración (vendedores, clientes, transacciones de prueba)
./vendor/bin/sail artisan db:seed --class=DemoDataSeeder

# Ejecutar todos los tests
./vendor/bin/sail artisan test

# Ver logs en tiempo real
./vendor/bin/sail artisan pail

# Limpiar caché de vistas
./vendor/bin/sail artisan view:clear

# Limpiar caché de configuración
./vendor/bin/sail artisan config:clear
```

---

## Apéndice — Diagrama de roles por sección

| Sección | super-admin | admin | contador | vendedor | cliente |
|---------|:-----------:|:-----:|:--------:|:--------:|:-------:|
| Dashboard principal | ✅ | ✅ | ✅ | ❌ | ❌ |
| Dashboard vendedor | ✅ | ✅ | ❌ | ✅ | ❌ |
| Dashboard cliente | ✅ | ✅ | ❌ | ❌ | ✅ |
| Crear transacción | ✅ | ✅ | ❌ | ❌ | ✅ |
| Gestionar transacciones | ✅ | ✅ | 👁️ | ✅ (propias) | 👁️ (propias) |
| Bandeja de vendedor | ✅ | ✅ | ❌ | ✅ | ❌ |
| Reportes y exportaciones | ✅ | ✅ | ✅ | ❌ | ❌ |
| Gestión de vendedores | ✅ | ✅ | 👁️ | ❌ | ❌ |
| Configurar países / bancos | ✅ | ✅ | 👁️ | ❌ | ❌ |
| Configurar tasas de cambio | ✅ | ✅ | 👁️ | 👁️ | ❌ |
| Motor de incentivos | ✅ | ✅ | ❌ | ❌ | ❌ |
| Liquidaciones | ✅ | ✅ | 👁️ | ❌ | ❌ |
| Auditoría y logs | ✅ | ✅ | ❌ | ❌ | ❌ |
| Gestión de roles | ✅ | ❌ | ❌ | ❌ | ❌ |

> ✅ = acceso completo · 👁️ = solo lectura · ❌ = sin acceso

---

*Documento generado automáticamente a partir del estado del código en la rama `feat/REQ-12-multi-mejoras`. Para actualizar, revisar los cambios en `routes/web.php`, los controllers y los modelos correspondientes.*
