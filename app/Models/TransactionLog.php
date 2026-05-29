<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'comment',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionInfoAttribute(): array
    {
        return match($this->action) {
            'created'             => ['icon' => '📤', 'label' => 'Solicitud recibida',             'color' => 'blue'],
            'observed'            => ['icon' => '⚠️', 'label' => 'Observación enviada al cliente', 'color' => 'orange'],
            'processed'           => ['icon' => '✅', 'label' => 'Solicitud aprobada',              'color' => 'green'],
            'completed'           => ['icon' => '💸', 'label' => 'Transferencia completada',        'color' => 'teal'],
            'corrected_by_client' => ['icon' => '✏️', 'label' => 'Cliente envió corrección',        'color' => 'purple'],
            'cancelled'           => ['icon' => '❌', 'label' => 'Solicitud cancelada',             'color' => 'red'],
            default               => ['icon' => '📋', 'label' => ucfirst($this->action),            'color' => 'gray'],
        };
    }

    public static function statusLabel(?string $status): string
    {
        if ($status === null) return '—';
        return match($status) {
            'pending'    => 'Pendiente de revisión',
            'observed'   => 'Con observaciones',
            'processing' => 'En proceso',
            'completed'  => 'Completado',
            'cancelled'  => 'Cancelado',
            default      => $status,
        };
    }
}
