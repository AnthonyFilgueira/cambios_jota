# Manual de Usuario — Cambio J

**Versión:** REQ-12 + Multi-Corredor (mayo 2026)
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
13. [Notas técnicas de la UX dinámica](#13-notas-técnicas-de-la-ux-dinámica)

---

## 1. Introducción

**Cambio J** es una plataforma de remesas en línea **multi-corredor** que permite a clientes desde varios países latinoamericanos enviar dinero a familiares en Venezuela o Perú. El sistema gestiona tipos de cambio en tiempo real para múltiples pares de divisas y automatiza el proceso de cobro, validación y pago.

### Países y corredores activos

| País de origen | Moneda | Destinos disponibles |
|----------------|--------|---------------------|
| 🇵🇪 Perú | PEN | Venezuela (VES) |
| 🇦🇷 Argentina | ARS | Venezuela (VES) · Perú (PEN) |
| 🇨🇱 Chile | CLP | Venezuela (VES) · Perú (PEN) |
| 🇨🇴 Colombia | COP | Venezuela (VES) · Perú (PEN) |
| 🇧🇷 Brasil | BRL | Venezuela (VES) · Perú (PEN) |
| — | USD | Perú (PEN) |

### Pares de divisas activos

| Par | Tasa aproximada | Descripción |
|-----|----------------|-------------|
| PEN → VES | 1 PEN = 173.71 Bs. | Perú a Venezuela |
| ARS → VES | 1 ARS = 3.00 Bs. | Argentina a Venezuela |
| ARS → PEN | 1 ARS = 0.0031 PEN | Argentina a Perú |
| CLP → VES | 1 CLP = 0.55 Bs. | Chile a Venezuela |
| CLP → PEN | 1 CLP = 0.0045 PEN | Chile a Perú |
| COP → VES | 1 COP = 0.12 Bs. | Colombia a Venezuela |
| COP → PEN | 1 COP = 0.00082 PEN | Colombia a Perú |
| BRL → PEN | 1 BRL = 0.72 PEN | Brasil a Perú |
| BRL → VES | 1 BRL = 86.20 Bs. | Brasil a Venezuela |
| USD → PEN | 1 USD = 3.72 PEN | USD a Perú |

> Las tasas son referenciales. El valor exacto lo determina la tasa activa configurada en el sistema.

### Flujo general

```
CLIENTE (cualquier país)   VENDEDOR             ADMINISTRADOR / DUEÑO
━━━━━━━━━━━━━━━━━━━━━      ━━━━━━━━━━━━━━━━     ━━━━━━━━━━━━━━━━━━━━━
Elige corredor          →  Revisa y aprueba  →  Ejecuta el pago
Sube comprobante                               Sube voucher final
Recibe confirmación                            Acredita comisión
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

#### PASO 1 — Seleccionar la tasa de cambio *(paso obligatorio — todo lo demás permanece oculto hasta aquí)*
- Elige el **par de divisas** disponible en el selector (ej. `CLP → PEN`, `ARS → VES`, `BRL → PEN`)
- Al seleccionar, el formulario se activa automáticamente:
  - Las cabeceras muestran el país de origen y destino con su bandera
  - Se cargan los bancos, tipos de documento y cuentas del negocio correctos para ese corredor
  - Aparecen las secciones del formulario (receptor + remitente)
- **Antes de elegir una tasa, ninguna sección del formulario es visible** — esto es intencional para evitar confusión

#### PASO 2 — Cuentas del negocio para tu depósito
- Aparecen automáticamente después de seleccionar la tasa
- Solo se muestran las cuentas del país de origen del corredor elegido asignadas a tu vendedor
- Ejemplo: si elegiste `CLP → PEN`, verás cuentas en **Chile** (Banco de Chile, Banco Estado)
- Si elegiste `ARS → PEN`, verás cuentas en **Argentina** (Banco Nación, Banco Galicia)
- Debes transferir el monto exacto a una de estas cuentas antes de continuar

#### PASO 3 — Elegir método de pago
- Los métodos disponibles se cargan automáticamente según el **país de origen** del par seleccionado
- Métodos por país:
  | País | Métodos disponibles |
  |------|---------------------|
  | 🇵🇪 Perú | Transferencia Bancaria |
  | 🇦🇷 Argentina | Transferencia Bancaria · CVU / Alias |
  | 🇨🇱 Chile | Transferencia Bancaria |
  | 🇨🇴 Colombia | Transferencia Bancaria · Nequi · Daviplata |
  | 🇧🇷 Brasil | Transferencia Bancaria · PIX |

#### PASO 4 — Ingresar el número de operación
- Solo aparece si el método de pago es **Transferencia bancaria**
- Es el número de referencia que da tu banco al hacer la transferencia
- Campo opcional pero recomendado para facilitar la conciliación

#### PASO 5 — Datos del remitente (quien envía)
- **Tipo de documento**: se carga dinámicamente según el **país de origen** del par
  | País | Tipos de documento |
  |------|--------------------|
  | 🇵🇪 Perú | DNI · CE (Carné de Extranjería) · RUC |
  | 🇦🇷 Argentina | DNI · CUIT · CUIL |
  | 🇨🇱 Chile | RUT · RUN |
  | 🇨🇴 Colombia | CC · CE · NIT |
  | 🇧🇷 Brasil | CPF · CNPJ · RG |
- **Banco del remitente**: banco desde el que se realizará la transferencia (cargado por país)
- **Número de documento**: ingresa el número según el tipo seleccionado

#### PASO 6 — Datos del destinatario (quien recibe)
- **Tipo de documento**: cargado según el **país destino**
  - Si destino es Venezuela: Cédula (V) · Extranjero (E) · Jurídico (J) · Gubernamental (G)
  - Si destino es Perú: DNI · CE · RUC
- **Número de documento**
- **Banco destino**: banco receptor (cargado según el país destino)
- **Número de cuenta**: solo si el método es transferencia bancaria
- **Tipo de cuenta**: Ahorro / Corriente
- **Teléfono**: número de contacto del destinatario

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

**Países activos en el sistema:**

| País | Código | Rol | Bancos | Cuentas del negocio |
|------|--------|-----|--------|---------------------|
| 🇵🇪 Perú | PE | Origen | 7 | 3 |
| 🇻🇪 Venezuela | VE | Destino | 8 | 0 |
| 🇨🇱 Chile | CL | Origen | 7 | 2 |
| 🇨🇴 Colombia | CO | Origen | 7 | 2 |
| 🇦🇷 Argentina | AR | Origen | 7 | 2 |
| 🇧🇷 Brasil | BR | Origen | 7 | 2 |

> Venezuela es solo destino — los clientes reciben dinero ahí, pero no inician envíos desde Venezuela.

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

**Datos precargados (6 países activos):**

| País | Tipos de documento |
|------|--------------------|
| 🇵🇪 Perú | DNI · CE (Carné de Extranjería) · RUC |
| 🇻🇪 Venezuela | V (Cédula venezolana) · E (Extranjero) · J (Jurídico/Empresa) · G (Gubernamental) |
| 🇨🇱 Chile | RUT (Rol Único Tributario) · RUN (Rol Único Nacional) |
| 🇨🇴 Colombia | CC (Cédula de Ciudadanía) · CE (Cédula de Extranjería) · NIT |
| 🇦🇷 Argentina | DNI · CUIT (Tributaria) · CUIL (Laboral) |
| 🇧🇷 Brasil | CPF · CNPJ · RG |

#### Pestaña Métodos de Pago

Configura qué métodos de pago se ofrecen según el **país de origen** del remitente.

**Datos precargados:**

| País | Métodos |
|------|---------|
| 🇵🇪 Perú | Transferencia bancaria |
| 🇻🇪 Venezuela | (destino — no aplica métodos de pago) |
| 🇨🇱 Chile | Transferencia bancaria |
| 🇨🇴 Colombia | Transferencia bancaria · Nequi · Daviplata |
| 🇦🇷 Argentina | Transferencia bancaria · CVU / Alias |
| 🇧🇷 Brasil | Transferencia bancaria · PIX |

---

### 8.2 Tasas de cambio

**Ruta:** `/exchange_rates`

Solo una tasa puede estar **activa** por par de divisas. Cada tasa tiene:

| Campo | Descripción |
|-------|-------------|
| `ves_rate` | Tasa del par seleccionado (unidades de moneda destino por 1 unidad de moneda origen) |
| `usd_rate` | Tasa de referencia en USD |
| `eur_rate` | Tasa de referencia en EUR |

**Tasas activas actuales:**

| ID | Par | ves_rate | Descripción |
|----|-----|----------|-------------|
| 1 | PEN → VES | 173.71 | 1 sol = 173.71 bolívares |
| 4 | ARS → VES | 3.00 | 1 peso argentino = 3 bolívares |
| 5 | CLP → PEN | 0.0045 | 1 peso chileno = 0.0045 soles |
| 6 | CLP → VES | 0.55 | 1 peso chileno = 0.55 bolívares |
| 7 | COP → VES | 0.12 | 1 peso colombiano = 0.12 bolívares |
| 8 | USD → PEN | 3.72 | 1 dólar = 3.72 soles |
| 9 | COP → PEN | 0.00082 | 1 COP = 0.00082 soles |
| 10 | ARS → PEN | 0.0031 | 1 peso argentino = 0.0031 soles |
| 11 | BRL → PEN | 0.72 | 1 real = 0.72 soles |
| 12 | BRL → VES | 86.20 | 1 real = 86.20 bolívares |

> La tasa activa se muestra en el simulador público y en el formulario de transacción. Para actualizar una tasa: crear nueva → activar → la anterior queda inactiva automáticamente.

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

# Cargar TODA la base de datos desde cero (orden correcto)
./vendor/bin/sail artisan db:seed

# ─── Seeders individuales (si se necesita actualizar parcialmente) ───

# Países, bancos y cuentas base (Perú + Venezuela)
./vendor/bin/sail artisan db:seed --class=CountryBankSeeder

# Tipos de documento (Perú y Venezuela)
./vendor/bin/sail artisan db:seed --class=DocumentTypeSeeder

# Métodos de pago por país
./vendor/bin/sail artisan db:seed --class=PaymentMethodSeeder

# Chile + Colombia + pares CLP→PEN, COP→VES, etc.
./vendor/bin/sail artisan db:seed --class=MultiCorridorSeeder

# Argentina + Brasil + cuentas CL/CO/AR/BR + pares ARS→PEN, BRL→PEN, etc.
./vendor/bin/sail artisan db:seed --class=MoreCorridorsSeeder

# Datos de demostración (vendedores, clientes, transacciones de prueba)
./vendor/bin/sail artisan db:seed --class=DemoDataSeeder

# ─── Utilidades ──────────────────────────────────────────────────────

# Ejecutar todos los tests (38 tests, ~21s)
./vendor/bin/sail artisan test

# Ver logs en tiempo real
./vendor/bin/sail artisan pail

# Limpiar caché de vistas
./vendor/bin/sail artisan view:clear

# Limpiar caché de configuración
./vendor/bin/sail artisan config:clear

# Ver rutas activas
./vendor/bin/sail artisan route:list

# Consola interactiva (Tinker)
./vendor/bin/sail artisan tinker
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

---

## 13. Notas técnicas de la UX dinámica

### Comportamiento del formulario de transacción

El formulario `/transactions/create` usa **Alpine.js** para reaccionar en tiempo real sin recargar la página.

**Estado inicial (sin tasa seleccionada):**
- Solo el selector de tasa es visible
- Las secciones de receptor y remitente permanecen **ocultas**
- Las cuentas del negocio muestran el placeholder: "Selecciona una tasa de cambio para ver las cuentas disponibles"
- No se cargan bancos, tipos de documento ni métodos de pago

**Al seleccionar una tasa:**
1. El formulario lee los atributos `data-*` del `<option>` seleccionado (país, bandera, country_id)
2. Se actualizan las cabeceras dinámicamente: "🇨🇱 Tu transferencia desde Chile" / "🇵🇪 Receptor en Perú"
3. Se ejecutan 4 llamadas AJAX en paralelo:
   - `GET /transactions/sender-banks?country_id={fromCountryId}` → bancos del remitente
   - `GET /transactions/document-types?country_id={fromCountryId}` → tipos de doc del remitente
   - `GET /transactions/recipient-banks?country_id={toCountryId}` → bancos del receptor
   - `GET /transactions/seller-accounts?rate_id={selectedRateId}` → cuentas del negocio para ese corredor
4. Las secciones del formulario se muestran con transición suave

**Al cambiar la tasa:**
- Todos los datos se limpian y se vuelven a cargar para el nuevo corredor
- Los campos completados se resetean (previene errores de datos mixtos)

### Arquitectura de cuentas del negocio

```
Tasa seleccionada (rate_id)
      ↓
ExchangeRate → CurrencyPair → from_currency → country_id
      ↓
BusinessAccount filtrado por:
  • country_id = país de origen del corredor
  • sellers.id = assigned_seller_id del cliente autenticado
      ↓
Se muestran al cliente solo las cuentas aplicables
```

### Seeders y su orden

Los seeders deben ejecutarse en orden específico (ya configurado en `DatabaseSeeder.php`):

1. `RolesAndPermissionsSeeder` — roles y permisos Spatie
2. `CurrencySeeder` → `CurrencyPairSeeder` → `ExchangeRateSeeder` — monedas y tasas base
3. `DemoDataSeeder` — usuarios, vendedores de prueba
4. `CountryBankSeeder` — Perú + Venezuela
5. `DocumentTypeSeeder` + `PaymentMethodSeeder` — tipos de doc y métodos de pago PE/VE
6. `MultiCorridorSeeder` — Chile + Colombia + pares CLP→PEN, COP→VES
7. `MoreCorridorsSeeder` — Argentina + Brasil + cuentas CL/CO/AR/BR + pares ARS→PEN, BRL→PEN
8. `IncentiveSeeder` — incentivos de demostración

---

*Documento actualizado — rama `feat/REQ-12-multi-mejoras` — mayo 2026.*
*Refleja el estado tras la implementación de multi-corredor con 6 países activos, UX dinámica del formulario y suite de tests 38/38 PASS.*
