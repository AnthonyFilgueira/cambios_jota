# CLAUDE.md — Proyecto Cambio J

Este archivo provee contexto completo para Claude Code. Léelo antes de tocar cualquier archivo.

---

## Stack técnico

- **Backend:** Laravel 12
- **Frontend:** Vue 3 (Composition API + `<script setup>`)
- **Estilos:** Tailwind CSS
- **Build:** Vite
- **Package manager:** npm
- **sail para ejecutar comanos**

---

## Tarea actual: Simulador de Tasas (Requerimiento 1)

Construir el componente `SimuladorEnvio.vue` — una vista mobile-first que permite al usuario calcular cuántos soles debe enviar y cuántos bolívares recibirá su familiar en Venezuela.

---

## Lógica de negocio — CRÍTICO leer antes de escribir código

### Las 3 tasas del sistema

| Variable | Descripción | Quién la define | Editable |
|---|---|---|---|
| `tasaUSD` | Tasa BCV del dólar (ej: 479.77750) | Dueño del negocio | Sí, manual |
| `tasaEUR` | Tasa BCV del euro (ej: 565.98392) | Dueño del negocio | Sí, manual |
| `tasaVES` | Tasa propia de Cambio J (ej: 173.71) | Dueño del negocio | Sí, en cualquier momento |

### Las 3 fórmulas de cálculo

El resultado siempre muestra DOS valores:
- **TÚ ENVÍAS** → monto en PEN (soles peruanos) que el cliente deposita
- **TU FAMILIAR RECIBE** → monto en VES (bolívares) que llega a Venezuela

```
// CASO 1: Cliente ingresa SOLES directamente
PEN ingresados × tasaVES = VES a recibir

// CASO 2: Cliente ingresa DÓLARES (quiere convertir a tasa BCV dólar)
USD × tasaUSD = VES intermedios
VES intermedios ÷ tasaVES = PEN a enviar
// El VES intermedios también es el VES final que recibe el familiar

// CASO 3: Cliente ingresa DÓLARES (quiere convertir a tasa BCV euro)
USD × tasaEUR = VES intermedios
VES intermedios ÷ tasaVES = PEN a enviar
// El VES intermedios también es el VES final que recibe el familiar
```

### Regla importante
Los 3 inputs (USD, EUR, PEN) conviven en pantalla al mismo tiempo.
Cuando el usuario escribe en uno, los otros dos se limpian automáticamente.
Solo un input activo a la vez.

---

## Parámetros fijos del negocio (hardcodeados en esta versión)

```js
const paisOrigen = { nombre: 'Perú', bandera: '🇵🇪', moneda: 'PEN', simbolo: 'S/' }
const paisDestino = { nombre: 'Venezuela', bandera: '🇻🇪', moneda: 'VES', simbolo: 'Bs.' }
```

No hay selectores de país ni de moneda en esta versión. Son fijos.

---

## Estructura de archivos esperada

```
resources/
└── js/
    ├── components/
    │   └── SimuladorEnvio.vue   ← componente principal
    ├── composables/
    │   └── useSimulador.js      ← lógica de cálculo extraída
    └── Pages/
        └── Simulador.vue        ← página que monta el componente (si usas Inertia)
```

---

## Diseño visual — Branding Cambio J

### Paleta de colores
```css
--cj-verde:        #1A3A2A;   /* fondo header, cajas oscuras */
--cj-verde-medio:  #3A7A5A;   /* bordes, acentos */
--cj-verde-claro:  #8BAF96;   /* textos secundarios sobre verde */
--cj-verde-mint:   #F0F7F3;   /* fondos suaves */
--cj-dorado:       #D4A843;   /* acento principal, botón swap, caja "recibe" */
--cj-dorado-texto: #7A5A10;   /* texto sobre fondo dorado */
--cj-crema:        #F7F5F0;   /* fondo general de la app */
--cj-blanco:       #FFFFFF;   /* fondo de la card principal */
--cj-borde:        #E0DDD5;   /* bordes suaves */
--cj-gris:         #8A8880;   /* labels, textos terciarios */
```

### Tipografía
- Fuente: sistema (sans-serif nativo)
- Labels: 9-10px, uppercase, letter-spacing
- Valores: 18-20px, font-weight 500
- Todo en español

### Layout
- Mobile-first, ancho máximo del contenedor: 400px centrado
- Card principal: fondo blanco, border-radius 20px, borde 0.5px
- Header: fondo `--cj-verde`, logo "CJ" en cuadro dorado

---

## Estructura del componente SimuladorEnvio.vue

```vue
<template>
  <!-- Header verde con logo Cambio J -->
  <!-- Sección tasas del día: USD | EUR | VES con botón "Editar" -->
  <!-- Ruta: 🇵🇪 Perú → 🇻🇪 Venezuela (fija, no editable) -->
  <!-- Inputs: En dólares / En euros / En soles (los 3 visibles) -->
  <!-- Caja verde oscura: TÚ ENVÍAS → X,XX PEN -->
  <!-- Separador con tasa aplicada: 173,71 VES/PEN -->
  <!-- Caja dorada: TU FAMILIAR RECIBE → X,XX VES 🇻🇪 -->
  <!-- Botón: Iniciar envío -->
</template>
```

---

## Comportamiento del botón "Editar" tasas

1. Al hacer clic en "✎ Editar": los displays de las 3 tasas se reemplazan por inputs numéricos editables
2. Al hacer clic en "✓ Guardar": se guardan los nuevos valores, se vuelven a mostrar como texto y se recalcula automáticamente
3. Las tasas editadas deben persistir en `localStorage` con la clave `cambioJ_tasas`
4. Al montar el componente, leer primero de `localStorage`; si no existe, usar los valores por defecto

### Valores por defecto de las tasas
```js
const defaultTasas = {
  usd: 479.77750,
  eur: 565.98392,
  ves: 173.71000
}
```

---

## Formateo de números

Usar `Intl.NumberFormat` con locale `es-PE` para todos los valores mostrados:

```js
// Para tasas (5 decimales)
new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 5 }).format(valor)

// Para montos PEN y VES (2 decimales)
new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(valor)
```

---

## Lo que NO hacer

- No conectar a ninguna API externa en esta versión (las tasas son manuales)
- No agregar selectores de país ni de moneda (están hardcodeados)
- No usar opciones de Swap en esta versión
- No implementar autenticación ni rutas protegidas en esta tarea
- No instalar librerías adicionales — solo Vue 3 + Tailwind que ya están en el proyecto

---

## Verificación final — prueba estos casos antes de dar por terminado

| Input | Valor | PEN esperado | VES esperado |
|---|---|---|---|
| PEN | 100 | 100,00 | 17.371,00 |
| USD (BCV) | 100 | 281,51 | 47.977,50 |
| USD (EUR) | 100 | 332,33 | 56.598,39 |

*(Calculado con tasas por defecto: USD=479.77750, EUR=565.98392, VES=173.71)*