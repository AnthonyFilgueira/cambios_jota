<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\WalletTransaction;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'seller_id',
        'amount_pen',
        'amount_ves',
        'exchange_rate_id',
        'operation_type',
        'status',
        'notes',
        'observation',

        // Comprobantes
        'voucher',
        'final_voucher',

        // Datos bancarios del receptor (Venezuela)
        'recipient_bank',
        'recipient_account_number',
        'recipient_account_type',
        'recipient_dni',
        'recipient_phone',

        // Datos de transferencia desde Perú
        'sender_bank',
        'sender_account_number',
        'sender_dni',

        // Tasas BCV (snapshot para historicidad)
        'usd_bcv_rate',
        'eur_bcv_rate',

        // Incentivos
        'bonus_amount_pen',
    ];

    protected $casts = [
        'amount_pen'       => 'decimal:2',
        'amount_ves'       => 'decimal:2',
        'bonus_amount_pen' => 'decimal:2',
        'usd_bcv_rate'     => 'decimal:6',
        'eur_bcv_rate'     => 'decimal:6',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function exchangeRate(): BelongsTo
    {
        return $this->belongsTo(ExchangeRate::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TransactionLog::class);
    }

    public function incentiveRules(): BelongsToMany
    {
        return $this->belongsToMany(IncentiveRule::class, 'transaction_incentive_rules')
            ->withPivot('bonus_amount', 'benefit_type', 'applied_at');
    }

    // Métodos de workflow
    public function markAsObserved(string $observation)
    {
        if ($this->status === 'pending') {
            $this->observation = $observation;
            $this->status = 'observed';
            $this->save();
            return true;
        }

        throw new \Exception("Solo se pueden observar transacciones en estado pending");
    }

    public function process()
    {
        if ($this->status === 'pending' || $this->status === 'observed') {
            $this->status = 'processing';
            $this->save();
            return true;
        }

        throw new \Exception("No se puede procesar esta transacción con estado: {$this->status}");
    }

    public function complete()
    {
        if ($this->status === 'processing') {
            $this->status = 'completed';
            $this->save();

            // Acreditar comisión base del vendedor
            $seller = $this->seller;
            if ($seller && $seller->seller_commission > 0) {
                $baseCommission = round((float) $this->amount_pen * ($seller->seller_commission / 100), 2);
                if ($baseCommission > 0) {
                    WalletTransaction::create([
                        'seller_id'      => $seller->id,
                        'type'           => 'commission',
                        'amount'         => $baseCommission,
                        'balance_after'  => $seller->walletBalance() + $baseCommission,
                        'description'    => "Comisión base {$seller->seller_commission}% — Transacción #{$this->id}",
                        'reference_id'   => $this->id,
                        'reference_type' => 'Transaction',
                    ]);
                }
            }

            // Acreditar bonos de incentivo extra_comision
            app(\App\Services\IncentiveService::class)->applySellerBonusOnComplete($this);

            return true;
        }

        throw new \Exception("Solo se pueden completar transacciones en estado processing");
    }

    public function cancel()
    {
        if (in_array($this->status, ['pending', 'observed', 'processing'])) {
            $this->status = 'cancelled';
            $this->save();
            return true;
        }

        throw new \Exception("No se puede cancelar una transacción con estado: {$this->status}");
    }

    // Helpers de estado
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isObserved(): bool
    {
        return $this->status === 'observed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeObserved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeProcessed(): bool
    {
        return in_array($this->status, ['pending', 'observed']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'observed', 'processing']);
    }
}
