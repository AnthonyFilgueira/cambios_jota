# CLAUDE.md — Proyecto Cambio J

**Plataforma de gestión de divisas PEN → VES**

---

## Stack Técnico (REAL - Implementado)

```yaml
Backend:   Laravel 12
Frontend:  Alpine.js 3.x (NO Vue 3, NO Inertia)
Vistas:    Blade templates
Estilos:   Tailwind CSS con variables custom
Base de datos: MySQL
Build:     Vite
Package manager: npm
Comandos:  sail (Docker)
```

---

## Paleta de Colores Cambio J

```css
/* Morados */
--cj-morado-profundo: #5B21B6;  /* purple-600 en Tailwind */
--cj-morado-medio:    #7C3AED;  /* purple-500 */
--cj-morado-claro:    #DDD6FE;  /* purple-200 */

/* Acentos */
--cj-turquesa:        #14B8A6;  /* teal-500 */
--cj-rosa:            #EC4899;  /* pink-500 */

/* Neutros */
--cj-fondo:           #F3F4F6;  /* gray-100 */
--cj-texto:           #374151;  /* gray-700 */
--cj-texto-claro:     #6B7280;  /* gray-500 */
```

Configuradas en: `tailwind.config.js`

### ⚠️ DISEÑO OBLIGATORIO en TODAS las vistas:

**Fondo animado con gradiente:**
```html
<div class="fixed inset-0 -z-20 bg-gradient-to-br from-purple-600 via-pink-500 to-teal-400 animate-gradient-shift"></div>
<div class="fixed inset-0 -z-10 bg-white/40 backdrop-blur-sm"></div>
```

**Cards con glassmorphism:**
```html
<div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/50 p-8">
```

**Botones primarios:**
```html
<button class="bg-gradient-to-r from-cj-morado-profundo to-cj-morado-medio text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all">
```

**Inputs con focus mejorado:**
```html
<input class="w-full p-3 border-2 border-gray-200 rounded-xl focus:border-cj-turquesa focus:ring-2 focus:ring-cj-turquesa/20 transition-all">
```

**Referencia:** Ver `resources/views/welcome.blade.php` para diseño completo

---

## Patrón de Desarrollo Establecido

### Estructura de archivos por módulo:

```
app/
├── Models/
│   └── NombreModelo.php
├── Http/Controllers/
│   └── NombreController.php
└── ...

database/migrations/
└── xxxx_create_tabla.php

resources/views/
├── nombre-modulo/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── ...

routes/
└── web.php
```

### Patrón Alpine.js en Blade:

```html
<div x-data="nombreComponente()">
    <!-- HTML con directivas Alpine: x-model, x-text, @click, etc. -->
</div>

<script>
function nombreComponente() {
    return {
        // Estado reactivo
        dato: '',
        
        // Métodos
        metodo() {
            // lógica
        }
    }
}
</script>
```

**Referencia:** `resources/views/welcome.blade.php` (REQ 1 - Simulador)

---

## Sistema de Gestión de Requerimientos

### Estructura de documentación:

```
docs/
├── work_plans/
│   └── plan_trabajo.md              # Plan maestro con 8 REQ
├── requirements/
│   ├── 00-sistema-gestion-pm/
│   │   └── requirement.md
│   ├── 01-analisis-estado-actual/
│   │   └── requirement.md
│   ├── 02-simulador-envio/         # REQ 1 del plan
│   │   ├── requirement.md
│   │   ├── 1.1-maquetado/
│   │   │   ├── task.md
│   │   │   └── implementation.md
│   │   └── ...
│   └── ...
└── checkpoints/
    └── checkpoint-YYYY-MM-DD-HHmm.md
```

### Por cada REQ:

1. **Crear carpeta:** `XX-nombre-descriptivo/`
2. **Crear `requirement.md`:** Descripción general del requerimiento
3. **Por cada tarea:**
   - Crear carpeta: `X.Y-nombre-tarea/`
   - Crear `task.md`: Descripción corta de la tarea
   - Ejecutar tarea
   - Crear `implementation.md`: Detalle completo (código, comandos, decisiones)
   - Commit: "REQ X.Y: Descripción de la tarea"

---

## Gestión de Cambios

### Caso 1: Error en implementación
- **Acción:** Editar `implementation.md` de la tarea
- **Commit:** "REQ X.Y: Corregir implementación - [descripción]"

### Caso 2: Cambio de requerimiento
- **Acción:** 
  1. Documentar cambio en `requirement.md` del REQ
  2. Crear nueva tarea: `X.YY-ajuste-descripcion/`
  3. Crear `task.md` + `implementation.md`
- **Commit:** "REQ X.YY: Ajuste por cambio de requerimiento - [descripción]"

**Beneficio:** Trazabilidad total de qué era original vs. cambios del cliente

---

## ⚠️ REGLA CRÍTICA: Checkpoint de Contexto

### Cuando el uso de tokens llegue al 50-60% (100K-120K tokens):

1. **PAUSAR** trabajo actual
2. **CREAR checkpoint:**
   ```bash
   # Crear archivo
   touch docs/checkpoints/checkpoint-$(date +%Y-%m-%d-%H%M).md
   ```

3. **Documentar en el checkpoint:**
   ```markdown
   # Checkpoint - [Fecha y Hora]
   
   ## Uso de contexto
   - Tokens usados: XXX / 200,000 (XX%)
   
   ## Estado del proyecto
   - REQ completados: [lista con commits]
   - REQ en progreso: XX - Tarea X.Y
   - Última tarea completada: [descripción]
   - Último commit: [hash] - [mensaje]
   
   ## Decisiones técnicas tomadas
   - [Lista de decisiones importantes de esta sesión]
   
   ## Archivos modificados en esta sesión
   - [Lista de archivos]
   
   ## Próximo paso
   [Descripción específica y detallada de qué hacer al retomar]
   
   ## Prompt de reanudación
   ```
   Continuar proyecto Cambio J desde checkpoint [fecha].
   
   Leer primero:
   - /home/anthony_filgueira/cambios_jota/docs/checkpoints/checkpoint-[fecha].md
   - /home/anthony_filgueira/cambios_jota/CLAUDE.md
   - /home/anthony_filgueira/cambios_jota/docs/requirements/[req-actual]/requirement.md
   
   Contexto rápido:
   [Resumen de 2-3 líneas del estado actual]
   
   Ejecutar: [Próximo paso específico]
   ```
   ```

4. **Hacer commit:**
   ```bash
   git add .
   git commit -m "Checkpoint: Sesión [fecha] - Estado antes de limpiar contexto"
   ```

5. **CERRAR esta conversación**

6. **ABRIR nueva conversación** con el "Prompt de reanudación"

---

## Plan de Trabajo (Requerimientos Principales)

### Requerimientos Completados ✅

| REQ | Nombre | Estado | Horas | Características Clave |
|-----|--------|--------|-------|----------------------|
| 1 | Simulador de Divisas | ✅ | 30h | Selector dinámico de países, cálculos en tiempo real |
| 2 | Registro y Transacciones | ✅ | 46h | Breeze, historial, perfil de usuario |
| 3 | Venta Indirecta | ✅ | 50h | Aprobación multi-nivel, notificaciones, estados |
| 4 | Observaciones y Feedback | ✅ | 39h | Trazabilidad, logs, notificaciones por email |
| 5 | Gestión de Vendedores | ✅ | 47h | Códigos únicos, comisiones, monedero, rankings |
| 6 | Matriz de Divisas | ✅ | 33h | CRUD divisas, pares, corredores, matriz interactiva |
| 7 | Consola de Tasas | ✅ | 37h | CRUD tasas, filtros, historial de auditoría |
| 9 | Historicidad de Tasas | ✅ | ~8h | Snapshots en ventas, comisiones inmutables |
| 10 | UI/UX Mejorada | ✅ | ~12h | Landing, navbar, efectos visuales |
| 11 | Datos de Demostración | ✅ | ~8h | Seeder con 30 ventas, usuarios, vendedores |

**Total Completado:** 310h / ~347h ≈ **89%**

### Requerimientos Pendientes ⏸️

| REQ | Nombre | Estado | Horas Est. | Descripción |
|-----|--------|--------|-----------|-------------|
| 8 | Incentivos y Bonos | ⏸️ | 37h | Metas, bonos variables, liquidaciones |

**Pendiente:** ~37h

**Plan completo:** `/docs/work_plans/plan_trabajo.md`

---

## Detalle de Requerimientos Completados

### REQ 1: Simulador de Divisas ✅
**Ubicación:** `resources/views/welcome.blade.php`
- Selector dinámico de países (🇵🇪 Perú, 🇦🇷 Argentina, 🇨🇱 Chile)
- Cálculos automáticos con tasas desde BD
- Labels y símbolos dinámicos por país
- Mobile-first responsive

### REQ 2: Registro y Transacciones ✅
**Archivos:** `app/Models/Transaction.php`, `TransactionController.php`
- Laravel Breeze (autenticación)
- Modelo Transaction con migración
- Vista de historial de transacciones
- Factory y seeder de datos de prueba

### REQ 3: Venta Indirecta ✅
**Archivos:** `app/Models/Sale.php`, `SaleController.php`
- Campo `approval_status` (pending/approved/rejected/observed)
- Métodos `approve()` y `reject()` en modelo
- Vista tablero de vendedor
- Vista cola de aprobación para admin
- Sistema de notificaciones básico
- Paleta de colores Cambio J aplicada

### REQ 4: Observaciones y Feedback ✅
**Archivos:** `app/Models/SaleLog.php`, Notificaciones
- Sistema de observaciones con trazabilidad
- Logs de quién observó, qué, cuándo
- Notificaciones por email
- Re-edición rápida de ventas observadas
- Carga de comprobantes

### REQ 5: Gestión de Vendedores ✅
**Archivos:** `app/Models/Seller.php`, comisiones
- Generador de código único alfanumérico
- Motor de cálculo de comisiones
- Monedero virtual con saldo en tiempo real
- Registro de liquidaciones
- Dashboard del dueño con métricas BI
- Reportes de rendimiento y rankings
- Exportación CSV/PDF

### REQ 6: Matriz de Divisas ✅
**Archivos:** `Currency.php`, `CurrencyPair.php`, `Corridor.php`
- CRUD completo de divisas
- Modelo de pares de conversión
- Modelo de corredores
- Tabla pivot par-corredor (many-to-many)
- Interfaz de gestión de pares con asignación
- Matriz interactiva con AJAX

### REQ 7: Consola de Tasas ✅
**Archivos:** `ExchangeRate.php`, `ExchangeRateHistory.php`
- CRUD tasas con validaciones dobles
- Filtros inteligentes (activas/inactivas/todas)
- Sistema de auditoría completo
- Observer para registro automático
- Vista de historial con timeline
- Integración con simulador público
- Solo 1 tasa activa por par (exclusividad)

### REQ 9: Historicidad de Tasas ✅
**Implementación:** Snapshots en ventas
- Campo `exchange_rate_snapshot` en sales
- Campo `commission_snapshots` en sales
- Tasas y comisiones inmutables en ventas
- Las viejas tasas se mantienen en BD

### REQ 10: UI/UX Mejorada ✅
**Archivos:** Landing, navbar, efectos CSS
- Mejora de landing page
- Navbar con efectos visuales modernos
- Fondo mejorado con gradientes
- Paleta de colores Cambio J aplicada
- Responsive design mejorado

### REQ 11: Datos de Demostración ✅
**Archivos:** `database/seeders/DemoDataSeeder.php`
- 30 ventas distribuidas en 5 estados
- 5 usuarios (admin + 4 clientes)
- 4 vendedores (VEND001-VEND004)
- Snapshots de comisiones inmutables
- 8 logs de observaciones/rechazos
- Distribución temporal realista (13 días)
- Credenciales: cambios_jotta@innodite.com / password
- Comando: `sail artisan db:seed --class=DemoDataSeeder`
- Testing: 36 tests ejecutados, 100% pass

---

## Convenciones de Código

### Commits
- Formato: `REQ X.Y: Descripción específica de la tarea`
- Ejemplo: `REQ 2.3: Crear modelo Transaction y migración`
- Commits atómicos: uno por tarea completada

### Nombres de archivos
- Controladores: `PascalCase` → `ExchangeRateController.php`
- Modelos: `PascalCase` → `Transaction.php`
- Vistas: `kebab-case` → `exchange-rates/index.blade.php`
- Migraciones: Snake case → `create_transactions_table.php`

### Alpine.js
- Funciones componentes: `camelCase` → `function simulador()`
- Variables reactivas: `camelCase` → `inputUSD`, `penEnviar`

### CSS/Tailwind
- Variables custom: `cj-nombre-descriptivo`
- Clases utility-first, evitar CSS custom innecesario

---

## Comandos Útiles

```bash
# Desarrollo
./vendor/bin/sail up -d
./vendor/bin/sail npm run dev
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed

# Estado del proyecto
git status
git log --oneline -10
php artisan route:list --compact

# Verificar tokens (aproximado)
wc -w docs/**/*.md  # 1 token ≈ 0.75 palabras
```

---

## Recursos del Proyecto

- **Plan de trabajo:** `/docs/work_plans/plan_trabajo.md`
- **Requerimientos:** `/docs/requirements/`
- **Checkpoints:** `/docs/checkpoints/`
- **Simulador (REQ 1):** `resources/views/welcome.blade.php`
- **Modelo tasas:** `app/Models/ExchangeRate.php`

---

## Notas Importantes

- **NO usar Vue 3:** El proyecto usa Alpine.js
- **NO instalar librerías sin consultar:** Mantener stack simple
- **Commits frecuentes:** Uno por tarea, no acumular
- **Documentar TODO:** Cada tarea tiene su `implementation.md`
- **Checkpoints obligatorios:** Al 50-60% de contexto

---

**Última actualización:** 2026-04-20  
**Estado actual:** Definiendo sistema de gestión de requerimientos
