<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'country_id', 'name', 'swift_code', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function businessAccounts()
    {
        return $this->hasMany(BusinessAccount::class);
    }

    public function transactionCount(): int
    {
        return $this->businessAccounts()->withCount('sellers')->get()->sum('sellers_count');
    }
}
