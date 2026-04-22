<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
