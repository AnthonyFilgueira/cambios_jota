<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionStatusChanged extends Notification
{
    use Queueable;

    private static array $labels = [
        'pending'    => 'Pendiente de revisión',
        'observed'   => 'Observada — requiere corrección',
        'processing' => 'Aprobada por tu vendedor — en proceso',
        'completed'  => 'Completada',
        'cancelled'  => 'Denegada',
    ];

    public function __construct(
        public Transaction $transaction,
        public string $newStatus,
        public ?string $motivo = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'status'         => $this->newStatus,
            'label'          => self::$labels[$this->newStatus] ?? $this->newStatus,
            'motivo'         => $this->motivo,
            'amount_pen'     => $this->transaction->amount_pen,
            'amount_ves'     => $this->transaction->amount_ves,
        ];
    }
}
