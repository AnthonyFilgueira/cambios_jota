# Implementación 6.3: Modelo de Corredores

**Fecha:** 2026-04-20  
**Estado:** ✅ COMPLETADO  
**Tiempo real:** ~2h  
**Tiempo estimado:** 6h

---

## Resumen

Implementación completa del modelo de corredores de envío (empresas que procesan conversiones de divisas) con CRUD básico, migración, seeder y vistas Blade siguiendo el patrón Cambio J.

---

## Archivos Creados

### 1. Migración
**Archivo:** `database/migrations/2026_04_20_144902_create_corridors_table.php`

**Estructura de tabla:**
```php
Schema::create('corridors', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->text('description')->nullable();
    $table->string('logo_url')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

**Campos:**
- `id`: PK autoincremental
- `name`: Nombre del corredor (único)
- `description`: Descripción del servicio (opcional)
- `logo_url`: URL del logo (opcional)
- `is_active`: Estado activo/inactivo (default: true)
- `created_at`, `updated_at`: Timestamps automáticos

---

### 2. Modelo
**Archivo:** `app/Models/Corridor.php`

**Características:**

**Fillable:**
```php
protected $fillable = [
    'name',
    'description',
    'logo_url',
    'is_active',
];
```

**Casts:**
```php
protected $casts = [
    'is_active' => 'boolean',
];
```

**Scopes:**
- `active()`: Filtra corredores activos
- `inactive()`: Filtra corredores inactivos

**Métodos:**
- `activate()`: Activa el corredor
- `deactivate()`: Desactiva el corredor
- `toggleStatus()`: Alterna el estado

**Relaciones (pendiente tarea 6.4):**
- `currencyPairs()`: Relación many-to-many con pares de divisas

---

### 3. Seeder
**Archivo:** `database/seeders/CorridorSeeder.php`

**Corredores iniciales (5):**

1. **Western Union**
   - Descripción: "Líder mundial en transferencias de dinero y pagos internacionales."
   - Estado: Activo

2. **MoneyGram**
   - Descripción: "Servicio global de transferencias de dinero rápidas y seguras."
   - Estado: Activo

3. **Remitly**
   - Descripción: "Plataforma digital de remesas internacionales con tarifas competitivas."
   - Estado: Activo

4. **Wise**
   - Descripción: "Transferencias internacionales con tipo de cambio real y tarifas bajas."
   - Estado: Activo

5. **Cambio J Directo**
   - Descripción: "Servicio directo de cambio de divisas Cambio J con las mejores tasas."
   - Estado: Activo

---

### 4. Controller
**Archivo:** `app/Http/Controllers/CorridorController.php`

**Métodos implementados:**

#### `index()`
- Lista todos los corredores ordenados por nombre
- Retorna vista `corridors.index`

#### `create()`
- Muestra formulario de creación
- Retorna vista `corridors.create`

#### `store(Request $request)`
- Validaciones:
  - `name`: requerido, string, max:255, único
  - `description`: nullable, string, max:1000
  - `logo_url`: nullable, url, max:255
  - `is_active`: checkbox (boolean)
- Crea nuevo corredor
- Redirecciona a index con mensaje de éxito

#### `edit(Corridor $corridor)`
- Muestra formulario de edición
- Retorna vista `corridors.edit`

#### `update(Request $request, Corridor $corridor)`
- Validaciones iguales a store, excepto name (ignora ID actual)
- Actualiza corredor
- Redirecciona a index con mensaje de éxito

#### `destroy(Corridor $corridor)`
- **NO ELIMINA**, solo desactiva
- Usa método `deactivate()`
- Redirecciona con mensaje de éxito

#### `toggleStatus(Corridor $corridor)`
- Alterna estado activo/inactivo
- Usa método `toggleStatus()`
- Redirecciona back con mensaje dinámico

---

### 5. Rutas
**Archivo:** `routes/web.php`

**Rutas agregadas:**
```php
use App\Http\Controllers\CorridorController;

Route::resource('corridors', CorridorController::class)->except(['show']);
Route::patch('corridors/{corridor}/toggle-status', [CorridorController::class, 'toggleStatus'])
    ->name('corridors.toggleStatus');
```

**Rutas generadas (7 rutas):**
- `GET /corridors` - corridors.index
- `GET /corridors/create` - corridors.create
- `POST /corridors` - corridors.store
- `GET /corridors/{corridor}/edit` - corridors.edit
- `PUT/PATCH /corridors/{corridor}` - corridors.update
- `DELETE /corridors/{corridor}` - corridors.destroy
- `PATCH /corridors/{corridor}/toggle-status` - corridors.toggleStatus

---

### 6. Vistas

#### `resources/views/corridors/index.blade.php`

**Características:**
- Layout: `x-app-layout` (Breeze)
- Header con título "🏦 Gestión de Corredores"
- Botón "+ Nuevo Corredor" con gradiente morado
- Tabla responsive con columnas:
  - Nombre (bold, morado)
  - Descripción (gris, muestra "Sin descripción" si null)
  - Estado (badge verde/gris con toggle)
  - Acciones (Editar)
- Footer con contador total y activos
- Mensajes de éxito/error
- Estado vacío con link a crear

**Diseño:**
- Header tabla: gradiente morado (`from-cj-morado-profundo to-cj-morado-medio`)
- Hover en filas: `hover:bg-gray-50`
- Badge estado clickeable (toggle inline)

#### `resources/views/corridors/create.blade.php`

**Características:**
- Layout: `x-app-layout`
- Header: "🏦 Nuevo Corredor"
- Formulario centrado (max-w-2xl)
- 4 campos:
  1. **Nombre** (requerido)
     - Input text
     - Ejemplo: "Western Union, MoneyGram, Remitly"
  2. **Descripción** (opcional)
     - Textarea (3 filas)
     - Max 1000 caracteres
  3. **URL del Logo** (opcional)
     - Input URL
     - Placeholder con ejemplo
  4. **Corredor activo** (checkbox)
     - Checked por defecto
- Botones:
  - "Crear Corredor" (gradiente morado)
  - "Cancelar" (gris)
- Validaciones inline (mensajes de error en rojo)

#### `resources/views/corridors/edit.blade.php`

**Características:**
- Idéntico a create, pero:
  - Header: "🏦 Editar Corredor: {nombre}"
  - Campos pre-llenados con `old()` o datos del corredor
  - Método PUT (`@method('PUT')`)
  - Botón: "Actualizar Corredor"
  - Acción: `route('corridors.update', $corridor)`

**Diseño consistente:**
- Focus: border morado + ring morado claro
- Errores: border rojo + mensaje debajo
- Textos de ayuda: gris pequeño

---

## Comandos Ejecutados

### 1. Crear migración
```bash
./vendor/bin/sail artisan make:migration create_corridors_table
```

### 2. Crear modelo
```bash
./vendor/bin/sail artisan make:model Corridor
```

### 3. Crear seeder
```bash
./vendor/bin/sail artisan make:seeder CorridorSeeder
```

### 4. Crear controller
```bash
./vendor/bin/sail artisan make:controller CorridorController
```

### 5. Ejecutar migración
```bash
./vendor/bin/sail artisan migrate
```
**Resultado:**
- `2026_04_20_143438_create_currencies_table` ✅
- `2026_04_20_144129_create_currency_pairs_table` ✅
- `2026_04_20_144902_create_corridors_table` ✅

### 6. Ejecutar seeder
```bash
./vendor/bin/sail artisan db:seed --class=CorridorSeeder
```
**Resultado:** 5 corredores insertados

### 7. Verificar rutas
```bash
./vendor/bin/sail artisan route:list --path=corridors
```
**Resultado:** 7 rutas generadas

---

## Decisiones Técnicas

### 1. No eliminar, solo desactivar
**Razón:** Evitar inconsistencias si el corredor tiene relaciones (pares, ventas futuras)  
**Implementación:** Método `destroy()` llama a `deactivate()`  
**Beneficio:** Historial preservado, se puede reactivar

### 2. Nombre único
**Razón:** No tiene sentido tener 2 "Western Union"  
**Implementación:** Unique constraint en migración + validación en controller  
**Resultado:** Previene duplicados

### 3. Logo URL opcional
**Razón:** No todos los corredores tienen logo al inicio  
**Implementación:** Campo nullable, validación URL  
**Futuro:** Migrar a subida de archivos (storage)

### 4. Toggle de estado inline
**Razón:** Cambio rápido sin ir a editar  
**Implementación:** Formulario dentro de cada fila + método PATCH  
**UX:** Badge clickeable, cambio inmediato

### 5. Descripción max 1000 caracteres
**Razón:** Breve descripción, no ensayo  
**Implementación:** Validación `max:1000` + textarea de 3 filas  
**UX:** Suficiente para pitch del servicio

---

## Validaciones Implementadas

### Store y Update
```php
[
    'name' => 'required|string|max:255|unique:corridors,name',
    'description' => 'nullable|string|max:1000',
    'logo_url' => 'nullable|url|max:255',
]
```

**En update:**
- `name` unique ignora el ID actual: `unique:corridors,name,{id}`

**Checkbox is_active:**
- `$request->has('is_active')` → true/false
- Unchecked = false (no viene en request)

---

## Patrón de Diseño Seguido

### Colores Cambio J
- **Morado profundo:** `#5B21B6` (backgrounds principales)
- **Morado medio:** `#7C3AED` (gradientes, hovers)
- **Morado claro:** `#DDD6FE` (focus rings)

### Componentes
- Gradientes en headers de tabla
- Badges redondeados (rounded-full)
- Formularios con focus states
- Botones con hover opacity
- Mensajes flash con borders

### Consistencia
- Mismo patrón que `currencies` (REQ 6.1)
- Layout Breeze (`x-app-layout`)
- Alpine.js NO usado (formularios puros)
- Tailwind utility-first

---

## Testing Manual

### ✅ Verificaciones realizadas
1. ✅ Migración ejecutada sin errores
2. ✅ Seeder insertó 5 corredores
3. ✅ 7 rutas generadas correctamente
4. ✅ Controller tiene 7 métodos
5. ✅ Modelo tiene scopes y métodos
6. ✅ 3 vistas creadas (index, create, edit)

### 🧪 Testing pendiente (manual en navegador)
- [ ] Ver lista de corredores (/corridors)
- [ ] Crear nuevo corredor
- [ ] Editar corredor existente
- [ ] Toggle de estado (activar/desactivar)
- [ ] Validaciones (nombre único, URL válida)
- [ ] Mensajes flash de éxito/error

---

## Próximos Pasos

### REQ 6.4: Tabla Pivot Par-Corredor (5h)
- Migración `corridor_pair` (pivot)
- Relación many-to-many en modelos
- Métodos para habilitar/deshabilitar corredores por par
- Seeders para relaciones iniciales

**Estructura esperada:**
```php
Schema::create('corridor_pair', function (Blueprint $table) {
    $table->id();
    $table->foreignId('corridor_id')->constrained()->onDelete('cascade');
    $table->foreignId('currency_pair_id')->constrained()->onDelete('cascade');
    $table->boolean('is_enabled')->default(true);
    $table->timestamps();
    
    $table->unique(['corridor_id', 'currency_pair_id']);
});
```

**Relaciones a agregar:**
- `Corridor::currencyPairs()`
- `CurrencyPair::corridors()`

---

## Métricas de Implementación

| Métrica | Valor |
|---------|-------|
| Archivos creados | 8 |
| Archivos modificados | 1 (web.php) |
| Líneas de código | ~600 |
| Migraciones | 1 |
| Modelos | 1 |
| Controllers | 1 |
| Seeders | 1 |
| Vistas Blade | 3 |
| Rutas | 7 |
| Tiempo real | ~2h |
| Tiempo estimado | 6h |
| Eficiencia | 300% |

---

## Notas de Sesión

### Velocidad de implementación
- Patrón ya establecido en REQ 6.1 (currencies)
- Reutilización de estructura de vistas
- Copy-paste inteligente + adaptación

### Diferencias con currencies
- Sin emoji/bandera
- Campo description (textarea vs inputs simples)
- Logo URL (vs flag emoji)
- Menos campos (4 vs 5)

### Aprendizajes
- Consistencia visual es clave
- Validación URL nativa de Laravel (`url` rule)
- Toggle inline mejora UX vs modal

---

**Implementado por:** Claude Sonnet 4.5  
**Fecha:** 2026-04-20 15:00  
**Commit pendiente:** REQ 6.3
