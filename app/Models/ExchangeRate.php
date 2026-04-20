<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        // Sistema nuevo (REQ 7)
        'currency_pair_id',
        'base_rate',
        'margin_type',
        'margin_value',
        'final_rate',
        'last_updated_by',
        'notes',

        // Sistema legacy (compatibilidad)
        'usd_rate',
        'eur_rate',
        'ves_rate',
        'is_active',
    ];

    protected $casts = [
        // Sistema nuevo
        'base_rate' => 'float',
        'margin_value' => 'float',
        'final_rate' => 'float',

        // Sistema legacy
        'usd_rate' => 'float',
        'eur_rate' => 'float',
        'ves_rate' => 'float',
        'is_active' => 'boolean',
    ];

    // =====================================
    // RELACIONES (REQ 6 + REQ 7)
    // =====================================

    /**
     * Relación con par de divisas
     */
    public function currencyPair()
    {
        return $this->belongsTo(CurrencyPair::class);
    }

    /**
     * Usuario que actualizó por última vez
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Transacciones que usan esta tasa
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Solo tasas activas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Tasas por par específico
     */
    public function scopeForPair($query, $currencyPairId)
    {
        return $query->where('currency_pair_id', $currencyPairId);
    }

    // =====================================
    // MÉTODOS DE OBTENCIÓN
    // =====================================

    /**
     * Obtener la tasa activa actual (compatible con legacy)
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first() ?? self::getDefault();
    }

    /**
     * Obtener tasa activa para un par específico
     */
    public static function getActiveForPair($currencyPairId)
    {
        return self::active()
            ->forPair($currencyPairId)
            ->first();
    }

    /**
     * Obtener tasas por defecto si no hay ninguna en BD (legacy)
     */
    public static function getDefault()
    {
        return (object) [
            'usd_rate' => 479.77750,
            'eur_rate' => 565.98392,
            'ves_rate' => 173.71000,
            'base_rate' => 0.1200,
            'final_rate' => 0.1236,
        ];
    }

    // =====================================
    // MÉTODOS DE CÁLCULO (REQ 7)
    // =====================================

    /**
     * Calcular tasa final automáticamente según margen
     */
    public function calculateFinalRate()
    {
        if (!$this->base_rate) {
            return 0;
        }

        switch ($this->margin_type) {
            case 'percentage':
                return $this->base_rate * (1 + ($this->margin_value / 100));

            case 'fixed':
                return $this->base_rate + $this->margin_value;

            case 'none':
            default:
                return $this->base_rate;
        }
    }

    /**
     * Calcular ganancia por operación
     *
     * @param float $amount Monto en divisa origen
     * @return array ['base_amount' => X, 'final_amount' => Y, 'profit' => Z]
     */
    public function calculateProfit($amount)
    {
        $baseAmount = $amount * $this->base_rate;
        $finalAmount = $amount * $this->final_rate;
        $profit = $finalAmount - $baseAmount;

        return [
            'base_amount' => round($baseAmount, 2),
            'final_amount' => round($finalAmount, 2),
            'profit' => round($profit, 2),
            'profit_percentage' => $this->margin_value,
        ];
    }

    /**
     * Recalcular y guardar tasa final
     */
    public function recalculateFinalRate()
    {
        $this->final_rate = $this->calculateFinalRate();
        $this->save();
    }

    // =====================================
    // MÉTODOS DE GESTIÓN
    // =====================================

    /**
     * Activar esta tasa y desactivar las demás del mismo par
     */
    public function activate()
    {
        // Si tiene par, desactivar otras del mismo par
        if ($this->currency_pair_id) {
            self::where('currency_pair_id', $this->currency_pair_id)
                ->where('id', '!=', $this->id)
                ->update(['is_active' => false]);
        } else {
            // Legacy: desactivar todas las demás
            self::where('id', '!=', $this->id)->update(['is_active' => false]);
        }

        $this->update(['is_active' => true]);
    }

    /**
     * Verificar si la tasa puede ser modificada/eliminada
     */
    public function canBeModified(): bool
    {
        return !$this->transactions()->exists();
    }

    // =====================================
    // ATRIBUTOS COMPUTADOS
    // =====================================

    /**
     * Nombre del par (ej: "PEN → VES")
     */
    public function getPairNameAttribute()
    {
        if ($this->currencyPair) {
            return $this->currencyPair->display_name;
        }
        return 'Sistema Legacy';
    }

    /**
     * Margen formateado (ej: "3.00%")
     */
    public function getMarginDisplayAttribute()
    {
        if ($this->margin_type === 'percentage') {
            return number_format($this->margin_value, 2) . '%';
        } elseif ($this->margin_type === 'fixed') {
            return '+' . number_format($this->margin_value, 2);
        }
        return 'Sin margen';
    }

    // =====================================
    // EVENTOS DE MODELO
    // =====================================

    /**
     * Eventos del modelo
     */
    protected static function booted()
    {
        // Auto-calcular final_rate antes de guardar
        static::saving(function ($rate) {
            if ($rate->base_rate && $rate->margin_type && !$rate->final_rate) {
                $rate->final_rate = $rate->calculateFinalRate();
            }
        });
    }
}
