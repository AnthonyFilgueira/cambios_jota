<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncentiveRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'target_type',
        'target_id',
        'value_type',
        'value',
        'min_amount',
        'min_transactions',
        'starts_at',
        'ends_at',
        'active',
        'created_by',
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'min_amount'       => 'decimal:2',
        'min_transactions' => 'integer',
        'active'           => 'boolean',
        'starts_at'        => 'date',
        'ends_at'          => 'date',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'bono_volumen'       => 'Bono por Volumen',
            'bono_estrella'      => 'Bono Estrella',
            'descuento_cliente'  => 'Descuento Cliente',
            'bono_nuevo_cliente' => 'Bono Nuevo Cliente',
            default              => $this->type,
        };
    }

    public function valueLabel(): string
    {
        return $this->value_type === 'fixed'
            ? 'S/ ' . number_format($this->value, 2)
            : number_format($this->value, 2) . '%';
    }

    public function targetLabel(): string
    {
        return match ($this->target_type) {
            'seller' => 'Vendedor específico',
            'client' => 'Cliente específico',
            'all'    => 'Todos',
            default  => $this->target_type,
        };
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->active) return false;
        $today = now()->toDateString();
        if ($this->starts_at->toDateString() > $today) return false;
        if ($this->ends_at && $this->ends_at->toDateString() < $today) return false;
        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where('starts_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }
}
