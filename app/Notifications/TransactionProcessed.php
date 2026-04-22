<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionProcessed extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Transaction $transaction
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu transacción #' . $this->transaction->id . ' está siendo procesada')
            ->greeting('¡Hola ' . $this->transaction->user->name . '!')
            ->line('Tu solicitud de envío de divisas está siendo procesada.')
            ->line('**Detalles de la transacción:**')
            ->line('Monto enviado: S/ ' . number_format($this->transaction->amount_pen, 2))
            ->line('Monto a recibir: Bs. ' . number_format($this->transaction->amount_ves, 2))
            ->line('El vendedor asignado está gestionando tu solicitud.')
            ->action('Ver estado', route('transactions.index'))
            ->line('Te notificaremos cuando la transferencia haya sido completada.')
            ->salutation('Saludos, Equipo Cambio J');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'amount_pen' => $this->transaction->amount_pen,
            'amount_ves' => $this->transaction->amount_ves,
        ];
    }
}
