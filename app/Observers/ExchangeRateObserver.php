<?php

namespace App\Observers;

use App\Models\ExchangeRate;
use App\Models\ExchangeRateHistory;

class ExchangeRateObserver
{
    /**
     * Handle the ExchangeRate "created" event.
     */
    public function created(ExchangeRate $exchangeRate): void
    {
        ExchangeRateHistory::log(
            $exchangeRate,
            'created',
            null,
            $exchangeRate->only(['currency_pair_id', 'ves_rate', 'usd_rate', 'eur_rate', 'is_active']),
            'Tasa de cambio creada'
        );
    }

    /**
     * Handle the ExchangeRate "updated" event.
     */
    public function updated(ExchangeRate $exchangeRate): void
    {
        // Obtener valores anteriores y nuevos
        $original = $exchangeRate->getOriginal();
        $changes = $exchangeRate->getChanges();

        // Solo registrar si hay cambios en campos relevantes
        $trackableFields = ['currency_pair_id', 'ves_rate', 'usd_rate', 'eur_rate', 'is_active'];
        $relevantChanges = array_intersect_key($changes, array_flip($trackableFields));

        if (empty($relevantChanges)) {
            return;
        }

        // Determinar tipo de acción
        $action = 'updated';
        if (isset($changes['is_active'])) {
            $action = $changes['is_active'] ? 'activated' : 'deactivated';
        }

        // Registrar cambio
        ExchangeRateHistory::log(
            $exchangeRate,
            $action,
            array_intersect_key($original, $relevantChanges),
            $relevantChanges,
            $this->buildDescription($action, $relevantChanges)
        );
    }

    /**
     * Handle the ExchangeRate "deleting" event (ANTES de eliminar).
     * Usamos "deleting" en lugar de "deleted" para evitar errores de FK.
     */
    public function deleting(ExchangeRate $exchangeRate): void
    {
        ExchangeRateHistory::log(
            $exchangeRate,
            'deleted',
            $exchangeRate->only(['currency_pair_id', 'ves_rate', 'usd_rate', 'eur_rate', 'is_active']),
            null,
            'Tasa de cambio eliminada'
        );
    }

    /**
     * Construir descripción del cambio
     */
    private function buildDescription(string $action, array $changes): string
    {
        if ($action === 'activated') {
            return 'Tasa activada';
        }

        if ($action === 'deactivated') {
            return 'Tasa desactivada';
        }

        $fields = array_keys($changes);
        $fieldLabels = array_map(function($field) {
            return match($field) {
                'ves_rate' => 'Tasa VES',
                'usd_rate' => 'Tasa USD',
                'eur_rate' => 'Tasa EUR',
                'currency_pair_id' => 'Par de divisas',
                default => ucfirst(str_replace('_', ' ', $field)),
            };
        }, $fields);

        return 'Campos actualizados: ' . implode(', ', $fieldLabels);
    }
}
