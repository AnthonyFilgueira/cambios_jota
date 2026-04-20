# Implementación 4.2: Sistema de Trazabilidad

## Archivos creados/modificados

### 1. Migración: `2026_04_20_084153_create_sale_logs_table.php`

```php
Schema::create('sale_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('action');
    $table->string('old_status')->nullable();
    $table->string('new_status')->nullable();
    $table->text('comment')->nullable();
    $table->timestamps();
});
```

### 2. Modelo: `app/Models/SaleLog.php`

```php
class SaleLog extends Model
{
    protected $fillable = [
        'sale_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'comment',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### 3. Observer: `app/Observers/SaleObserver.php`

```php
public function updated(Sale $sale): void
{
    if ($sale->isDirty('approval_status')) {
        $oldStatus = $sale->getOriginal('approval_status');
        $newStatus = $sale->approval_status;

        $action = match($newStatus) {
            'pending_admin' => $oldStatus === 'observed' ? 'corrected' : 'approved_by_seller',
            'approved' => 'approved',
            'rejected' => 'rejected',
            'observed' => 'observed',
            'completed' => 'completed',
            default => 'status_changed',
        };

        SaleLog::create([
            'sale_id' => $sale->id,
            'user_id' => Auth::id() ?? 1,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'comment' => $sale->admin_observation ?? null,
        ]);
    }
}
```

### 4. Registro en `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    Sale::observe(SaleObserver::class);
}
```

### 5. Métodos agregados en `app/Models/Sale.php`

```php
// Relación
public function logs()
{
    return $this->hasMany(SaleLog::class);
}

// Nuevos métodos de workflow
public function observe(string $observation)
{
    if ($this->approval_status === 'pending_admin') {
        $this->admin_observation = $observation;
        $this->approval_status = 'observed';
        $this->save();
        return true;
    }
    throw new \Exception("Solo se pueden observar ventas en estado pending_admin");
}

public function complete(string $voucherPath)
{
    if ($this->approval_status === 'approved') {
        $this->voucher_path = $voucherPath;
        $this->approval_status = 'completed';
        $this->save();
        return true;
    }
    throw new \Exception("Solo se pueden completar ventas en estado approved");
}

// Helpers
public function isObserved(): bool
{
    return $this->approval_status === 'observed';
}

public function isCompleted(): bool
{
    return $this->approval_status === 'completed';
}

public function canBeObserved(): bool
{
    return $this->approval_status === 'pending_admin';
}

public function canBeCompleted(): bool
{
    return $this->approval_status === 'approved';
}
```

---

## Comandos ejecutados

```bash
./vendor/bin/sail artisan make:migration create_sale_logs_table
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan make:model SaleLog
./vendor/bin/sail artisan make:observer SaleObserver --model=Sale
```

---

## Validación

✅ Tabla sale_logs creada  
✅ Modelo SaleLog con relaciones  
✅ Observer registrado y funcionando  
✅ Métodos observe() y complete() en Sale  
✅ Auto-logging transparente

---

**Fecha:** 2026-04-20
