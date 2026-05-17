<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code_iso', 'emoji', 'currency_name', 'role', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function banks()
    {
        return $this->hasMany(Bank::class);
    }

    public function activeBanks()
    {
        return $this->hasMany(Bank::class)->where('active', true);
    }

    public function businessAccounts()
    {
        return $this->hasMany(BusinessAccount::class);
    }

    public function activeBusinessAccounts()
    {
        return $this->hasMany(BusinessAccount::class)->where('active', true);
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'origin'      => 'Origen',
            'destination' => 'Destino',
            'both'        => 'Origen y destino',
            default       => $this->role,
        };
    }
}
