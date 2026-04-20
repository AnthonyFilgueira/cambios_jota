# Tarea 0.3: Auditar vistas Blade

## Objetivo
Identificar todas las vistas Blade existentes y determinar qué interfaces están implementadas.

## Descripción
Listar todos los archivos `.blade.php` en `resources/views/` y analizar su estructura para entender qué pantallas están completas.

## Comandos a ejecutar
```bash
find resources/views -name "*.blade.php" -type f
```

## Análisis esperado
Para cada vista:
- Identificar a qué módulo/REQ pertenece
- Verificar si usa Alpine.js
- Verificar si usa la paleta de colores Cambio J

## Salida esperada
- Listado completo de vistas organizadas por módulo
- Identificación de vistas que usan Alpine.js
- Mapeo de vistas a REQ del plan

## Duración estimada
2 minutos

## Prioridad
High
