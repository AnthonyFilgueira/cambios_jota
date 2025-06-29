<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'seller_commission',
        'boss_commission',
    ];

    protected $casts = [
        'seller_commission' => 'float',
        'boss_commission' => 'float',
    ];

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
}