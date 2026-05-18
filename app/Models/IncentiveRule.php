<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IncentiveRule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'type', 'target_type', 'target_id',
        'value_type', 'value', 'min_amount', 'min_transactions',
        'starts_at', 'ends_at', 'active', 'created_by',
        'max_uses', 'uses_count', 'condition_new_client',
    ];

    protected $casts = [
        'value'               => 'decimal:2',
        'min_amount'          => 'decimal:2',
        'min_transactions'    => 'integer',
        'active'              => 'boolean',
        'condition_new_client'=> 'boolean',
        'max_uses'            => 'integer',
        'uses_count'          => 'integer',
        'starts_at'           => 'date',
        'ends_at'             => 'date',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function appliedTransactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'transaction_incentive_rules')
            ->withPivot('bonus_amount', 'benefit_type', 'applied_at');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'extra_receptor' => 'Extra al Receptor',
            'extra_comision' => 'Extra Comisión Vendedor',
            'descuento_tasa' => 'Descuento de Tasa',
            default          => $this->type,
        };
    }

    public function typeIcon(): string
    {
        return match ($this->type) {
            'extra_receptor' => '🎁',
            'extra_comision' => '⭐',
            'descuento_tasa' => '📉',
            default          => '💡',
        };
    }

    public function targetLabel(): string
    {
        return match ($this->target_type) {
            'todos_clientes'       => 'Todos los clientes',
            'cliente_nuevo'        => 'Clientes nuevos (0 envíos)',
            'cliente_especifico'   => 'Cliente específico',
            'todos_vendedores'     => 'Todos los vendedores',
            'vendedor_especifico'  => 'Vendedor específico',
            'clientes_de_vendedor' => 'Clientes de un vendedor',
            default                => $this->target_type,
        };
    }

    public function valueLabel(): string
    {
        return $this->value_type === 'fixed'
            ? 'S/ ' . number_format($this->value, 2)
            : number_format($this->value, 2) . '%';
    }

    public function usesLabel(): string
    {
        if ($this->max_uses === null) return $this->uses_count . ' usos (ilimitado)';
        return $this->uses_count . ' / ' . $this->max_uses . ' usos';
    }

    public function isCurrentlyActive(): bool
    {
        if (!$this->active) return false;
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) return false;
        $today = now()->toDateString();
        if ($this->starts_at->toDateString() > $today) return false;
        if ($this->ends_at && $this->ends_at->toDateString() < $today) return false;
        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where('starts_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->where(fn ($q) => $q->whereNull('max_uses')->orWhereColumn('uses_count', '<', 'max_uses'));
    }
}
