<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_pair_id',
        'usd_rate',
        'eur_rate',
        'ves_rate',
        'is_active',
    ];

    protected $casts = [
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
     * Transacciones que usan esta tasa
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Historial de cambios de esta tasa
     */
    public function history()
    {
        return $this->hasMany(ExchangeRateHistory::class)->orderBy('created_at', 'desc');
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
     * Obtener tasas por defecto si no hay ninguna en BD
     */
    public static function getDefault()
    {
        return (object) [
            'usd_rate' => 479.77750,
            'eur_rate' => 565.98392,
            'ves_rate' => 173.71000,
        ];
    }

    // =====================================
    // MÉTODOS DE CÁLCULO
    // =====================================

    /**
     * Calcular cuánto recibe el cliente en VES
     *
     * @param float $amount Monto en divisa origen
     * @return float Monto en VES
     */
    public function calculateVesAmount($amount)
    {
        return $amount * $this->ves_rate;
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
        return 'Tasas de Referencia';
    }

}
