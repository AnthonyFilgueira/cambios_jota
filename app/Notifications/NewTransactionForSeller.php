<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Transaction;

class NewTransactionForSeller extends Notification implements ShouldQueue
{
    use Queueable;

    public $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
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
            ->subject('Nueva Transacción Asignada - Cambios Jotta')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se ha generado una nueva transacción a tu nombre.')
            ->line('**Detalles de la transacción:**')
            ->line('Cliente: ' . $this->transaction->user->name)
            ->line('Monto a enviar: S/. ' . number_format($this->transaction->amount_pen, 2))
            ->line('Monto a recibir: Bs. ' . number_format($this->transaction->amount_ves, 2))
            ->line('Estado: ' . $this->transaction->status)
            ->action('Ver Transacción', url('/seller-dashboard'))
            ->line('Por favor, contacta al cliente para coordinar la transferencia.')
            ->salutation('Saludos, Cambios Jotta');
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
            'client_name' => $this->transaction->user->name,
            'amount_pen' => $this->transaction->amount_pen,
            'amount_ves' => $this->transaction->amount_ves,
            'status' => $this->transaction->status,
        ];
    }
}
