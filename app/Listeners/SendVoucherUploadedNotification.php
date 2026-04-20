<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use App\Mail\VoucherUploadedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendVoucherUploadedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaleCompleted $event): void
    {
        $sale = $event->sale;

        // Enviar email al vendedor (tiene email en el modelo Seller)
        if ($sale->seller && $sale->seller->email) {
            Mail::to($sale->seller->email)
                ->send(new VoucherUploadedMail($sale));

            Log::info("Email de comprobante enviado al vendedor {$sale->seller->email} para venta #{$sale->id}");
        }
    }
}
