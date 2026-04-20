<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRateHistory extends Model
{
    // Solo created_at (no updated_at)
    const UPDATED_AT = null;

    protected $table = 'exchange_rate_history';

    protected $fillable = [
        'exchange_rate_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // =====================================
    // RELACIONES
    // =====================================

    /**
     * Tasa de cambio relacionada
     */
    public function exchangeRate(): BelongsTo
    {
        return $this->belongsTo(ExchangeRate::class);
    }

    /**
     * Usuario que realizó el cambio
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =====================================
    // MÉTODOS ESTÁTICOS
    // =====================================

    /**
     * Registrar un cambio en el historial
     *
     * @param ExchangeRate $rate
     * @param string $action
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param string|null $description
     * @return self
     */
    public static function log(
        ExchangeRate $rate,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): self {
        return self::create([
            'exchange_rate_id' => $rate->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }

    // =====================================
    // ATRIBUTOS COMPUTADOS
    // =====================================

    /**
     * Nombre del usuario que hizo el cambio
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Sistema';
    }

    /**
     * Descripción legible de la acción
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'created' => 'Creada',
            'updated' => 'Actualizada',
            'activated' => 'Activada',
            'deactivated' => 'Desactivada',
            'deleted' => 'Eliminada',
            default => ucfirst($this->action),
        };
    }

    /**
     * Lista de cambios en formato legible
     */
    public function getChangesAttribute(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changes = [];

        foreach ($this->new_values as $field => $newValue) {
            $oldValue = $this->old_values[$field] ?? null;

            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'from' => $oldValue,
                    'to' => $newValue,
                    'label' => $this->getFieldLabel($field),
                ];
            }
        }

        return $changes;
    }

    /**
     * Etiqueta legible del campo
     */
    private function getFieldLabel(string $field): string
    {
        return match($field) {
            'ves_rate' => 'Tasa VES',
            'usd_rate' => 'Tasa USD',
            'eur_rate' => 'Tasa EUR',
            'is_active' => 'Estado',
            'currency_pair_id' => 'Par de divisas',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }
}
