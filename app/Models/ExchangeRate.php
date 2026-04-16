<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $fillable = [
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

    /**
     * Obtener la tasa activa actual
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first() ?? self::getDefault();
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

    /**
     * Activar esta tasa y desactivar las demás
     */
    public function activate()
    {
        self::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }
}
