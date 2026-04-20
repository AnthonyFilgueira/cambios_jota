# Tarea 2.1: Crear modelo Transaction con migración

## Objetivo
Crear el modelo Eloquent `Transaction` con su migración para persistir las transacciones de los usuarios.

## Campos de la tabla

```sql
- id (bigint, primary key, auto_increment)
- user_id (bigint, foreign key → users.id)
- seller_id (bigint, nullable, foreign key → sellers.id)
- amount_pen (decimal 10,2) // Monto enviado en soles
- amount_ves (decimal 12,2) // Monto recibido en bolívares  
- exchange_rate_id (bigint, foreign key → exchange_rates.id)
- status (enum: 'pending', 'processing', 'completed', 'cancelled')
- notes (text, nullable)
- timestamps (created_at, updated_at)
```

## Comandos

```bash
./vendor/bin/sail artisan make:model Transaction -m
```

## Relaciones

- `belongsTo(User::class)`
- `belongsTo(Seller::class)` → nullable
- `belongsTo(ExchangeRate::class)`

## Duración
5 minutos

## Prioridad
Critical
