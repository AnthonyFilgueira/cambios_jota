<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'country',
        'country_id',
        'is_active',
        'flag_emoji',
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
    public function originCountry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function pairsAsOrigin()
    {
        return $this->hasMany(CurrencyPair::class, 'from_currency_id');
    }

    public function pairsAsDestination()
    {
        return $this->hasMany(CurrencyPair::class, 'to_currency_id');
    }

    // Métodos auxiliares
    public function getFullNameAttribute()
    {
        return "{$this->symbol} {$this->name} ({$this->code})";
    }

    public function getDisplayNameAttribute()
    {
        return "{$this->flag_emoji} {$this->symbol} {$this->code}";
    }
}
