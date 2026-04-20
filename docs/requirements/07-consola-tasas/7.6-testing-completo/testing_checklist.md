# Checklist de Testing Manual - REQ 7

**Servidor:** http://localhost  
**Fecha:** 2026-04-20  
**Duración estimada:** 3h

---

## Pre-requisitos

```bash
# Servidor levantado
./vendor/bin/sail up -d

# Migraciones ejecutadas
./vendor/bin/sail artisan migrate:status

# Datos de prueba cargados
./vendor/bin/sail artisan db:seed --class=ExchangeRateSeeder
```

---

## 1. SIMULADOR DINÁMICO (Página Principal)

**URL:** http://localhost

### 🧪 Test 1.1: Selector de países
- [ ] Abrir página principal
- [ ] Verificar que aparecen 3 opciones:
  - 🇵🇪 Perú
  - 🇦🇷 Argentina
  - 🇨🇱 Chile
- [ ] Por defecto debe estar seleccionado "Perú"

### 🧪 Test 1.2: Cálculos con PEN (Perú)
- [ ] Seleccionar "🇵🇪 Perú"
- [ ] Verificar label: "En Sol Peruano (Directo)"
- [ ] Verificar símbolo: "S/"
- [ ] Ingresar: 100 PEN
- [ ] Resultado esperado: **17,371.00 VES** (tasa 173.71)
- [ ] Verificar que muestre tasa: "1 PEN = 173.71000 VES"

### 🧪 Test 1.3: Cálculos con ARS (Argentina)
- [ ] Seleccionar "🇦🇷 Argentina"
- [ ] Verificar label: "En Peso Argentino (Directo)"
- [ ] Verificar símbolo: "$"
- [ ] Ingresar: 100 ARS
- [ ] Resultado esperado: **250.00 VES** (tasa 2.50)
- [ ] Verificar que muestre tasa: "1 ARS = 2.50000 VES"

### 🧪 Test 1.4: Cálculos con CLP (Chile)
- [ ] Seleccionar "🇨🇱 Chile"
- [ ] Verificar label: "En Peso Chileno (Directo)"
- [ ] Verificar símbolo: "$"
- [ ] Ingresar: 1000 CLP
- [ ] Resultado esperado: **550.00 VES** (tasa 0.55)
- [ ] Verificar que muestre tasa: "1 CLP = 0.55000 VES"

### 🧪 Test 1.5: Cambio dinámico entre países
- [ ] Ingresar 100 en Perú → verificar resultado
- [ ] Cambiar a Argentina (sin borrar input)
- [ ] Verificar que recalcula automáticamente con nueva tasa
- [ ] Cambiar a Chile
- [ ] Verificar que recalcula automáticamente con nueva tasa

---

## 2. CONSOLA DE TASAS - Index

**URL:** http://localhost/exchange_rates  
**Requiere:** Usuario autenticado

### 🧪 Test 2.1: Listado completo
- [ ] Iniciar sesión
- [ ] Ir a "Consola de Tasas"
- [ ] Verificar que aparecen 3 tasas:
  - PEN → VES (173.71)
  - ARS → VES (2.50)
  - CLP → VES (0.55)
- [ ] Verificar que todas están activas (badge verde)

### 🧪 Test 2.2: Referencias BCV en header
- [ ] Verificar card superior con:
  - USD → VES (BCV): **479.78**
  - EUR → VES (BCV): **565.98**
  - Par activo: mostrar primera tasa activa
- [ ] Verificar que los valores tienen 2 decimales

### 🧪 Test 2.3: Filtro por divisa origen
- [ ] Seleccionar filtro "Divisa Origen: PEN"
- [ ] Click "Filtrar"
- [ ] Verificar que solo aparece tasa PEN → VES
- [ ] Limpiar filtro
- [ ] Verificar que vuelven a aparecer las 3 tasas

### 🧪 Test 2.4: Filtro por estado
- [ ] Seleccionar filtro "Estado: Activa"
- [ ] Verificar que aparecen las 3 tasas
- [ ] Cambiar a "Estado: Inactiva"
- [ ] Verificar que no aparece ninguna
- [ ] (Si hubiera inactivas, deberían aparecer)

### 🧪 Test 2.5: Acciones de tabla
- [ ] Verificar que cada fila tiene botones:
  - 👁️ Ver
  - ✏️ Editar
  - 🔴 Desactivar (si está activa)
- [ ] Hover sobre botones muestra tooltip

---

## 3. CONSOLA DE TASAS - Create

**URL:** http://localhost/exchange_rates/create

### 🧪 Test 3.1: Carga inicial
- [ ] Click en "Nueva Tasa"
- [ ] Verificar que carga formulario
- [ ] Verificar que aparece selector de par de divisas
- [ ] Verificar que aparecen 3 campos:
  - Tasa VES (específica del par)
  - Tasa USD (referencia BCV)
  - Tasa EUR (referencia BCV)

### 🧪 Test 3.2: Placeholders y ejemplos
- [ ] Verificar placeholders:
  - Tasa VES: "Ej: 173.71"
  - Tasa USD: "Ej: 479.78"
  - Tasa EUR: "Ej: 565.98"
- [ ] Verificar textos de ayuda debajo de cada campo

### 🧪 Test 3.3: Crear tasa nueva (duplicar PEN)
- [ ] Seleccionar par: PEN → VES
- [ ] Ingresar Tasa VES: 175.00
- [ ] Ingresar Tasa USD: 480.00
- [ ] Ingresar Tasa EUR: 566.00
- [ ] Click "Guardar"
- [ ] Verificar redirect a index
- [ ] Verificar mensaje de éxito
- [ ] Verificar que aparece nueva tasa
- [ ] Verificar que la anterior PEN→VES se **desactivó automáticamente**

### 🧪 Test 3.4: Validación campos obligatorios
- [ ] Click "Nueva Tasa"
- [ ] Dejar campos vacíos
- [ ] Click "Guardar"
- [ ] Verificar errores de validación:
  - "El campo par de divisas es obligatorio"
  - "El campo tasa VES es obligatorio"
  - etc.

---

## 4. CONSOLA DE TASAS - Edit

**URL:** http://localhost/exchange_rates/{id}/edit

### 🧪 Test 4.1: Editar tasa existente
- [ ] En index, click "Editar" en tasa PEN→VES
- [ ] Verificar que carga formulario con datos actuales
- [ ] Cambiar Tasa VES: 174.50
- [ ] Cambiar Tasa USD: 481.00
- [ ] Click "Actualizar"
- [ ] Verificar redirect a index
- [ ] Verificar que tasa se actualizó

### 🧪 Test 4.2: Cambiar estado (activar/desactivar)
- [ ] Editar una tasa inactiva
- [ ] Marcar checkbox "Tasa Activa"
- [ ] Guardar
- [ ] Verificar que se activó
- [ ] Volver a editar
- [ ] Desmarcar checkbox
- [ ] Guardar
- [ ] Verificar que se desactivó

---

## 5. INTEGRACIÓN CON VENTAS

**URL:** http://localhost/sales/create

### 🧪 Test 5.1: Crear venta con snapshot de tasa
- [ ] Ir a "Nueva Venta"
- [ ] Seleccionar vendedor
- [ ] Ingresar monto en PEN: 100
- [ ] Verificar que calcula automáticamente VES: 17,371
- [ ] Guardar venta
- [ ] Ir a "Ver Venta"
- [ ] Verificar que guardó snapshot de tasa:
  - `exchange_rate_id` = ID de tasa activa
  - `exchange_rate_snapshot` = JSON con datos de la tasa

### 🧪 Test 5.2: Cambiar tasa y verificar snapshot
- [ ] Crear venta con tasa actual (173.71)
- [ ] Cambiar tasa PEN→VES a 175.00 (crear nueva)
- [ ] Crear otra venta
- [ ] Verificar que primera venta mantiene tasa 173.71 en snapshot
- [ ] Verificar que segunda venta usa tasa 175.00 en snapshot

---

## 6. SIMULADOR EN WELCOME - Integración con BD

### 🧪 Test 6.1: Simulador carga tasas desde BD
- [ ] Ir a página principal (sin auth)
- [ ] Abrir DevTools → Network
- [ ] Recargar página
- [ ] Verificar llamada a endpoint `/api/currency-pairs` o similar
- [ ] Verificar que responde con 3 pares
- [ ] Verificar JSON contiene tasas correctas

### 🧪 Test 6.2: Actualizar tasa y verificar simulador
- [ ] Cambiar tasa PEN→VES a 180.00 (consola)
- [ ] Volver a página principal
- [ ] Recargar (Ctrl+F5)
- [ ] Seleccionar Perú
- [ ] Ingresar 100 PEN
- [ ] Verificar que usa nueva tasa: **18,000 VES**

---

## 7. TESTS DE REGRESIÓN

### 🧪 Test 7.1: Otros módulos siguen funcionando
- [ ] Dashboard principal
- [ ] Gestión de vendedores
- [ ] Gestión de ventas
- [ ] Matriz de corredores
- [ ] Liquidaciones

### 🧪 Test 7.2: Comisiones siguen calculando correctamente
- [ ] Crear venta con 100 PEN
- [ ] Verificar comisión vendedor (5% sobre 100 PEN)
- [ ] Verificar comisión dueño (15% sobre 100 PEN)
- [ ] Total comisiones: 20 PEN (NO sobre VES)

---

## ✅ CRITERIOS DE ACEPTACIÓN

**El REQ 7.1-7.5 está completo cuando:**

1. ✅ Simulador dinámico funciona con 3 países
2. ✅ Consola de tasas muestra todas las tasas
3. ✅ Filtros funcionan correctamente
4. ✅ Create/Edit guardan y actualizan tasas
5. ✅ Solo una tasa activa por par
6. ✅ Ventas guardan snapshot correcto
7. ✅ Comisiones calculan sobre monto origen
8. ✅ No hay errores en consola del navegador
9. ✅ No hay errores en logs de Laravel

---

## 📊 RESULTADO DEL TESTING

**Tests ejecutados:** ___ / 40  
**Tests pasados:** ___ / ___  
**Tests fallados:** ___ / ___  
**Bugs encontrados:** ___

**Estado:** [ ] ✅ APROBADO | [ ] ❌ BLOQUEADO | [ ] ⚠️ PARCIAL

---

## 🐛 BUGS ENCONTRADOS

| # | Descripción | Severidad | Estado |
|---|-------------|-----------|--------|
| 1 | | [ ] Critical [ ] High [ ] Medium [ ] Low | [ ] Open [ ] Fixed |
| 2 | | [ ] Critical [ ] High [ ] Medium [ ] Low | [ ] Open [ ] Fixed |
| 3 | | [ ] Critical [ ] High [ ] Medium [ ] Low | [ ] Open [ ] Fixed |

---

**Tester:** Claude Sonnet 4.5  
**Fecha:** 2026-04-20  
**Tiempo total:** ___h ___m
