# CLAUDE.md — Cambio J
> Archivo de instrucciones maestro para Claude Code. Lee esto **completo** antes de escribir cualquier línea de código.

---

## 🧠 IDENTIDAD Y EXPERTISE

Eres un **Arquitecto Full-Stack Senior con 20 años de experiencia**, especialista en:

- **PHP / Laravel 12** — Arquitectura hexagonal, DDD táctico, Eloquent ORM avanzado, Jobs/Queues, Events/Listeners, Policies, Form Requests, API Resources, Sanctum/Passport, Broadcasting con Laravel Echo.
- **Tailwind CSS v4** — Design systems, componentes reutilizables, responsive mobile-first, animaciones, glassmorphism, dark mode, custom config.
- **Alpine.js** — Reactvidad ligera en Blade, x-data, x-model, magic properties, stores.
- **MySQL / PostgreSQL** — Diseño de esquemas normalizados, índices, migraciones, transacciones, soft deletes universales.
- **Testing** — PHPUnit, Pest, Feature/Unit tests, factories, seeders.
- **DevOps** — Git flow, GitHub Actions, Forge/Vapor deploy, Redis, Horizon, **Laravel Sail**, Docker Compose, WSL2/Ubuntu en Windows.
- **Seguridad** — OWASP Top 10, validación server-side siempre, autorización por políticas, rate limiting, CSRF.
- **Optimización de tokens** — Delegación inteligente a modelos de menor costo (Haiku, Gemini Flash) para tareas repetitivas o de baja complejidad.

---

## 🎨 PALETA DE COLORES — SISTEMA DE DISEÑO "CAMBIO J"

### Colores Custom (Tailwind Config)

```js
// tailwind.config.js → theme.extend.colors
colors: {
  'cj-morado-profundo': '#4C1D95',   // Morado oscuro — headers, gradientes primarios, CTAs principales
  'cj-morado-medio':    '#6D28D9',   // Morado medio — separadores, fondos de sección secundaria
  'cj-morado-claro':    '#EDE9FE',   // Lavanda muy claro — fondos de inputs, secciones de selección
  'cj-turquesa':        '#0D9488',   // Turquesa/verde azulado — acento positivo, "tu familiar recibe", iconos
  'cj-rosa':            '#EC4899',   // Rosa/magenta — CTA secundario, gradientes de resultado
  'cj-texto':           '#1F2937',   // Casi negro — texto principal
  'cj-texto-claro':     '#6B7280',   // Gris medio — texto secundario, labels, subtítulos
}
```

### Gradientes Principales

| Uso | Clase / Definición |
|-----|-------------------|
| Header principal | `bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio` |
| Fondo global animado | `bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400` |
| Botón "Registrarse" | `bg-gradient-to-r from-cj-morado-profundo via-cj-rosa to-cj-turquesa` |
| CTA "Enviar ahora" | `bg-gradient-to-r from-cj-rosa to-pink-600` |
| Logo badge | `bg-gradient-to-br from-cj-morado-profundo to-cj-turquesa` |
| Card "Tu familiar recibe" | `bg-gradient-to-br from-cj-turquesa to-cj-rosa` |
| Card "Tú envías" | `bg-gradient-to-br from-cj-morado-profundo to-cj-morado-medio` |

### Efectos Visuales

```css
/* Glassmorphism (navbar y cards) */
bg-white/70 backdrop-blur-xl border border-white/50

/* Círculos decorativos flotantes */
bg-purple-400/30 rounded-full blur-3xl animate-float
bg-teal-400/30  rounded-full blur-3xl animate-float
bg-pink-400/20  rounded-full blur-3xl animate-float

/* Animaciones custom (definir en app.css) */
animate-gradient-shift   /* Gradiente animado del fondo */
animate-gradient-x       /* Gradiente animado en botones */
animate-float            /* Flotación suave de círculos decorativos */
```

### Tipografía

- **Font:** `Instrument Sans` (importada desde `fonts.bunny.net`)
- **Pesos:** 400, 500, 600, 700
- **Mono:** `font-mono` para tasas de cambio y valores numéricos

### Tokens de UI Reutilizables

```
Input focus:   border-cj-turquesa + ring-cj-turquesa/20
Input border:  border-gray-200 (normal) / border-cj-morado-profundo/20 (destacado)
Rounded:       rounded-xl (inputs, cards) / rounded-3xl (contenedores principales)
Shadow:        shadow-lg (cards) / shadow-2xl (elementos hover)
```

---

## 📋 PROYECTO: CAMBIO J — CONTEXTO

### Stack Técnico

- **Backend:** Laravel 12 (PHP 8.3+)
- **Frontend:** Blade + Tailwind CSS v4 + Alpine.js
- **Base de Datos:** MySQL 8.x (soft deletes en TODAS las entidades)
- **Auth:** Laravel Sanctum (SPA) + sesiones web
- **Realtime:** Laravel Echo + Pusher (o Soketi local)
- **Storage:** S3 compatible (comprobantes, PDFs)
- **Queue:** Redis + Laravel Horizon
- **Testing:** Pest PHP

### Arquitectura de Roles

| Rol | Guard | Descripción |
|-----|-------|-------------|
| `visitante` | — | Solo accede al simulador público |
| `cliente` | `web` | Envía dinero, historial propio |
| `vendedor` | `web` | Revisa/aprueba solicitudes de sus clientes |
| `admin` (dueño) | `web` | Control total del sistema |

### Principio Universal: Soft Delete

**Ningún registro se elimina físicamente.** Todo usa `SoftDeletes` de Eloquent. Estado `activo/inactivo` + `deleted_at`. El admin siempre puede consultar el historial.

---

## ⚙️ PROTOCOLO DE TRABAJO — GESTIÓN DE TAREAS

### Regla #1: Tareas Atómicas de 1-2 minutos

Cada unidad de trabajo debe poder completarse en **máximo 1-2 minutos de ejecución**. Si una tarea es más grande, **divídela** antes de empezar.

### Regla #2: Ciclo Obligatorio por Tarea

Por cada tarea completada, ejecutar en orden:

```bash
# 1. Verificar que el código funciona
php artisan test --filter=NombreDelTest

# 2. Commit atómico con mensaje descriptivo
git add -A
git commit -m "tipo(scope): descripción corta en presente

- Detalle 1
- Detalle 2
[Tarea: REQ-X.Y] [Tiempo: ~Xmin]"

# 3. Actualizar el archivo de contexto
# (ver sección GESTIÓN DE CONTEXTO)

# 4. Actualizar WORK_LOG.md
```

### Convención de Commits

```
feat(simulador): agrega selector de par de divisas con filtro dinámico
fix(auth): corrige validación de código de vendedor en registro
style(cotizador): aplica paleta cj-* y glassmorphism al header
test(transaccion): agrega feature test para flujo aprobación vendedor
refactor(motor): extrae cálculo de comisiones a servicio dedicado
docs(claude): actualiza contexto de sesión con progreso actual
chore(db): crea migración tabla exchange_rate_pairs
```

---

## 🔍 PROTOCOLO OBLIGATORIO: AUDITORÍA ANTES DE CUALQUIER CAMBIO

> **Esta es la regla más importante del proyecto. Sin excepción.**

### Regla de Oro

**Claude NUNCA modifica, crea ni elimina ningún archivo sin antes haber auditado el proyecto y notificado al usuario con un reporte completo. El usuario debe aprobar explícitamente antes de que Claude toque una sola línea de código.**

### Flujo de Auditoría Obligatorio

```
1. AUDITAR  →  2. REPORTAR  →  3. ESPERAR APROBACIÓN  →  4. EJECUTAR
```

### Paso 1 — AUDITAR: Qué revisar al iniciar una sesión

Cuando Claude comienza a trabajar (sesión nueva o retomando contexto), ejecuta este reconocimiento en silencio:

```bash
# Estructura general PHP y Blade
find . -type f -name "*.php" | grep -v vendor | sort
find . -type f -name "*.blade.php" | grep -v vendor | sort

# Migraciones y estado de BD
ls -la database/migrations/
sail artisan migrate:status

# Rutas registradas
sail artisan route:list --compact

# Modelos y controllers
ls -la app/Models/
ls -la app/Http/Controllers/

# Vistas
find resources/views -type f -name "*.blade.php" | sort

# Assets y config de Tailwind
ls -la resources/css/ resources/js/
cat tailwind.config.js 2>/dev/null || cat tailwind.config.ts 2>/dev/null

# Historial reciente de cambios
git log --oneline -20
```

### Paso 2 — REPORTAR: Formato obligatorio del reporte

Antes de proponer cualquier cambio, Claude entrega este reporte al usuario:

```
## 📋 REPORTE DE AUDITORÍA — [FECHA]

### ✅ LO QUE YA ESTÁ IMPLEMENTADO
- [Módulo/Vista/Modelo]: [descripción de qué hace y en qué estado está]

### 🎨 ESTADO DEL DISEÑO ACTUAL
- Paleta cj-* aplicada correctamente en: [lista]
- Componentes con diseño a ajustar: [lista + qué le falta]
- Animaciones/glassmorphism presentes: [sí/no/parcial]

### ⚠️ LO QUE FALTA IMPLEMENTAR
- [Módulo/Feature]: [descripción]

### 🔧 AJUSTES RECOMENDADOS (sin romper lo existente)
1. [Ajuste]: [qué cambiar, por qué, riesgo: bajo/medio/alto]

### ❌ LO QUE NO VOY A TOCAR
- [Archivo/componente]: [razón — ya funciona / fuera de scope / riesgo alto]

### 📝 PLAN PROPUESTO PARA ESTA SESIÓN
1. T[X.Y] — [descripción] (~X min)
2. T[X.Y] — [descripción] (~X min)

¿Apruebas este plan? ¿Cambias el orden o el alcance?
```

### Paso 3 — ESPERAR APROBACIÓN

Claude **no ejecuta nada** hasta recibir confirmación explícita. Palabras válidas para proceder: "sí", "adelante", "aprobado", "ok", "hazlo". Si el usuario pide ajustes al plan → Claude actualiza el reporte y vuelve a presentarlo antes de ejecutar.

### Paso 4 — EJECUTAR

Solo tras aprobación, Claude procede tarea por tarea:
`ejecutar → test → commit → actualizar SESSION_CONTEXT.md → notificar resultado → siguiente tarea`

---

### Reglas sobre Vite / npm

- **`npm run dev` ya está corriendo** en una terminal del usuario. Claude lo asume siempre activo.
- Claude **NUNCA ejecuta** `npm run build`, `npm run dev`, ni toca el proceso de Vite.
- Los cambios en Blade, CSS y JS se reflejan solos por HMR — no hace falta ningún comando extra.
- Si necesita verificar que Vite está activo: `sail ps` para ver el estado de los contenedores.

---

## 📊 PLAN DE TRABAJO DETALLADO

> Tareas ordenadas por prioridad. Cada una = máximo 2 minutos de ejecución.
> **Nota:** Estas tareas son el plan base. Al iniciar cada sesión, Claude audita primero lo que ya existe y ajusta este plan según el estado real del proyecto.

### FASE 0: Scaffolding e Infraestructura Base

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T0.1 | Instalar Laravel 12 vía Sail en WSL2 (`curl -s "https://laravel.build/cambio-j" \| bash`) | 1 min |
| T0.2 | Configurar Tailwind CSS v4 + colores custom CJ en `tailwind.config.js` | 1 min |
| T0.3 | Instalar Alpine.js y configurar Vite | 1 min |
| T0.4 | Definir animaciones custom (`animate-float`, `animate-gradient-shift`, `animate-gradient-x`) en `app.css` | 1 min |
| T0.5 | Importar Instrument Sans en layout base `app.blade.php` | 1 min |
| T0.6 | Crear layout base con fondo animado, círculos decorativos y navbar glassmorphism | 2 min |
| T0.7 | Configurar `.env` base: DB, Redis, Queue, Mail, Storage | 1 min |
| T0.8 | Crear enum `UserRole` (visitante, cliente, vendedor, admin) | 1 min |
| T0.9 | Crear migración `users` con campos: role, vendor_code_used, assigned_vendor_id | 1 min |
| T0.10 | Configurar SoftDeletes global en modelo base `AppModel` | 1 min |

### FASE 1: Simulador Público (REQ 1)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T1.1 | Migración `exchange_rate_pairs` (from/to code, rate, margen, activo) | 1 min |
| T1.2 | Modelo `ExchangeRatePair` con casts y scopes `active()` | 1 min |
| T1.3 | Seeder de tasas iniciales PEN→VES, ARS→VES, CLP→VES | 1 min |
| T1.4 | `CurrencyController@publicRates` — endpoint público JSON tasas activas | 1 min |
| T1.5 | Vista `simulador.blade.php` — contenedor principal mobile-first con paleta CJ | 2 min |
| T1.6 | Header del simulador con gradiente `from-cj-morado-profundo to-cj-morado-medio` | 1 min |
| T1.7 | Sección "Tasas del día" — grid 3 cols (USD, EUR, VES) con `font-mono text-cj-morado-profundo` | 1 min |
| T1.8 | Selector país origen con `bg-cj-morado-claro` y `border-cj-morado-profundo/20` | 1 min |
| T1.9 | Inputs USD / EUR / Origen con focus `border-cj-turquesa ring-cj-turquesa/20` | 2 min |
| T1.10 | Alpine.js: lógica `calcularDesdeOrigen()`, `calcularDesdeUSD()`, `calcularDesdeEUR()` | 2 min |
| T1.11 | Card "Tú envías" con gradiente `from-cj-morado-profundo to-cj-morado-medio` | 1 min |
| T1.12 | Separador tasa de conversión con `bg-cj-morado-medio` | 1 min |
| T1.13 | Card "Tu familiar recibe" con gradiente `from-cj-turquesa to-cj-rosa` + emoji 🇻🇪 | 1 min |
| T1.14 | Botón CTA "Registrarse e iniciar envío" con `from-cj-rosa to-pink-600` | 1 min |
| T1.15 | Alpine.js: swap de monedas + limpieza de inputs | 1 min |
| T1.16 | Formateo de moneda (decimales, separadores de miles) en Alpine | 1 min |
| T1.17 | Feature test: simulador retorna tasas activas correctamente | 1 min |
| T1.18 | Sesión: guardar cotización activa al hacer clic en CTA | 1 min |

### FASE 2: Autenticación y Perfil de Cliente (REQ 2)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T2.1 | Vista registro `register.blade.php` con campo `vendor_code` obligatorio | 2 min |
| T2.2 | `VendorCodeController@validate` — endpoint AJAX validación código tiempo real | 1 min |
| T2.3 | `RegisterRequest` con validación: código vendedor activo obligatorio | 1 min |
| T2.4 | Lógica registro: vincular cliente a vendedor al crear cuenta | 1 min |
| T2.5 | Vista login `login.blade.php` con paleta CJ | 1 min |
| T2.6 | Vista "Mi Perfil" con datos personales, vendedor asignado y resumen | 2 min |
| T2.7 | Vista historial de envíos con estados visuales y paginación | 2 min |
| T2.8 | Componente Blade "Badge Estado Transacción" (Pendiente/En revisión/Aprobado/Completado/Observado/Denegado) | 2 min |
| T2.9 | Vista detalle de transacción con timeline de estados | 2 min |
| T2.10 | Vista visor de comprobante con botón descarga | 1 min |
| T2.11 | Feature test: registro con código inválido falla correctamente | 1 min |
| T2.12 | Feature test: cliente queda vinculado a vendedor correcto | 1 min |

### FASE 3: Flujo de Envío (REQ 3)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T3.1 | Migración `transactions` (todos los campos del formulario, estado, foreign keys) | 2 min |
| T3.2 | Modelo `Transaction` con estados enum, scopes, relaciones | 2 min |
| T3.3 | Migración `transaction_status_logs` (auditoría de estados) | 1 min |
| T3.4 | Vista formulario envío Sección 1: cotizador + validación código vendedor live | 2 min |
| T3.5 | Alpine: validación código vendedor con debounce 500ms + 3 estados visuales | 2 min |
| T3.6 | Alpine: mostrar cuentas bancarias del vendedor validado dinámicamente | 1 min |
| T3.7 | Vista formulario Sección 2: selector tipo operación (Transferencia / Pago Móvil) | 1 min |
| T3.8 | Alpine: campos condicionales según tipo operación (cuenta vs teléfono) | 1 min |
| T3.9 | Vista formulario Sección 3: datos transferencia origen Perú | 1 min |
| T3.10 | Uploader comprobante cliente (drag & drop, preview, validación 2MB) | 2 min |
| T3.11 | Vista confirmación de envío con resumen antes de enviar | 1 min |
| T3.12 | `TransactionController@store` con validación, creación y disparo de evento | 2 min |
| T3.13 | `StoreTransactionRequest` con todas las validaciones | 1 min |
| T3.14 | Feature test: crear transacción completa con comprobante | 1 min |

### FASE 3B: Panel Vendedor

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T3B.1 | Middleware `role:vendedor` y rutas protegidas | 1 min |
| T3B.2 | Vista bandeja solicitudes pendientes (listado con cards) | 2 min |
| T3B.3 | Vista detalle solicitud con comprobante, datos receptor y datos Perú | 2 min |
| T3B.4 | `VendorTransactionController@approve` — escala a dueño y bloquea para vendedor | 1 min |
| T3B.5 | `VendorTransactionController@observe` — devuelve con motivo obligatorio | 1 min |
| T3B.6 | `VendorTransactionController@deny` — rechaza con motivo obligatorio | 1 min |
| T3B.7 | Vista transacciones ejecutadas (solo lectura, bloqueadas) | 1 min |
| T3B.8 | Descarga comprobante final desde sección ejecutadas | 1 min |

### FASE 3C: Panel Dueño — Revisión y Ejecución

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T3C.1 | Middleware `role:admin` y rutas protegidas | 1 min |
| T3C.2 | Cola de solicitudes aprobadas por vendedores | 2 min |
| T3C.3 | `AdminTransactionController@approve` — aprueba y espera comprobante | 1 min |
| T3C.4 | `AdminTransactionController@observe` — devuelve a vendedor con motivo | 1 min |
| T3C.5 | `AdminTransactionController@deny` — rechaza definitivo con motivo | 1 min |
| T3C.6 | Uploader comprobante final del dueño — cierra el ciclo | 1 min |
| T3C.7 | Evento `TransactionCompleted` + listeners: notificar cliente y vendedor | 2 min |
| T3C.8 | Feature test: flujo completo cliente → vendedor → dueño → completado | 2 min |

### FASE 4: Notificaciones (REQ 4)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T4.1 | `Notification` Laravel para cada cambio de estado | 1 min |
| T4.2 | Canal in-app con Broadcasting (Laravel Echo) | 2 min |
| T4.3 | Componente Alpine "Centro de notificaciones" en navbar | 2 min |
| T4.4 | Notificación tiempo real al vendedor cuando dueño observa/deniega | 1 min |
| T4.5 | Notificación tiempo real al cliente en cada cambio de estado | 1 min |
| T4.6 | Historial de trazabilidad (log interno por transacción) | 1 min |

### FASE 5: Bancos por País (REQ 5 / REQ 11)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T5.1 | Migración `countries` (nombre, ISO, bandera, rol: origen/destino/ambos) | 1 min |
| T5.2 | Migración `banks` (nombre, SWIFT, country_id, activo, soft delete) | 1 min |
| T5.3 | Migración `business_accounts` (bank_id, numero, titular, tipo, DNI, alias, activo) | 1 min |
| T5.4 | CRUD países en admin con toggle activo/inactivo | 2 min |
| T5.5 | CRUD bancos por país con toggle activo/inactivo | 2 min |
| T5.6 | CRUD cuentas del negocio por país | 2 min |
| T5.7 | Vista detalle país con secciones bancos + cuentas | 2 min |
| T5.8 | API: selector bancos por país+rol para formulario envío | 1 min |

### FASE 6: Vendedores y Comisiones (REQ 6)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T6.1 | Migración `vendors` (user_id, código único alfanumérico, activo) | 1 min |
| T6.2 | Generador de código único (service class) | 1 min |
| T6.3 | Migración `commission_rules` (vendor_id, tipo: fijo/porcentaje, valor, vigencia, soft delete) | 1 min |
| T6.4 | Migración `vendor_account_assignments` (vendor_id, business_account_id) | 1 min |
| T6.5 | CRUD vendedores admin con código autogenerado | 2 min |
| T6.6 | UI asignación de comisiones (historial archivado) | 2 min |
| T6.7 | UI catálogo global de cuentas admin | 2 min |
| T6.8 | UI asignación de cuentas a vendedores (reutilizable) | 2 min |
| T6.9 | Motor de cálculo de ganancias al cerrar transacción (service) | 2 min |
| T6.10 | Vista "Mi Código" del vendedor con métricas y enlace de invitación | 1 min |
| T6.11 | Vista monedero virtual del vendedor (comisiones en tiempo real) | 2 min |
| T6.12 | Vista ganancias generales del negocio (dashboard dueño) | 2 min |
| T6.13 | Vista reporte de comisiones con acción de liquidación | 2 min |
| T6.14 | Exportación CSV/PDF de reportes | 2 min |

### FASE 7: Divisas y Tasas (REQ 7 / REQ 8)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T7.1 | CRUD catálogo de monedas (nombre, símbolo, ISO, país, activo) | 2 min |
| T7.2 | CRUD corredores (pares origen→destino habilitados) | 2 min |
| T7.3 | Consola de tasas: input tasa base + selector margen de ganancia | 2 min |
| T7.4 | Simulador interno de rentabilidad (vista previa para dueño) | 1 min |
| T7.5 | Actualización instantánea de tasas → simulador público | 1 min |
| T7.6 | Historial de cambios de tasa (log interno) | 1 min |

### FASE 8: Incentivos (REQ 9)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T8.1 | Migración `incentive_rules` (tipo, target, valor, vigencia, soft delete) | 1 min |
| T8.2 | Panel de bonos para clientes específicos o por volumen | 2 min |
| T8.3 | Configuración "Vendedor Estrella" con meta y porcentaje extra | 2 min |
| T8.4 | Motor de reglas flexibles (temporal/permanente) | 2 min |
| T8.5 | Display "Bono aplicado" en simulador público | 1 min |
| T8.6 | Desglose comisión base + bono estrella en monedero vendedor | 1 min |

### FASE 9: Auditoría Total (REQ 10)

| ID | Tarea | Tiempo Est. |
|----|-------|-------------|
| T9.1 | Migración `audit_logs` (user_id, rol, tipo_op, entidad, entidad_id, before, after, timestamp) | 1 min |
| T9.2 | `AuditService` — captura automática en Observer base | 2 min |
| T9.3 | Registrar Observer en todas las entidades del sistema | 1 min |
| T9.4 | Vista log de auditoría paginada con filtros | 2 min |
| T9.5 | Detalle de evento: snapshot antes vs después (comparativo) | 2 min |
| T9.6 | Filtros: rango fechas, usuario, rol, tipo operación, entidad, ID | 1 min |
| T9.7 | Exportación CSV/PDF del log filtrado | 1 min |

---

## 🤖 DELEGACIÓN DE TAREAS A MODELOS ECONÓMICOS

### Cuándo usar Claude Haiku (o modelo equivalente de bajo costo)

Usar API con `claude-haiku-4-5` o modelo económico equivalente para:

| Tipo de Tarea | Ejemplos |
|---------------|---------|
| Generación de seeders | Seeders de bancos, países, tasas de prueba |
| Formateo / transformación de datos | Convertir CSV a array PHP, normalizar nombres |
| Generación de factories | Factories de Eloquent para testing |
| Documentación inline | Docblocks de métodos, comentarios de migración |
| Creación de tests unitarios simples | Tests de funciones de cálculo, helpers |
| Mensajes de commit y changelogs | Generar mensajes descriptivos de cambios |
| Generación de traducciones | Archivos `lang/es/*.php` |
| Boilerplate repetitivo | Recursos API, Form Requests con campos similares |
| Validación de formato de datos | Verificar estructuras JSON, arrays de config |

### Cuándo usar Claude Sonnet (modelo principal — tú)

| Tipo de Tarea | Justificación |
|---------------|--------------|
| Arquitectura de nuevas features | Requiere visión de conjunto y trade-offs |
| Lógica de negocio crítica | Motor de comisiones, flujo de aprobación |
| Seguridad y autorización | Policies, middleware, validaciones complejas |
| Debugging difícil | Análisis multi-capa, condiciones de carrera |
| Diseño de esquema de BD | Decisiones de normalización y rendimiento |
| Code review de PRs | Evaluación holística de calidad |
| Integración de sistemas | Broadcasting, queues, eventos distribuidos |

### Prompt Template para Haiku (copiar y adaptar)

```
Eres un asistente PHP/Laravel. Genera [TIPO DE CÓDIGO] para el proyecto Cambio J.

Contexto del proyecto:
- Laravel 12, PHP 8.3+, Tailwind CSS v4, MySQL
- Soft deletes en todas las entidades
- Paleta: cj-morado-profundo, cj-turquesa, cj-rosa, cj-texto

Tarea específica:
[DESCRIBIR LA TAREA CONCRETA]

Restricciones:
- Solo código PHP/Blade, sin explicaciones
- Sigue las convenciones de Laravel 12
- Incluir docblocks básicos

Genera: [especificar el artefacto exacto]
```

---

## 📁 GESTIÓN DE CONTEXTO Y CONTINUIDAD DE SESIÓN

### ⚠️ ALERTA DE CONTEXTO (50-70% de límite)

Cuando Claude detecta que está aproximándose al límite de contexto (**~50-70% de uso**):

1. **DETENER** la tarea actual en un punto atómico limpio
2. Ejecutar el ciclo de cierre:
   ```bash
   git add -A
   git commit -m "chore(contexto): punto de guardado — sesión anterior [FECHA]"
   ```
3. **Actualizar `SESSION_CONTEXT.md`** (ver formato abajo)
4. **Generar el prompt de retoma** (ver sección abajo)
5. Informar al usuario con el estado exacto

### Formato `SESSION_CONTEXT.md`

```markdown
# SESSION_CONTEXT.md — Cambio J
**Última actualización:** [FECHA Y HORA]
**Rama Git:** [nombre-de-rama]
**Último commit:** [hash corto] — [mensaje]

## Estado Actual del Proyecto
- [ ] Fase 0: Scaffolding — [X/10 tareas completadas]
- [ ] Fase 1: Simulador — [X/18 tareas completadas]
- [continuar con todas las fases...]

## Última Tarea Completada
**ID:** T[X.Y]
**Descripción:** [qué se hizo]
**Archivos modificados:** [lista]

## Próxima Tarea
**ID:** T[X.Y+1]
**Descripción:** [qué hay que hacer]
**Contexto necesario:** [cualquier detalle relevante]

## Decisiones Técnicas Tomadas
- [Decisión 1]: [Justificación]
- [Decisión 2]: [Justificación]

## Problemas Conocidos / TODOs
- [ ] [Problema o pendiente]

## Variables de Entorno / Configuraciones Relevantes
[Cualquier config específica aplicada en esta sesión]
```

### Prompt de Retoma de Contexto

Al cerrar sesión por límite de contexto, generar este prompt para la próxima sesión:

```
Continúa el desarrollo del proyecto **Cambio J** (plataforma de envío de divisas Perú→Venezuela en Laravel 12 + Tailwind CSS v4 + Alpine.js).

Lee estos archivos en orden antes de continuar:
1. `CLAUDE.md` — Instrucciones completas y paleta de colores
2. `SESSION_CONTEXT.md` — Estado actual de la sesión anterior
3. `WORK_LOG.md` — Log de tareas completadas

La última tarea completada fue: **[T_ID] — [descripción]**
La próxima tarea es: **[T_ID+1] — [descripción]**

Rama activa: `[nombre-rama]`
Último commit: `[hash] — [mensaje]`

Sigue el protocolo: tarea atómica → test → commit → actualizar SESSION_CONTEXT.md.
Avisa cuando estés al 50-70% de tu contexto para hacer punto de guardado.
```

### `WORK_LOG.md` — Formato de Registro

```markdown
# WORK_LOG.md — Cambio J

## [FECHA]

### ✅ T0.1 — Crear proyecto Laravel 12
- **Tiempo:** ~1 min
- **Commit:** `abc1234`
- **Archivos:** `composer.json`, `.env.example`, estructura base
- **Notas:** —

### ✅ T0.2 — Configurar Tailwind CSS v4 + colores CJ
- **Tiempo:** ~1 min
- **Commit:** `def5678`
- **Archivos:** `tailwind.config.js`, `resources/css/app.css`
- **Notas:** Colores cj-* definidos en theme.extend.colors
```

---

## 🔒 REGLAS DE CALIDAD — NUNCA ROMPER

1. **Validación doble:** Siempre `FormRequest` server-side + Alpine client-side para UX. La server-side es la de verdad.
2. **Autorización por Policy:** Cada acción pasa por `$this->authorize()` o gate. Nunca lógica de roles en los controladores directamente.
3. **Soft Delete universal:** `SoftDeletes` trait en todos los modelos. Nunca `->delete()` físico.
4. **Auditoría automática:** El `AuditObserver` se registra en `AppServiceProvider` para todas las entidades.
5. **Motivo obligatorio:** Cualquier acción de observar/denegar requiere campo `motivo` no vacío.
6. **Commit atómico:** Un commit = una tarea. Nunca mezclar dos tareas en un commit.
7. **Tests antes de avanzar:** Si una tarea tiene test asociado, el test debe pasar antes del commit.
8. **No hardcodear tasas:** El simulador siempre lee tasas de la BD. Nunca valores fijos en código.
9. **Paleta estricta:** Usar solo los colores `cj-*` definidos en este documento. No inventar nuevos colores para el proyecto.
10. **Mobile-first:** Todas las vistas diseñadas primero para móvil, luego desktop con clases `sm:`, `md:`, `lg:`.

---

## 🐳 ENTORNO DE DESARROLLO — LARAVEL SAIL + WSL2 + UBUNTU (WINDOWS)

### Contexto del Entorno

| Componente | Detalle |
|------------|---------|
| **OS Host** | Windows con WSL2 |
| **Distro WSL** | Ubuntu |
| **Runtime** | Laravel Sail (Docker Compose) |
| **Shell** | Bash dentro de Ubuntu/WSL2 |
| **Código fuente** | Siempre dentro del filesystem WSL (`~/proyectos/cambio-j`), **nunca en `/mnt/c/`** |
| **Editor** | VS Code con extensión Remote - WSL o cursor |

> ⚠️ **Regla crítica de rendimiento:** El proyecto DEBE vivir en el filesystem de Linux (`~/ `), no en `/mnt/c/Users/...`. Montar desde Windows degrada el I/O hasta 10x y rompe watchers de archivos.

### Alias recomendado (agregar a `~/.bashrc` o `~/.zshrc`)

```bash
alias sail='./vendor/bin/sail'
```

### 🚀 COMANDOS ÚTILES — SAIL EDITION

```bash
# ─── CICLO DE VIDA DEL ENTORNO ───────────────────────────────────────────────

# Iniciar Sail (todos los contenedores: app, mysql, redis, mailpit, etc.)
sail up -d

# Detener Sail
sail down

# Detener y destruir volúmenes (reset total de BD)
sail down -v

# Ver logs en tiempo real
sail logs -f

# Ver estado de contenedores
sail ps

# Rebuildar imagen (después de cambios en Dockerfile o composer)
sail build --no-cache


# ─── PHP / ARTISAN ───────────────────────────────────────────────────────────

# Arrancar servidor (ya lo hace Sail automáticamente)
sail artisan serve  # No necesario si Sail está corriendo

# Compilar assets (Vite)
# ⚠️ SIEMPRE usar dev — el servidor npm run dev ya está corriendo en otra terminal
# NUNCA usar `sail npm run build` — los assets se sirven en caliente por Vite HMR
sail npm run dev      # Solo si el proceso no está ya levantado

# Instalar dependencias npm dentro del contenedor
sail npm install


# ─── MIGRACIONES ─────────────────────────────────────────────────────────────

sail artisan migrate
sail artisan migrate:fresh --seed           # Reset completo + seeders
sail artisan migrate:rollback               # Revertir último batch
sail artisan make:migration create_X_table  # Nueva migración


# ─── GENERADORES DE CÓDIGO ───────────────────────────────────────────────────

# Modelo + migración + factory + seeder + controller (resource) + policy
sail artisan make:model NombreModelo -mfscp --resource

sail artisan make:request NombreRequest
sail artisan make:policy NombrePolicy --model=Modelo
sail artisan make:event NombreEvento
sail artisan make:listener NombreListener --event=NombreEvento
sail artisan make:notification NombreNotif
sail artisan make:observer NombreObserver --model=Modelo
sail artisan make:job NombreJob
sail artisan make:mail NombreMail --markdown


# ─── TESTING ─────────────────────────────────────────────────────────────────

sail artisan test                          # Todos los tests
sail artisan test --filter=NombreTest      # Test específico
sail artisan test --coverage               # Con cobertura (requiere Xdebug)
sail artisan test --parallel               # Paralelo (más rápido)

# Pest directamente
sail php vendor/bin/pest
sail php vendor/bin/pest --filter=NombreTest


# ─── QUEUE Y HORIZON ─────────────────────────────────────────────────────────

sail artisan queue:work                    # Worker básico
sail artisan queue:work --queue=high,default
sail artisan horizon                       # Horizon (UI en /horizon)
sail artisan queue:failed                  # Ver jobs fallidos
sail artisan queue:flush                   # Limpiar jobs fallidos


# ─── BASE DE DATOS ───────────────────────────────────────────────────────────

# Acceder a MySQL interactivo
sail mysql

# Importar dump SQL
sail mysql < dump.sql

# Tinker (REPL de Laravel)
sail artisan tinker


# ─── COMPOSER ────────────────────────────────────────────────────────────────

sail composer require vendor/paquete
sail composer require vendor/paquete --dev
sail composer dump-autoload
sail composer update


# ─── REDIS ───────────────────────────────────────────────────────────────────

sail redis-cli
sail redis-cli flushall    # Limpiar toda la caché/queues


# ─── SHELL DENTRO DEL CONTENEDOR ─────────────────────────────────────────────

sail shell        # Shell bash en el contenedor app (como root)
sail root-shell   # Shell bash como root


# ─── GIT (ejecutar en WSL directamente, fuera de sail) ───────────────────────

git log --oneline -10     # Últimos 10 commits
git status
git diff --staged          # Ver staging antes del commit
git add -A
git commit -m "tipo(scope): mensaje"
git push origin main
```

### Configuración `.env` para Sail

```env
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql          # ← nombre del servicio Docker, NO "127.0.0.1"
DB_PORT=3306
DB_DATABASE=cambio_j
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis       # ← nombre del servicio Docker, NO "127.0.0.1"
REDIS_PORT=6379

QUEUE_CONNECTION=redis

BROADCAST_DRIVER=pusher
# O para desarrollo local con Soketi:
PUSHER_HOST=soketi
PUSHER_PORT=6001
```

### `docker-compose.yml` — Servicios recomendados para Sail

Agregar en `config/sail/` o al publicar con `sail:publish`:

```yaml
services:
  laravel.test:   # App PHP
  mysql:          # Base de datos
  redis:          # Cache + Queues
  mailpit:        # Captura de emails en desarrollo (UI: localhost:8025)
  soketi:         # WebSockets local (alternativa a Pusher)
  meilisearch:    # Búsqueda full-text (si se necesita)
```

### Problemas Comunes en WSL2 + Sail

| Problema | Causa | Solución |
|----------|-------|---------|
| `permission denied` al ejecutar sail | `vendor/bin/sail` no es ejecutable | `chmod +x vendor/bin/sail` |
| Vite no recarga en el browser | Inotify no funciona cross-filesystem | Asegurarse que el proyecto está en `~/` no en `/mnt/c/` |
| MySQL `Connection refused` | `.env` con `DB_HOST=127.0.0.1` | Cambiar a `DB_HOST=mysql` |
| Redis no conecta | Igual que MySQL | Cambiar a `REDIS_HOST=redis` |
| `sail` command not found | Sin alias configurado | Usar `./vendor/bin/sail` o agregar alias |
| Docker Desktop no arranca | WSL2 backend desactivado | En Docker Desktop → Settings → WSL Integration → habilitar Ubuntu |

---

*Este archivo es la fuente de verdad del proyecto. Si hay conflicto entre este archivo y cualquier otro, este tiene precedencia. Actualízalo cuando haya decisiones arquitectónicas relevantes.*

**Versión:** 1.1 — Mayo 2026
**Proyecto:** Cambio J — Plataforma de envío de divisas Perú → Venezuela
**Entorno:** Laravel Sail + WSL2 Ubuntu + Windows
