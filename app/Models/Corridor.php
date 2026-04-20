<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Corridor extends Model
{
    protected $fillable = [
        'name',
        'description',
        'logo_url',
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

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Relaciones
    public function currencyPairs()
    {
        return $this->belongsToMany(CurrencyPair::class, 'corridor_currency_pair')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    // Obtener solo pares habilitados para este corredor
    public function enabledCurrencyPairs()
    {
        return $this->currencyPairs()->wherePivot('is_enabled', true);
    }

    // Métodos de utilidad
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function toggleStatus()
    {
        $this->update(['is_active' => !$this->is_active]);
    }
}
