# Tarea 0.4: Auditar rutas

## Objetivo
Listar todas las rutas HTTP registradas en la aplicación para identificar qué endpoints están activos.

## Descripción
Ejecutar `php artisan route:list` para obtener el listado completo de rutas y analizar qué funcionalidades están accesibles vía web.

## Comandos a ejecutar
```bash
./vendor/bin/sail artisan route:list --columns=method,uri,name,action
```

## Análisis esperado
- Identificar rutas por módulo (auth, exchange_rates, sales, sellers, etc.)
- Verificar middlewares aplicados (auth, guest)
- Contar rutas por REQ

## Salida esperada
- Listado organizado de rutas por módulo
- Identificación de endpoints protegidos vs. públicos
- Mapeo de rutas activas a REQ

## Duración estimada
1 minuto

## Prioridad
High
