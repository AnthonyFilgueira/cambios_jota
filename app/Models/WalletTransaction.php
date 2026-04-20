<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'type',
        'amount',
        'balance_after',
        'description',
        'reference_id',
        'reference_type',
    ];

    protected $casts = [
        'amount' => 'float',
        'balance_after' => 'float',
    ];

    // Relaciones
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Relación polimórfica a la referencia (Sale, Liquidation, etc.)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeCommissions($query)
    {
        return $query->where('type', 'commission');
    }

    public function scopeLiquidations($query)
    {
        return $query->where('type', 'liquidation');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('type', 'adjustment');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Verificar si la transacción es positiva (ingreso)
     */
    public function isCredit(): bool
    {
        return $this->amount > 0;
    }

    /**
     * Verificar si la transacción es negativa (egreso)
     */
    public function isDebit(): bool
    {
        return $this->amount < 0;
    }
}
