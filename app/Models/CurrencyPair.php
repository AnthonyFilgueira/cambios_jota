<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurrencyPair extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_currency_id',
        'to_currency_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relaciones
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }

    public function corridors()
    {
        return $this->belongsToMany(Corridor::class, 'corridor_pair')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    // Métodos auxiliares
    public function getDisplayNameAttribute()
    {
        return "{$this->fromCurrency->code} → {$this->toCurrency->code}";
    }

    public function getFullDisplayAttribute()
    {
        return "{$this->fromCurrency->flag_emoji} {$this->fromCurrency->code} → {$this->toCurrency->flag_emoji} {$this->toCurrency->code}";
    }

    /**
     * Verificar si el par tiene al menos un corredor habilitado
     */
    public function hasEnabledCorridors()
    {
        return $this->corridors()
            ->wherePivot('is_enabled', true)
            ->exists();
    }

    /**
     * Obtener corredores habilitados para este par
     */
    public function getEnabledCorridors()
    {
        return $this->corridors()
            ->wherePivot('is_enabled', true)
            ->get();
    }
}
