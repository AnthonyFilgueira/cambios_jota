# REQ 10: Mejora de UI/UX Landing, Login y Registro

**Estado:** 🔄 EN PROGRESO  
**Horas estimadas:** 6h  
**Prioridad:** ALTA  
**Propósito:** Preparación para video demo

---

## Descripción General

Mejorar la presentación visual de la landing page (welcome), pantallas de login y registro para que el sistema tenga una apariencia profesional y atractiva para videos de demostración.

---

## Problemas Actuales

1. **Landing (welcome.blade.php):**
   - ❌ No tiene botones visibles de Login/Registro
   - ❌ Usuario no sabe cómo acceder al sistema
   - ✅ Simulador está bonito pero aislado

2. **Login/Registro:**
   - ❌ Diseño básico de Breeze (sin personalización)
   - ❌ No aplica paleta Cambio J
   - ❌ No tiene branding

---

## Mejoras a Implementar

### 1. Landing Page (Welcome)

**Cambios:**
- Agregar header fijo con logo y botones Login/Registro
- Botones destacados con paleta Cambio J
- Efecto hover en botones
- Responsive (mobile-first)

**Diseño:**
```
┌──────────────────────────────────────┐
│ [Logo CJ]      Inicio  Ingresar  [Registrarse] │ ← Header fijo
├──────────────────────────────────────┤
│                                      │
│      [SIMULADOR EXISTENTE]           │
│                                      │
└──────────────────────────────────────┘
```

---

### 2. Pantalla de Login

**Mejoras:**
- Logo Cambio J arriba del formulario
- Card con sombra y bordes redondeados
- Inputs con íconos
- Botón gradiente morado
- Link "¿No tienes cuenta? Regístrate"
- Fondo con gradiente sutil

---

### 3. Pantalla de Registro

**Mejoras:**
- Diseño consistente con Login
- Formulario de 2 columnas (desktop)
- Indicadores visuales de campos requeridos
- Confirmación de contraseña con ícono
- Términos y condiciones (checkbox)

---

## Paleta de Colores (Cambio J)

```css
Morados:
--cj-morado-profundo: #5B21B6  (Botones principales)
--cj-morado-medio:    #7C3AED  (Hover, acentos)
--cj-morado-claro:    #DDD6FE  (Fondos suaves)

Acentos:
--cj-turquesa:        #14B8A6  (Links, íconos)
--cj-rosa:            #EC4899  (Alertas, errores)

Neutros:
--cj-fondo:           #F3F4F6  (Background general)
--cj-texto:           #374151  (Texto principal)
--cj-texto-claro:     #6B7280  (Texto secundario)
```

---

## Wireframes

### Landing Header
```
┌────────────────────────────────────────────┐
│  [🔷CJ]  Cambios Jotta                    │
│                                             │
│                         [Ingresar] [Registrarse] │
└────────────────────────────────────────────┘
```

### Login
```
        ┌──────────────────────┐
        │    [Logo CJ]         │
        │                      │
        │  Inicia Sesión       │
        │                      │
        │  Email: [___________]│
        │  Pass:  [___________]│
        │                      │
        │  [Recordarme]        │
        │                      │
        │  [INGRESAR →]        │
        │                      │
        │  ¿No tienes cuenta?  │
        │  Regístrate aquí     │
        └──────────────────────┘
```

---

## Tareas

| # | Tarea | Horas |
|---|-------|-------|
| 10.1 | Mejorar welcome.blade.php (header + botones) | 2h |
| 10.2 | Mejorar login.blade.php (branding + diseño) | 2h |
| 10.3 | Mejorar register.blade.php (formulario) | 2h |

**Total:** 6h

---

## Criterios de Aceptación

- ✅ Landing tiene botones de Login/Registro visibles
- ✅ Diseño consistente con paleta Cambio J
- ✅ Responsive en mobile y desktop
- ✅ Branding (logo CJ) presente en todas las vistas
- ✅ Transiciones suaves y efectos hover
- ✅ Listo para grabar video demo

---

**Fecha creación:** 2026-04-20  
**Propósito:** Video demo del sistema
