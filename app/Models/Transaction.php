<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'seller_id',
        'amount_pen',
        'amount_ves',
        'exchange_rate_id',
        'status',
        'notes',
        'observation',

        // Comprobante
        'voucher',

        // Datos bancarios del receptor (Venezuela)
        'recipient_bank',
        'recipient_account_number',
        'recipient_dni',
        'recipient_account_type',

        // Datos de transferencia desde Perú
        'sender_bank',
        'sender_account_number',

        // Tasas BCV (snapshot para historicidad)
        'usd_bcv_rate',
        'eur_bcv_rate',
    ];

    protected $casts = [
        'amount_pen' => 'decimal:2',
        'amount_ves' => 'decimal:2',
        'usd_bcv_rate' => 'decimal:6',
        'eur_bcv_rate' => 'decimal:6',
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
