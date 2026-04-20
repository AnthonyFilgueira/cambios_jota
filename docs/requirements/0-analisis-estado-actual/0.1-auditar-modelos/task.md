# Tarea 0.1: Auditar modelos y migraciones

## Objetivo
Identificar todos los modelos Eloquent existentes en el proyecto y verificar el estado de las migraciones ejecutadas.

## Descripción
Listar todos los archivos en `app/Models/` y ejecutar `php artisan migrate:status` para conocer qué migraciones están ejecutadas y cuáles están pendientes.

## Comandos a ejecutar
```bash
ls -la app/Models/
./vendor/bin/sail artisan migrate:status
```

## Salida esperada
- Listado completo de modelos (User, ExchangeRate, Transaction, etc.)
- Estado de cada migración (Ran/Pending)
- Identificación de qué funcionalidades están implementadas a nivel de base de datos

## Duración estimada
2 minutos

## Prioridad
Critical

## Siguiente paso
Tarea 0.2: Auditar controladores
