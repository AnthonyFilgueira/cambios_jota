<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    private static array $labels = [
        'pending'    => 'Pendiente de revisión',
        'observed'   => 'Observada — requiere corrección',
        'processing' => 'Aprobada por tu vendedor — en proceso',
        'completed'  => 'Completada',
        'cancelled'  => 'Denegada',
    ];

    private static array $subjects = [
        'pending'    => 'Tu transacción está pendiente de revisión',
        'observed'   => 'Tu transacción tiene observaciones',
        'processing' => 'Tu transacción está en proceso',
        'completed'  => 'Tu transacción fue completada',
        'cancelled'  => 'Tu transacción fue denegada',
    ];

    public function __construct(
        public Transaction $transaction,
        public string $newStatus,
        public ?string $motivo = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = self::$subjects[$this->newStatus] ?? 'Actualización de tu transacción';
        $label   = self::$labels[$this->newStatus]   ?? $this->newStatus;

        $mail = (new MailMessage)
            ->subject($subject . ' #' . $this->transaction->id)
            ->greeting('¡Hola ' . ($this->transaction->user->name ?? $notifiable->name) . '!')
            ->line('El estado de tu transacción #' . $this->transaction->id . ' ha cambiado.')
            ->line('**Nuevo estado:** ' . $label)
            ->line('**Monto enviado:** S/ ' . number_format($this->transaction->amount_pen, 2))
            ->line('**Monto a recibir:** Bs. ' . number_format($this->transaction->amount_ves, 2));

        if ($this->motivo) {
            $mail->line('**Motivo:** ' . $this->motivo);
        }

        return $mail
            ->action('Ver detalles', route('transactions.index'))
            ->salutation('Saludos, Equipo Cambio J');
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
