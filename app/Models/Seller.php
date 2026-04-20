<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'seller_commission',
        'boss_commission',
    ];

    protected $casts = [
        'seller_commission' => 'float',
        'boss_commission' => 'float',
    ];

    /**
     * Boot del modelo - Generar código único automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($seller) {
            if (empty($seller->code)) {
                $seller->code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generar código único alfanumérico
     * Formato: VEN-XXXXXX (VEN + 6 caracteres alfanuméricos)
     */
    private static function generateUniqueCode(): string
    {
        do {
            $randomPart = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6));
            $code = 'VEN-' . $randomPart;
        } while (self::where('code', $code)->exists());

        return $code;
    }

    // Relaciones
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Total vendido en un rango de fechas
    public function totalSalesBetween($start, $end)
    {
        return $this->sales()
            ->whereBetween('sale_date', [$start, $end])
            ->sum('amount');
    }

    // Comisión total del vendedor en un rango
    public function sellerCommissionTotal($start, $end)
    {
        return $this->sales()
            ->whereBetween('sale_date', [$start, $end])
            ->get()
            ->sum(fn($sale) => $sale->sellerCommissionAmount());
    }

    // Comisión total del jefe en un rango
    public function bossCommissionTotal($start, $end)
    {
        return $this->sales()
            ->whereBetween('sale_date', [$start, $end])
            ->get()
            ->sum(fn($sale) => $sale->bossCommissionAmount());
    }

    /**
     * Verificar si las comisiones pueden ser modificadas
     * (solo si no tiene ventas registradas)
     */
    public function commissionsCanBeModified(): bool
    {
        return !$this->sales()->exists();
    }
}