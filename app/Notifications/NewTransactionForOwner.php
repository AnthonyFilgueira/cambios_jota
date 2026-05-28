<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTransactionForOwner extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Transaction $transaction,
        public string $event = 'created',
        public ?string $detail = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $eventLabels = [
            'created'    => 'Nueva transacción creada',
            'observed'   => 'Transacción observada',
            'processing' => 'Transacción en proceso',
            'completed'  => 'Transacción completada',
            'cancelled'  => 'Transacción cancelada',
        ];

        $subject = ($eventLabels[$this->event] ?? 'Actualización de transacción') . ' #' . $this->transaction->id;

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line($subject . ' en el sistema.')
            ->line('**Cliente:** ' . ($this->transaction->user->name ?? '—'))
            ->line('**Vendedor asignado:** ' . ($this->transaction->seller->name ?? '—'))
            ->line('**Monto enviado:** S/ ' . number_format($this->transaction->amount_pen, 2))
            ->line('**Monto a recibir:** Bs. ' . number_format($this->transaction->amount_ves, 2))
            ->line('**Estado:** ' . ($eventLabels[$this->event] ?? $this->event));

        if ($this->detail) {
            $mail->line('**Detalle:** ' . $this->detail);
        }

        return $mail
            ->action('Ver transacción', route('transactions.manage'))
            ->salutation('Sistema Cambio J');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'event'          => $this->event,
            'client_name'    => $this->transaction->user->name ?? '—',
            'seller_name'    => $this->transaction->seller->name ?? '—',
            'amount_pen'     => $this->transaction->amount_pen,
            'amount_ves'     => $this->transaction->amount_ves,
            'detail'         => $this->detail,
        ];
    }
}
