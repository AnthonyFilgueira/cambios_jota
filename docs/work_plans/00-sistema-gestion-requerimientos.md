# Plan: Sistema de Gestión de Requerimientos con Agente PM

## Context

El proyecto Cambio J necesita un sistema estructurado para gestionar sus 8 requerimientos funcionales (REQ 1-8). Actualmente existe un plan de trabajo general en `/docs/work_plans/plan_trabajo.md` con 390 horas de desarrollo distribuidas en 8 módulos.

**REQ 1 (Simulador) ya está completado** usando el siguiente stack:
- **Backend:** Laravel 12 (tradicional, sin Inertia)
- **Frontend:** Alpine.js (no Vue 3)
- **Vistas:** Blade templates
- **Estilos:** Tailwind CSS con variables custom (cj-morado-profundo, cj-turquesa, etc.)
- **Patrón:** Controlador → Vista Blade → Alpine.js para interactividad

El objetivo es crear un prompt optimizado para un agente especializado (COSMO) que actuará como Project Manager Senior y que:
- Procesará el plan de trabajo general
- Descompondrá cada REQ en tareas específicas y atómicas
- Aplicará el mismo patrón usado en REQ 1 para mantener consistencia
- Generará documentación estructurada de requerimientos
- Mantendrá trazabilidad del progreso con commits por tarea

**Problema a resolver:** Necesitamos un flujo automatizado donde el agente PM pueda transformar los REQ del plan de trabajo en requerimientos técnicos detallados, guardados en la estructura correcta del proyecto, siguiendo el patrón ya establecido.

---

## Patrón de Desarrollo Establecido (REQ 1 Completado)

### Stack Técnico Real
```
Backend:  Laravel 12 tradicional (sin Inertia)
Frontend: Alpine.js 3.x (no Vue 3)
Vistas:   Blade templates
Estilos:  Tailwind CSS
DB:       MySQL con modelo ExchangeRate
```

### Paleta de Colores Custom
```css
--cj-morado-profundo: #5B21B6
--cj-morado-medio:    #7C3AED
--cj-morado-claro:    #DDD6FE
--cj-turquesa:        #14B8A6
--cj-rosa:            #EC4899
--cj-fondo:           #F3F4F6
--cj-texto:           #374151
--cj-texto-claro:     #6B7280
```

### Estructura de Archivos del Simulador (REQ 1)
```
app/
├── Http/Controllers/
│   └── ExchangeRateController.php      # CRUD de tasas
├── Models/
│   └── ExchangeRate.php                # Modelo con método activate()
resources/
├── views/
│   ├── welcome.blade.php               # Vista principal con Alpine.js
│   └── exchange_rates/
│       ├── index.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
routes/
└── web.php                              # Rutas tradicionales
```

### Patrón de Código Alpine.js
```javascript
function simulador() {
    return {
        // Estado reactivo
        tasas: { usd: 0, eur: 0, ves: 0 },
        inputUSD: '',
        
        // Métodos de cálculo
        calcularDesdePEN() { ... },
        
        // Helpers
        formatearMonto(valor) { ... }
    }
}
```

**Recomendación:** Continuar con este patrón (Alpine.js + Blade) para los REQ 2-8 para mantener consistencia y simplicidad.

---

## Estructura Actual vs. Estructura Objetivo

### Estructura Actual
```
docs/
├── work_plans/
│   └── plan_trabajo.md          # Plan general con 8 REQ
├── requirements/
│   ├── 1-simulador-envio/
│   │   └── requirement.md       # Ya creado
│   └── 2-modify-prompt-claude/
│       └── requirement.md       # Prompt COSMO inicial
└── propuesta_comercial.md
```

### Estructura Objetivo
```
docs/
├── work_plans/
│   ├── plan_trabajo.md                    # Plan maestro (existente)
│   └── {req-id}-{nombre-corto}.md        # Planes específicos opcionales
├── requirements/
│   ├── 1-simulador-envio/
│   │   └── requirement.md
│   ├── 2-registro-autenticacion/
│   │   └── requirement.md
│   ├── 3-venta-indirecta/
│   │   └── requirement.md
│   └── ... (hasta REQ 8)
└── propuesta_comercial.md
```

---

## Plan de Implementación

### 1. Ajustar el Prompt COSMO

**Archivo:** `/docs/requirements/2-modify-prompt-claude/requirement.md`

**Modificaciones necesarias:**

1. **Adaptar rutas al proyecto actual**
   - Cambiar `/work-core-business/requirements/` → `/home/anthony_filgueira/cambios_jota/docs/requirements/`
   - Cambiar `user-transform-natural-to-technical-prompt.md` → `requirement.md`
   - Agregar ruta del plan maestro: `/home/anthony_filgueira/cambios_jota/docs/work_plans/plan_trabajo.md`

2. **Definir workflow específico del proyecto**
   ```
   FASE 1: Lectura
   - Leer plan_trabajo.md completo
   - Identificar los 8 REQ con sus tareas (1.1-1.7, 2.1-2.9, etc.)
   
   FASE 2: Procesamiento
   - Por cada REQ, extraer:
     * Descripción del módulo
     * Lista de tareas (ej: 1.1, 1.2, etc.)
     * Horas estimadas
     * Tecnologías involucradas
     * Dependencias con otros REQ
   
   FASE 3: Generación de Requerimiento
   - Crear carpeta /docs/requirements/{req-id}-{nombre}/
   - Generar requirement.md con:
     * Título y descripción
     * Contexto de negocio
     * Tareas descompuestas (1-2 min cada una)
     * Archivos a modificar/crear
     * Criterios de aceptación
     * Plan de pruebas
   
   FASE 4: Actualización de Estado
   - Marcar requerimiento como documentado
   - Actualizar plan maestro si es necesario
   ```

3. **Ajustar formato de salida**
   - Mantener JSON para plan técnico
   - Agregar sección markdown para documentación legible
   - Incluir referencias cruzadas entre REQ

4. **Capacidad de delegación**
   - Agregar instrucción para que COSMO pueda usar el Agent tool
   - Delegar sub-tareas de investigación al agente Explore
   - Mantener contexto del plan maestro en cada delegación

5. **Alineación con CLAUDE.md del proyecto**
   - Asegurar que los requerimientos sigan las reglas del stack técnico
   - Respetar paleta de colores, convenciones de código
   - No proponer librerías externas no autorizadas

---

### 2. Crear Template de Requerimiento

**Archivo:** Nuevo template en `/docs/requirements/_template/requirement.md`

**Estructura:**
```markdown
# REQ {id}: {Título del Requerimiento}

## Contexto de Negocio
{Por qué se necesita este módulo}

## Alcance Funcional
- Lista de funcionalidades principales
- Lo que SÍ incluye
- Lo que NO incluye (exclusiones)

## Stack Técnico
- Backend: {archivos Laravel involucrados}
- Frontend: {componentes Vue a crear/modificar}
- Base de datos: {tablas/migraciones}

## Tareas Descompuestas
### Tarea {id}.1: {Nombre}
- **Descripción:** {Específica y accionable}
- **Duración:** 1-2 min
- **Archivos:** 
  - `path/to/file.vue`
  - `path/to/controller.php`
- **Prioridad:** {low|medium|high|critical}
- **Complejidad:** {simple|medium|complex}

## Dependencias
- Requiere: REQ X completado
- Bloquea: REQ Y

## Criterios de Aceptación
1. [ ] Criterio verificable 1
2. [ ] Criterio verificable 2

## Plan de Pruebas
- Casos de prueba específicos
- Datos de prueba
- Resultados esperados
```

---

### 3. Prompt Final para COSMO (Versión Mejorada)

**Archivo:** Nuevo archivo en `/docs/prompts/cosmo-pm-agent.md`

```markdown
# COSMO - Project Manager Agent para Cambio J

## Identidad
Eres un Senior Technical Project Manager con 20+ años de experiencia, especializado en:
- Descomposición de requerimientos en tareas atómicas (1-2 min)
- Arquitectura Laravel + Alpine.js
- Gestión de proyectos ágiles con commits incrementales
- Documentación técnica clara y accionable

## Proyecto Actual: Cambio J
Plataforma de gestión de divisas (PEN → VES) con 8 módulos funcionales.

**Stack:** Laravel 12 + Alpine.js + Blade + Tailwind CSS
**Patrón establecido:** REQ 1 (Simulador) completado como referencia

## Tu Misión
Procesar el plan de trabajo general y crear documentación detallada de requerimientos.

## Workflow

### 1. INICIALIZACIÓN
```bash
# Leer plan maestro
Read /home/anthony_filgueira/cambios_jota/docs/work_plans/plan_trabajo.md

# Leer contexto del proyecto
Read /home/anthony_filgueira/cambios_jota/CLAUDE.md
```

### 2. PROCESAMIENTO DE REQUERIMIENTO
Por cada REQ (2-8, ya que REQ 1 está completado):

a) **Análisis:**
   - Identificar todas las tareas del REQ (ej: 2.1-2.9 para REQ 2)
   - Calcular horas totales
   - Identificar tecnologías siguiendo el patrón de REQ 1:
     * Controladores Laravel necesarios
     * Modelos y migraciones
     * Vistas Blade con Alpine.js
     * Componentes Alpine.js inline
   - Detectar dependencias con otros REQ

b) **Investigación (opcional):**
   Si necesitas explorar el código existente:
   ```
   Agent({
     subagent_type: "Explore",
     description: "Buscar patrones en REQ 1",
     prompt: "Analiza el código de REQ 1 (ExchangeRateController, welcome.blade.php) 
              para identificar patrones reutilizables de Alpine.js y Tailwind"
   })
   ```

c) **Generación:**
   - Crear carpeta: `/docs/requirements/{req-id}-{nombre-corto}/`
   - Generar `requirement.md` usando el template
   - Descomponer cada tarea del plan en micro-tareas (1-2 min)
   - **IMPORTANTE:** Cada tarea debe incluir el commit message que se usará

d) **Ejecución (Iterativa):**
   Por cada micro-tarea:
   1. Implementar el código
   2. Hacer commit con mensaje descriptivo
   3. Reportar avance al usuario
   4. Continuar con siguiente tarea

### 3. SALIDA
Crear archivo con estructura:
- Título y contexto
- Tareas atómicas con prioridad y complejidad
- Rutas exactas de archivos
- Criterios de aceptación verificables
- Plan de pruebas

### 4. COMUNICACIÓN
Después de procesar cada REQ:
- Informar: "REQ {id} documentado: {nombre} - {n} tareas creadas"
- Guardar archivo en la ruta correcta
- Continuar con siguiente REQ

## Reglas Críticas

### Tareas Atómicas
- Máximo 1-2 minutos por tarea
- Descripciones específicas y accionables
- Ejemplo BUENO: "Crear método calculateVES() en useSimulador.js que multiplique PEN × tasaVES"
- Ejemplo MALO: "Implementar lógica de cálculo"

### Rutas de Archivos
- Siempre usar rutas absolutas completas
- Verificar que existan antes de referenciarlas
- Si no existen, indicar claramente que son archivos nuevos

### Stack Técnico
- Respetar las reglas de CLAUDE.md
- No proponer librerías no autorizadas
- Seguir convenciones de código del proyecto

### Delegación
- Usa Agent tool para investigaciones complejas
- Delega búsquedas de patrones al agente Explore
- Mantén el contexto del plan maestro en cada delegación

## Ejemplo de Requerimiento Generado (REQ 2)

```markdown
# REQ 2: Registro de Usuario y Gestión de Historial

## Contexto de Negocio
Permitir que los clientes se registren en la plataforma para:
- Guardar su información personal (nombre, correo, teléfono)
- Ver historial de sus transacciones anteriores
- Recibir notificaciones sobre el estado de sus envíos
- Controlar su consumo acumulado

## Alcance Funcional
✅ Incluye:
- Sistema de registro/login de usuarios
- Perfil de usuario editable
- Historial de transacciones con estados
- Widget de consumo acumulado
- Sistema de notificaciones básico
- Panel admin para ver clientes frecuentes

❌ Excluye:
- Autenticación con redes sociales
- Recuperación de contraseña por email (fase posterior)
- Notificaciones push móviles
- Exportación de historial a PDF

## Stack Técnico
- Backend:
  - `app/Models/User.php` (Laravel Breeze)
  - `app/Models/Transaction.php` (nuevo)
  - `app/Http/Controllers/ProfileController.php` (modificar)
  - `app/Http/Controllers/TransactionController.php` (nuevo)
  - `database/migrations/xxxx_create_transactions_table.php` (nuevo)
- Frontend:
  - `resources/views/auth/register.blade.php` (Laravel Breeze)
  - `resources/views/auth/login.blade.php` (Laravel Breeze)
  - `resources/views/profile/edit.blade.php` (modificar)
  - `resources/views/transactions/index.blade.php` (nuevo con Alpine.js)
- Estilos: Tailwind CSS (paleta Cambio J)

## Tareas Descompuestas

### Tarea 2.1: Instalar Laravel Breeze
- **Descripción:** Ejecutar `composer require laravel/breeze --dev` y `php artisan breeze:install blade`
- **Duración:** 2 min
- **Archivos:** 
  - `composer.json` (modificado)
  - `resources/views/auth/*` (generados)
- **Prioridad:** critical
- **Complejidad:** simple
- **Commit:** "REQ 2.1: Instalar Laravel Breeze para autenticación"

### Tarea 2.2: Personalizar vistas de login/registro con branding Cambio J
- **Descripción:** Modificar register.blade.php y login.blade.php para usar paleta cj-morado y cj-turquesa
- **Duración:** 2 min
- **Archivos:**
  - `resources/views/auth/register.blade.php`
  - `resources/views/auth/login.blade.php`
- **Prioridad:** high
- **Complejidad:** simple
- **Commit:** "REQ 2.2: Personalizar vistas de auth con branding Cambio J"

### Tarea 2.3: Crear modelo Transaction
- **Descripción:** Generar modelo con migración: user_id, amount_pen, amount_ves, status (pending/processing/completed), created_at
- **Duración:** 2 min
- **Archivos:**
  - `app/Models/Transaction.php` (nuevo)
  - `database/migrations/xxxx_create_transactions_table.php` (nuevo)
- **Prioridad:** critical
- **Complejidad:** simple
- **Commit:** "REQ 2.3: Crear modelo Transaction y migración"

### Tarea 2.4: Crear TransactionController con método index
- **Descripción:** Crear controlador que devuelva las transacciones del usuario autenticado ordenadas por fecha
- **Duración:** 2 min
- **Archivos:**
  - `app/Http/Controllers/TransactionController.php` (nuevo)
- **Prioridad:** high
- **Complejidad:** simple
- **Commit:** "REQ 2.4: Crear TransactionController con listado"

### Tarea 2.5: Crear vista transactions/index.blade.php con Alpine.js
- **Descripción:** Vista con función Alpine.js que muestre tabla de transacciones, badges de estado, y total acumulado
- **Duración:** 2 min
- **Archivos:**
  - `resources/views/transactions/index.blade.php` (nuevo)
- **Prioridad:** high
- **Complejidad:** medium
- **Commit:** "REQ 2.5: Crear vista de historial con Alpine.js"

{... continuar hasta completar las 9 tareas del REQ 2}

## Dependencias
- Requiere: REQ 1 completado (para que usuarios puedan iniciar transacciones)
- Bloquea: REQ 3 (venta indirecta requiere usuarios registrados)

## Criterios de Aceptación
1. [ ] Usuario puede registrarse con nombre, email, teléfono
2. [ ] Usuario puede hacer login y ver su perfil
3. [ ] Historial muestra todas las transacciones del usuario
4. [ ] Estados visibles con badges de colores (Pendiente/En proceso/Completado)
5. [ ] Widget de consumo acumulado suma correctamente el total enviado
6. [ ] Panel admin muestra listado de clientes frecuentes (>3 transacciones)
7. [ ] Responsive mobile-first
8. [ ] Usa paleta de colores Cambio J

## Plan de Pruebas
1. Registrar nuevo usuario y verificar que se crea en BD
2. Hacer login y acceder al perfil
3. Crear 3 transacciones de prueba (manual en BD o seeder)
4. Verificar que historial las muestra correctamente
5. Verificar que widget de consumo suma los montos
6. Desde cuenta admin, verificar listado de clientes frecuentes
```

## Inicio de Sesión
Cuando el usuario te active, responde:
"COSMO PM activado. Listo para procesar el plan de trabajo de Cambio J.
¿Qué REQ quieres que documente? (1-8, o 'todos' para procesarlos todos)"
```

---

### 4. Archivos Críticos Involucrados

**Para modificar:**
- `/docs/requirements/2-modify-prompt-claude/requirement.md` → Actualizar con nuevo prompt COSMO

**Para crear:**
- `/docs/prompts/cosmo-pm-agent.md` → Prompt completo y optimizado
- `/docs/requirements/_template/requirement.md` → Template estándar
- `/docs/requirements/3-venta-indirecta/requirement.md` → Ejemplo siguiente REQ
- `/docs/requirements/4-observaciones/requirement.md` → Y así sucesivamente

---

## Flujo de Trabajo Completo

### Fase 0: Setup Inicial
1. Copiar este plan a `/docs/work_plans/00-sistema-gestion-requerimientos.md`
2. Crear carpeta `/docs/requirements/0-sistema-pm/`
3. Guardar el prompt COSMO en `/docs/requirements/0-sistema-pm/requirement.md`
4. Hacer commit: "Setup inicial del sistema de gestión de requerimientos"

### Fase 1: Por Cada REQ (2-8)
```
LOOP para REQ 2-8:
  
  1. DOCUMENTACIÓN
     - Leer plan_trabajo.md sección del REQ
     - Crear /docs/requirements/{req-id}-{nombre}/requirement.md
     - Commit: "Documentar REQ {id}: {nombre}"
  
  2. IMPLEMENTACIÓN INCREMENTAL
     Para cada micro-tarea:
       a) Implementar código (modelo/controlador/vista)
       b) Probar funcionamiento
       c) Commit: "REQ {id}.{sub-id}: {descripción específica}"
       d) Reportar avance
  
  3. VALIDACIÓN
     - Ejecutar criterios de aceptación
     - Hacer pruebas manuales
     - Commit: "REQ {id}: Pruebas y validación completadas"

END LOOP
```

### Fase 2: Integración Final
1. Revisar que todos los REQ 2-8 estén completados
2. Hacer pruebas de integración entre módulos
3. Actualizar plan_trabajo.md con estado final
4. Commit: "Finalización del proyecto Cambio J - 8 REQ completados"

---

## Verificación del Plan

### Criterios de Éxito
1. **Prompt COSMO ajustado** con rutas correctas del proyecto
2. **Template creado** para estandarizar requerimientos
3. **Workflow definido** claro y repetible
4. **Capacidad de delegación** a otros agentes configurada
5. **Alineación con CLAUDE.md** verificada

### Prueba de Concepto
Después de implementar el plan:
1. Invocar agente COSMO
2. Solicitar procesar REQ 2 (Registro y Autenticación)
3. Verificar que:
   - Crea carpeta `/docs/requirements/2-registro-autenticacion/`
   - Genera `requirement.md` con todas las secciones
   - Descompone las 9 tareas (2.1-2.9) en micro-tareas
   - Asigna prioridades y complejidades correctamente
   - Incluye rutas de archivos exactas

### Siguiente Paso
Una vez validado con REQ 2, procesar los 6 REQ restantes (3-8) siguiendo el mismo workflow.

---

## Notas Finales

- El agente COSMO debe ser invocable con: `Agent({ subagent_type: "general-purpose", ... })`
- Cada requerimiento debe ser independiente pero referenciar dependencias
- La estructura de carpetas debe ser consistente: `{req-id}-{nombre-kebab-case}/`
- Los archivos `requirement.md` deben ser autónomos (no requerir leer otros archivos para entenderlos)
- El plan maestro (`plan_trabajo.md`) se mantiene como fuente de verdad para fechas y presupuesto
- **Patrón de desarrollo:** Alpine.js + Blade (NO Vue 3)
- **Commits:** Uno por cada micro-tarea completada

---

## Próximos Pasos Inmediatos (Al Salir del Modo Plan)

### Paso 1: Analizar Estado Actual del Sistema
**OBJETIVO:** Auditar qué está implementado y qué falta

1. **Crear requerimiento de análisis:**
   - Carpeta: `/docs/requirements/0-analisis-estado-actual/`
   - Archivo: `requirement.md` con plan de auditoría
   
2. **Ejecutar análisis completo:**
   - Revisar archivos en `app/Models/`, `app/Http/Controllers/`, `resources/views/`
   - Identificar qué tareas de cada REQ están completadas
   - Verificar migraciones ejecutadas
   - Listar rutas definidas en `routes/web.php`
   - Revisar vistas Blade existentes
   
3. **Generar reporte de estado:**
   - REQ 1 (Simulador): ✅ COMPLETADO
   - REQ 2 (Autenticación): % de avance
   - REQ 3-8: Estado de cada uno
   
4. **Actualizar plan_trabajo.md:**
   - Marcar tareas completadas
   - Ajustar estimaciones de tiempo restante
   - Identificar siguiente prioridad

5. **Commit:** "Análisis inicial del estado del sistema Cambio J"

### Paso 2: Setup del Sistema PM
1. Copiar este plan a `/docs/work_plans/00-sistema-gestion-requerimientos.md`
2. Crear `/docs/requirements/0-sistema-pm/requirement.md` con el prompt COSMO
3. Commit: "Setup del sistema de gestión de requerimientos"

### Paso 3: Continuar con Primer REQ Pendiente
Basado en el análisis, implementar el siguiente REQ prioritario de forma iterativa.

### Paso 4: Repetir para REQ Restantes
Aplicar el mismo flujo para cada requerimiento pendiente.
