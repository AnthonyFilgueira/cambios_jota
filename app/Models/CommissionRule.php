<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRule extends Model
{
    protected $fillable = [
        'seller_id',
        'commission_type',
        'seller_value',
        'boss_value',
        'notes',
        'applied_by',
    ];

    protected $casts = [
        'seller_value' => 'decimal:2',
        'boss_value'   => 'decimal:2',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function appliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function typeLabel(): string
    {
        return $this->commission_type === 'fixed' ? 'Monto fijo' : 'Porcentaje';
    }

    public function sellerSymbol(): string
    {
        return $this->commission_type === 'fixed' ? 'S/' : '%';
    }
}
