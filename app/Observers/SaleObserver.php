<?php

namespace App\Observers;

use App\Models\Sale;
use App\Models\SaleLog;
use App\Events\SaleCompleted;
use Illuminate\Support\Facades\Auth;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        // Solo registrar si el approval_status cambió
        if ($sale->isDirty('approval_status')) {
            $oldStatus = $sale->getOriginal('approval_status');
            $newStatus = $sale->approval_status;

            // Determinar la acción basada en el nuevo estado
            $action = match($newStatus) {
                'pending_admin' => $oldStatus === 'observed' ? 'corrected' : 'approved_by_seller',
                'approved' => 'approved',
                'rejected' => 'rejected',
                'observed' => 'observed',
                'completed' => 'completed',
                default => 'status_changed',
            };

            SaleLog::create([
                'sale_id' => $sale->id,
                'user_id' => Auth::id() ?? 1, // Fallback a user 1 si no hay auth
                'action' => $action,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'comment' => $sale->admin_observation ?? null,
            ]);

            // Disparar evento cuando venta se completa
            if ($newStatus === 'completed') {
                event(new SaleCompleted($sale));
            }

            // Agregar comisión al monedero cuando se aprueba o completa
            if (in_array($newStatus, ['approved', 'completed']) && !in_array($oldStatus, ['approved', 'completed'])) {
                $commission = $sale->sellerCommissionAmount();
                $sale->seller->addToWallet(
                    $commission,
                    'commission',
                    "Comisión por venta #{$sale->id} - S/. " . number_format($sale->amount, 2),
                    $sale
                );
            }
        }
    }

    /**
     * Handle the Sale "deleted" event.
     */
    public function deleted(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "restored" event.
     */
    public function restored(Sale $sale): void
    {
        //
    }

    /**
     * Handle the Sale "force deleted" event.
     */
    public function forceDeleted(Sale $sale): void
    {
        //
    }
}
