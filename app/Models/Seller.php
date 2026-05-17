<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    // ===============================================
    // MÉTODOS ESTÁTICOS - RANKINGS Y COMPARACIONES
    // ===============================================

    /**
     * Ranking de vendedores por total vendido en un rango
     *
     * @param string $start
     * @param string $end
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function rankingByTotalSales($start, $end, $limit = 10)
    {
        return self::all()->map(function ($seller) use ($start, $end) {
            return [
                'seller' => $seller,
                'total_sales' => $seller->totalSalesBetween($start, $end),
                'sales_count' => $seller->salesCountBetween($start, $end),
                'commission' => $seller->sellerCommissionTotal($start, $end),
            ];
        })->sortByDesc('total_sales')->take($limit);
    }

    /**
     * Ranking de vendedores por número de ventas
     *
     * @param string $start
     * @param string $end
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function rankingBySalesCount($start, $end, $limit = 10)
    {
        return self::all()->map(function ($seller) use ($start, $end) {
            return [
                'seller' => $seller,
                'sales_count' => $seller->salesCountBetween($start, $end),
                'total_sales' => $seller->totalSalesBetween($start, $end),
                'commission' => $seller->sellerCommissionTotal($start, $end),
            ];
        })->sortByDesc('sales_count')->take($limit);
    }

    /**
     * Ranking de vendedores por comisiones generadas
     *
     * @param string $start
     * @param string $end
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function rankingByCommissions($start, $end, $limit = 10)
    {
        return self::all()->map(function ($seller) use ($start, $end) {
            return [
                'seller' => $seller,
                'commission' => $seller->sellerCommissionTotal($start, $end),
                'total_sales' => $seller->totalSalesBetween($start, $end),
                'sales_count' => $seller->salesCountBetween($start, $end),
            ];
        })->sortByDesc('commission')->take($limit);
    }

    /**
     * Total global de ventas de todos los vendedores en un rango
     *
     * @param string $start
     * @param string $end
     * @return float
     */
    public static function globalTotalSales($start, $end)
    {
        return Sale::whereBetween('sale_date', [$start, $end])->sum('amount');
    }

    /**
     * Total global de comisiones de vendedores en un rango
     *
     * @param string $start
     * @param string $end
     * @return float
     */
    public static function globalSellerCommissions($start, $end)
    {
        return Sale::whereBetween('sale_date', [$start, $end])
            ->get()
            ->sum(fn($sale) => $sale->sellerCommissionAmount());
    }

    /**
     * Total global de comisiones del dueño en un rango
     *
     * @param string $start
     * @param string $end
     * @return float
     */
    public static function globalBossCommissions($start, $end)
    {
        return Sale::whereBetween('sale_date', [$start, $end])
            ->get()
            ->sum(fn($sale) => $sale->bossCommissionAmount());
    }

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function businessAccounts()
    {
        return $this->belongsToMany(BusinessAccount::class, 'business_account_seller')
            ->withTimestamps()
            ->wherePivotNull('unassigned_at');
    }

    public function allBusinessAccounts()
    {
        return $this->belongsToMany(BusinessAccount::class, 'business_account_seller')->withTimestamps();
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class)->orderBy('created_at', 'desc');
    }

    public function liquidations()
    {
        return $this->hasMany(Liquidation::class)->orderBy('payment_date', 'desc');
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

    // ===============================================
    // MOTOR DE CÁLCULO DE GANANCIAS
    // ===============================================

    /**
     * Contar ventas en un rango de fechas, con filtro opcional por estado
     *
     * @param string $start Fecha inicio (Y-m-d)
     * @param string $end Fecha fin (Y-m-d)
     * @param string|null $status Estado (pending_seller, pending_admin, approved, completed, rejected, observed)
     * @return int
     */
    public function salesCountBetween($start, $end, $status = null)
    {
        $query = $this->sales()->whereBetween('sale_date', [$start, $end]);

        if ($status) {
            $query->where('approval_status', $status);
        }

        return $query->count();
    }

    /**
     * Obtener ventas por estado específico
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function salesByStatus($status)
    {
        return $this->sales()->where('approval_status', $status)->get();
    }

    /**
     * Calcular ticket promedio en un rango de fechas
     *
     * @param string $start
     * @param string $end
     * @return float
     */
    public function averageTicketBetween($start, $end)
    {
        $sales = $this->sales()->whereBetween('sale_date', [$start, $end]);
        $count = $sales->count();

        if ($count === 0) {
            return 0;
        }

        $total = $sales->sum('amount');
        return $total / $count;
    }

    /**
     * Calcular tasa de conversión (ventas aprobadas/completadas vs totales)
     *
     * @param string $start
     * @param string $end
     * @return float Porcentaje (0-100)
     */
    public function conversionRateBetween($start, $end)
    {
        $total = $this->salesCountBetween($start, $end);

        if ($total === 0) {
            return 0;
        }

        $approved = $this->salesCountBetween($start, $end, 'approved');
        $completed = $this->salesCountBetween($start, $end, 'completed');

        return (($approved + $completed) / $total) * 100;
    }

    /**
     * Total vendido histórico (todas las ventas aprobadas/completadas)
     *
     * @return float
     */
    public function totalHistoricSales()
    {
        return $this->sales()
            ->whereIn('approval_status', ['approved', 'completed'])
            ->sum('amount');
    }

    /**
     * Total de comisiones del vendedor (histórico)
     *
     * @return float
     */
    public function totalHistoricSellerCommissions()
    {
        return $this->sales()
            ->whereIn('approval_status', ['approved', 'completed'])
            ->get()
            ->sum(fn($sale) => $sale->sellerCommissionAmount());
    }

    /**
     * Total de comisiones del dueño (histórico)
     *
     * @return float
     */
    public function totalHistoricBossCommissions()
    {
        return $this->sales()
            ->whereIn('approval_status', ['approved', 'completed'])
            ->get()
            ->sum(fn($sale) => $sale->bossCommissionAmount());
    }

    /**
     * Saldo pendiente de pago del vendedor
     * (Total comisiones aprobadas/completadas - Total liquidaciones)
     *
     * Nota: Requiere implementación de liquidaciones (REQ 5.6)
     * Por ahora retorna solo el total de comisiones
     *
     * @return float
     */
    public function pendingBalance()
    {
        // TODO: Restar liquidaciones cuando se implemente REQ 5.6
        // return $this->totalHistoricSellerCommissions() - $this->totalLiquidations();

        return $this->totalHistoricSellerCommissions();
    }

    /**
     * Métricas resumidas de un vendedor en un rango de fechas
     *
     * @param string $start
     * @param string $end
     * @return array
     */
    public function metricsBetween($start, $end)
    {
        return [
            'total_sales' => $this->salesCountBetween($start, $end),
            'approved_sales' => $this->salesCountBetween($start, $end, 'approved'),
            'completed_sales' => $this->salesCountBetween($start, $end, 'completed'),
            'rejected_sales' => $this->salesCountBetween($start, $end, 'rejected'),
            'total_amount' => $this->totalSalesBetween($start, $end),
            'average_ticket' => $this->averageTicketBetween($start, $end),
            'conversion_rate' => $this->conversionRateBetween($start, $end),
            'seller_commission' => $this->sellerCommissionTotal($start, $end),
            'boss_commission' => $this->bossCommissionTotal($start, $end),
        ];
    }

    // ===============================================
    // MONEDERO VIRTUAL
    // ===============================================

    /**
     * Obtener saldo actual del monedero
     * (Basado en la última transacción registrada)
     *
     * @return float
     */
    public function walletBalance()
    {
        $lastTransaction = $this->walletTransactions()->latest('created_at')->first();
        return $lastTransaction ? $lastTransaction->balance_after : 0;
    }

    /**
     * Agregar fondos al monedero
     *
     * @param float $amount
     * @param string $type (commission, liquidation, adjustment)
     * @param string $description
     * @param mixed $reference (Sale, Liquidation, etc.)
     * @return WalletTransaction
     */
    public function addToWallet($amount, $type, $description, $reference = null)
    {
        $currentBalance = $this->walletBalance();
        $newBalance = $currentBalance + abs($amount); // Siempre sumar positivo

        return $this->walletTransactions()->create([
            'type' => $type,
            'amount' => abs($amount),
            'balance_after' => $newBalance,
            'description' => $description,
            'reference_id' => $reference ? $reference->id : null,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
    }

    /**
     * Restar fondos del monedero
     *
     * @param float $amount
     * @param string $type
     * @param string $description
     * @param mixed $reference
     * @return WalletTransaction
     * @throws \Exception Si no hay saldo suficiente
     */
    public function deductFromWallet($amount, $type, $description, $reference = null)
    {
        $currentBalance = $this->walletBalance();
        $deductAmount = abs($amount);

        if ($currentBalance < $deductAmount) {
            throw new \Exception("Saldo insuficiente. Saldo actual: S/. {$currentBalance}, Intentando restar: S/. {$deductAmount}");
        }

        $newBalance = $currentBalance - $deductAmount;

        return $this->walletTransactions()->create([
            'type' => $type,
            'amount' => -$deductAmount, // Negativo para indicar egreso
            'balance_after' => $newBalance,
            'description' => $description,
            'reference_id' => $reference ? $reference->id : null,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
    }

    /**
     * Ajustar saldo manualmente (corrección)
     *
     * @param float $amount (positivo o negativo)
     * @param string $description
     * @return WalletTransaction
     */
    public function adjustWallet($amount, $description)
    {
        $currentBalance = $this->walletBalance();
        $newBalance = $currentBalance + $amount;

        return $this->walletTransactions()->create([
            'type' => 'adjustment',
            'amount' => $amount,
            'balance_after' => $newBalance,
            'description' => $description,
        ]);
    }

    /**
     * Verificar si tiene saldo suficiente
     *
     * @param float $amount
     * @return bool
     */
    public function hasSufficientBalance($amount)
    {
        return $this->walletBalance() >= $amount;
    }
}