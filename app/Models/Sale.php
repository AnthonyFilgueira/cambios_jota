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
        'voucher_path',
        'admin_observation',

        // Snapshots de comisiones
        'seller_commission_percent',
        'admin_commission_percent',
        'seller_commission_amount',
        'admin_commission_amount',

        // Snapshots de tasas (para trazabilidad)
        'usd_rate_snapshot',
        'eur_rate_snapshot',
        'ves_rate_snapshot',
    ];

    protected $casts = [
        'amount' => 'float',
        'sale_date' => 'date',

        // Snapshots de comisiones
        'seller_commission_percent' => 'float',
        'admin_commission_percent' => 'float',
        'seller_commission_amount' => 'float',
        'admin_commission_amount' => 'float',

        // Snapshots de tasas
        'usd_rate_snapshot' => 'float',
        'eur_rate_snapshot' => 'float',
        'ves_rate_snapshot' => 'float',
    ];

    // Relaciones
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function logs()
    {
        return $this->hasMany(SaleLog::class);
    }

    // Cálculo de comisión del vendedor
    public function sellerCommissionAmount()
    {
        // Usar snapshot si existe, sino calcular (fallback para ventas antiguas)
        return $this->seller_commission_amount ??
               $this->amount * ($this->seller->seller_commission / 100);
    }

    // Cálculo de comisión del jefe
    public function bossCommissionAmount()
    {
        // Usar snapshot si existe, sino calcular (fallback para ventas antiguas)
        return $this->admin_commission_amount ??
               $this->amount * ($this->seller->boss_commission / 100);
    }

    // Workflow de aprobación
    public function approve()
    {
        if ($this->approval_status === 'pending_seller') {
            $this->approval_status = 'pending_admin';
            $this->save();
            return true;
        }

        if ($this->approval_status === 'pending_admin') {
            $this->approval_status = 'approved';
            $this->save();
            return true;
        }

        throw new \Exception("No se puede aprobar una venta con estado: {$this->approval_status}");
    }

    public function reject()
    {
        if (in_array($this->approval_status, ['pending_seller', 'pending_admin'])) {
            $this->approval_status = 'rejected';
            $this->save();
            return true;
        }

        throw new \Exception("No se puede rechazar una venta con estado: {$this->approval_status}");
    }

    public function markAsObserved(string $observation)
    {
        if ($this->approval_status === 'pending_admin') {
            $this->admin_observation = $observation;
            $this->approval_status = 'observed';
            $this->save();
            return true;
        }

        throw new \Exception("Solo se pueden observar ventas en estado pending_admin");
    }

    public function complete(string $voucherPath)
    {
        if ($this->approval_status === 'approved') {
            $this->voucher_path = $voucherPath;
            $this->approval_status = 'completed';
            $this->save();
            return true;
        }

        throw new \Exception("Solo se pueden completar ventas en estado approved");
    }

    // Helpers de estado
    public function isPendingSeller(): bool
    {
        return $this->approval_status === 'pending_seller';
    }

    public function isPendingAdmin(): bool
    {
        return $this->approval_status === 'pending_admin';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function isObserved(): bool
    {
        return $this->approval_status === 'observed';
    }

    public function isCompleted(): bool
    {
        return $this->approval_status === 'completed';
    }

    public function canBeApproved(): bool
    {
        return in_array($this->approval_status, ['pending_seller', 'pending_admin']);
    }

    public function canBeRejected(): bool
    {
        return in_array($this->approval_status, ['pending_seller', 'pending_admin']);
    }

    public function canBeObserved(): bool
    {
        return $this->approval_status === 'pending_admin';
    }

    public function canBeCompleted(): bool
    {
        return $this->approval_status === 'approved';
    }
}
