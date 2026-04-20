<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Liquidation extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'amount',
        'payment_method',
        'reference',
        'notes',
        'payment_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'payment_date' => 'date',
    ];

    // Relaciones
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Obtener la transacción de monedero asociada
     */
    public function walletTransaction()
    {
        return WalletTransaction::where('reference_type', self::class)
            ->where('reference_id', $this->id)
            ->first();
    }

    // Scopes
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('payment_date', '>=', now()->subDays($days));
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('payment_date', [$start, $end]);
    }

    /**
     * Métodos de pago disponibles
     */
    public static function paymentMethods()
    {
        return [
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia Bancaria',
            'yape' => 'Yape',
            'plin' => 'Plin',
            'otro' => 'Otro',
        ];
    }

    /**
     * Obtener nombre legible del método de pago
     */
    public function paymentMethodLabel()
    {
        return self::paymentMethods()[$this->payment_method] ?? $this->payment_method;
    }
}
