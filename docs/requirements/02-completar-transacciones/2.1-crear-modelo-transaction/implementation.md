# Implementación: Modelo Transaction

## Fecha
2026-04-20

## Archivos creados

1. `app/Models/Transaction.php`
2. `database/migrations/2026_04_20_070932_create_transactions_table.php`

## Migración ejecutada

```bash
./vendor/bin/sail artisan migrate
```

**Resultado:** ✅ Migración ejecutada correctamente (592.94ms)

## Estructura de la tabla

```sql
CREATE TABLE transactions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    seller_id BIGINT NULL,
    amount_pen DECIMAL(10,2) NOT NULL,
    amount_ves DECIMAL(12,2) NOT NULL,
    exchange_rate_id BIGINT NOT NULL,
    status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE SET NULL,
    FOREIGN KEY (exchange_rate_id) REFERENCES exchange_rates(id) ON DELETE RESTRICT
);
```

## Relaciones implementadas

### Transaction → User
```php
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

### Transaction → Seller (nullable)
```php
public function seller(): BelongsTo
{
    return $this->belongsTo(Seller::class);
}
```

### Transaction → ExchangeRate
```php
public function exchangeRate(): BelongsTo
{
    return $this->belongsTo(ExchangeRate::class);
}
```

## Campos fillable

- `user_id`
- `seller_id`
- `amount_pen`
- `amount_ves`
- `exchange_rate_id`
- `status`
- `notes`

## Casts

- `amount_pen` → decimal:2
- `amount_ves` → decimal:2

## Validación

✅ Migración ejecutada sin errores  
✅ Tabla creada con todos los campos  
✅ Foreign keys configuradas correctamente  
✅ Modelo con relaciones definidas

## Próximo paso

Tarea 2.2: Crear TransactionController
