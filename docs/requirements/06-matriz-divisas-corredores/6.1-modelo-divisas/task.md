# Tarea 6.1: Modelo y Migración de Divisas

**Horas estimadas:** 6h  
**Estado:** ✅ COMPLETADO

## Objetivo

Crear el modelo Currency con su migración, seeder inicial y CRUD completo para gestionar el catálogo de divisas del sistema.

## Implementado

### 1. Migración `create_currencies_table`
- Código ISO (3 letras, unique)
- Nombre completo
- Símbolo  
- País
- Estado activo/inactivo
- Emoji de bandera (opcional)

### 2. Modelo `Currency`
- Fillable y casts
- Scope `active()`
- Relaciones con CurrencyPair
- Atributos computed: `full_name`, `display_name`

### 3. Seeder `CurrencySeeder`
- 8 divisas iniciales: PEN, VES, USD, EUR, COP, ARS, CLP, BRL
- Con banderas emoji

### 4. Controlador `CurrencyController`
- CRUD completo
- Toggle status (activar/desactivar)
- Validaciones
- Protección contra eliminación si está en uso

### 5. Vistas Blade
- `index`: Lista con badges de estado
- `create`: Formulario con validaciones
- `edit`: Formulario prellenado

### 6. Rutas
- Resource routes (index, create, store, edit, update)
- Ruta adicional: toggleStatus

## Resultado

✅ Sistema completo de gestión de divisas funcionando
