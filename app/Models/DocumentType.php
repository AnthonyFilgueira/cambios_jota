<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'country_id',
        'name',
        'code',
        'prefix',
        'placeholder',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
