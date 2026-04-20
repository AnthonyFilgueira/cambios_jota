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
--cj-morado-profundo: #5B21B6;
--cj-morado-medio:    #7C3AED;
--cj-morado-claro:    #DDD6FE;

/* Acentos */
--cj-turquesa:        #14B8A6;
--cj-rosa:            #EC4899;

/* Neutros */
--cj-fondo:           #F3F4F6;
--cj-texto:           #374151;
--cj-texto-claro:     #6B7280;
```

Configuradas en: `tailwind.config.js`

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

## Plan de Trabajo (8 REQ)

| REQ | Nombre | Estado | Horas | Notas |
|-----|--------|--------|-------|-------|
| 1 | Simulador de Divisas | ✅ COMPLETO | 30h | `welcome.blade.php` + `ExchangeRateController` |
| 2 | Registro y Autenticación | 🔄 PARCIAL | 46h | Breeze instalado, falta historial |
| 3 | Venta Indirecta | ⏸️ PENDIENTE | 50h | - |
| 4 | Observaciones y Feedback | ⏸️ PENDIENTE | 39h | - |
| 5 | Gestión de Vendedores | ⏸️ PENDIENTE | 47h | - |
| 6 | Matriz de Divisas | ⏸️ PENDIENTE | 33h | - |
| 7 | Consola de Tasas | ✅ COMPLETO | 37h | CRUD tasas, filtros, historial, validaciones |
| 8 | Incentivos y Bonos | ⏸️ PENDIENTE | 37h | - |

**Plan completo:** `/docs/work_plans/plan_trabajo.md`

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
