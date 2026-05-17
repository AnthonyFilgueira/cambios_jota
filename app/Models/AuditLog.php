<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_role',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function modelLabel(): string
    {
        return class_basename($this->model_type);
    }

    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'created'  => 'bg-green-100 text-green-800',
            'updated'  => 'bg-blue-100 text-blue-800',
            'deleted'  => 'bg-red-100 text-red-800',
            'restored' => 'bg-yellow-100 text-yellow-800',
            default    => 'bg-gray-100 text-gray-700',
        };
    }
}
