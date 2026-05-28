<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'code',
        'fields_required',
        'active',
    ];

    protected $casts = [
        'active'          => 'boolean',
        'fields_required' => 'array',
    ];

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
