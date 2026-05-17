<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id', 'bank_id', 'account_number', 'account_type',
        'account_holder', 'dni_ruc', 'alias', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'business_account_seller')
            ->withTimestamps()
            ->wherePivotNull('unassigned_at');
    }

    public function allSellers()
    {
        return $this->belongsToMany(Seller::class, 'business_account_seller')->withTimestamps();
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->alias ?: $this->bank->name . ' · ' . $this->account_number;
    }

    public function getAccountTypeLabelAttribute(): string
    {
        return match($this->account_type) {
            'ahorro'    => 'Ahorros',
            'corriente' => 'Corriente',
            'movil'     => 'Pago móvil',
            default     => $this->account_type,
        };
    }
}
