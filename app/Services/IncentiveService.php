<?php

namespace App\Services;

use App\Models\IncentiveRule;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Collection;

class IncentiveService
{
    /**
     * Retorna las reglas activas que aplican a un usuario, monto y moneda dados.
     * Rules with currency_id=null apply to all currencies.
     * Rules with currency_id only apply when the from-currency matches.
     */
    public function getApplicableRules(?User $user, float $amountPen, ?int $currencyId = null): Collection
    {
        return IncentiveRule::active()
            ->with('currency')
            ->when($currencyId, function ($q) use ($currencyId) {
                $q->where(function ($inner) use ($currencyId) {
                    $inner->whereNull('currency_id')
                          ->orWhere('currency_id', $currencyId);
                });
            })
            ->get()
            ->filter(fn ($rule) => $this->isEligible($rule, $user, $amountPen));
    }

    /**
     * Preview sin escribir en BD — para simulador y formulario.
     * Devuelve la suma de bonos de tipo extra_receptor para mostrar al usuario.
     */
    public function getReceptorPreview(?User $user, float $amountPen, ?int $currencyId = null): array
    {
        $rules = $this->getApplicableRules($user, $amountPen, $currencyId)
            ->where('type', 'extra_receptor');

        $bonusPen = $rules->sum(fn ($r) => $this->calculateBonus($r, $amountPen));

        return [
            'has_bonus'     => $rules->isNotEmpty(),
            'bonus_pen'     => round($bonusPen, 2),
            'effective_pen' => round($amountPen + $bonusPen, 2),
            'rules'         => $rules->values()->map(fn ($r) => [
                'name'       => $r->name,
                'value_label'=> $r->valueLabel(),
                'type_icon'  => $r->typeIcon(),
                'value_type' => $r->value_type,
                'value'      => (float) $r->value,
            ]),
        ];
    }

    /**
     * Calcula el monto del bono para una regla y un monto dado.
     */
    public function calculateBonus(IncentiveRule $rule, float $amountPen): float
    {
        return match ($rule->value_type) {
            'fixed'      => (float) $rule->value,
            'percentage' => round($amountPen * (float) $rule->value / 100, 2),
            default      => 0.0,
        };
    }

    /**
     * Aplica los bonos extra_receptor al crear la transacción:
     * - Actualiza amount_ves con el extra
     * - Guarda bonus_amount_pen
     * - Crea registros en el pivot
     * - Incrementa uses_count de cada regla
     */
    public function applyToTransaction(Transaction $transaction): void
    {
        $user  = $transaction->user;
        $rules = $this->getApplicableRules($user, (float) $transaction->amount_pen)
            ->where('type', 'extra_receptor');

        if ($rules->isEmpty()) return;

        $totalBonus = 0.0;

        foreach ($rules as $rule) {
            $bonus = $this->calculateBonus($rule, (float) $transaction->amount_pen);
            $totalBonus += $bonus;

            $transaction->incentiveRules()->attach($rule->id, [
                'bonus_amount' => $bonus,
                'benefit_type' => 'receptor',
                'applied_at'   => now(),
            ]);

            $rule->increment('uses_count');
        }

        // Recalcular amount_ves con el bono
        $effectivePen = (float) $transaction->amount_pen + $totalBonus;
        $vesRate      = $transaction->exchangeRate?->ves_rate ?? 0;

        $transaction->bonus_amount_pen = round($totalBonus, 2);
        $transaction->amount_ves       = round($effectivePen * $vesRate, 2);
        $transaction->saveQuietly();
    }

    /**
     * Aplica los bonos extra_comision al completar la transacción:
     * - Lee reglas activas para el vendedor
     * - Acredita en el wallet del vendedor
     */
    public function applySellerBonusOnComplete(Transaction $transaction): void
    {
        $seller = $transaction->seller;
        if (!$seller) return;

        $user  = $seller->user;
        if (!$user) return;

        $rules = $this->getApplicableRules($user, (float) $transaction->amount_pen)
            ->where('type', 'extra_comision');

        if ($rules->isEmpty()) return;

        foreach ($rules as $rule) {
            $bonus = $this->calculateBonus($rule, (float) $transaction->amount_pen);
            if ($bonus <= 0) continue;

            // Acreditar en wallet
            $currentBalance = $seller->walletBalance();
            WalletTransaction::create([
                'seller_id'      => $seller->id,
                'type'           => 'commission',
                'amount'         => $bonus,
                'balance_after'  => $currentBalance + $bonus,
                'description'    => 'Bono incentivo: ' . $rule->name,
                'reference_id'   => $transaction->id,
                'reference_type' => 'Transaction',
            ]);

            // Registrar en pivot
            $transaction->incentiveRules()->attach($rule->id, [
                'bonus_amount' => $bonus,
                'benefit_type' => 'comision',
                'applied_at'   => now(),
            ]);

            $rule->increment('uses_count');
        }
    }

    // ─────────────────────────────────────────
    // PRIVADOS
    // ─────────────────────────────────────────

    private function isEligible(IncentiveRule $rule, ?User $user, float $amountPen): bool
    {
        if (!$this->checkTarget($rule, $user)) return false;

        if ($rule->min_amount && $amountPen < (float) $rule->min_amount) return false;

        if ($rule->min_transactions && $user) {
            $count = Transaction::where('user_id', $user->id)
                ->where('status', 'completed')
                ->count();
            if ($count < $rule->min_transactions) return false;
        }

        if ($rule->condition_new_client && $user) {
            $hasAny = Transaction::where('user_id', $user->id)->exists();
            if ($hasAny) return false;
        }

        return true;
    }

    private function checkTarget(IncentiveRule $rule, ?User $user): bool
    {
        if ($user === null) {
            // Visitante anónimo: solo puede ver bonos para todos los clientes
            return $rule->target_type === 'todos_clientes';
        }

        return match ($rule->target_type) {
            'todos_clientes'       => $user->hasRole('cliente'),
            'cliente_nuevo'        => $user->hasRole('cliente'),
            'cliente_especifico'   => (int) $rule->target_id === $user->id,
            'todos_vendedores'     => $user->seller !== null,
            'vendedor_especifico'  => (int) $rule->target_id === ($user->seller?->id),
            'clientes_de_vendedor' => (int) $rule->target_id === ($user->assignedSeller?->id),
            default                => false,
        };
    }
}
