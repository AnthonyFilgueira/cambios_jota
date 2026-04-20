<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'amount',
        'sale_date',
        'approval_status',
    ];

    protected $casts = [
        'amount' => 'float',
        'sale_date' => 'date',
    ];

    // Relaciones
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // Cálculo de comisión del vendedor
    public function sellerCommissionAmount()
    {
        return $this->amount * ($this->seller->seller_commission / 100);
    }

    // Cálculo de comisión del jefe
    public function bossCommissionAmount()
    {
        return $this->amount * ($this->seller->boss_commission / 100);
    }
}
