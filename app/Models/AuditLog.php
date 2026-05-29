<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_role',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function modelLabel(): string
    {
        return class_basename($this->model_type);
    }

    public function modelLabelEs(): string
    {
        return match (class_basename($this->model_type)) {
            'Transaction'     => 'Transacción',
            'Seller'          => 'Vendedor',
            'User'            => 'Usuario',
            'Country'         => 'País',
            'Bank'            => 'Banco',
            'BusinessAccount' => 'Cuenta Bancaria',
            'IncentiveRule'   => 'Regla de Incentivo',
            'ExchangeRate'    => 'Tasa de cambio',
            'Sale'            => 'Venta',
            default           => class_basename($this->model_type),
        };
    }

    public function actionInfo(): array
    {
        return match ($this->action) {
            'created'  => ['icon' => '✨', 'label' => 'Creación',      'ring' => 'ring-green-300',  'bg' => 'bg-green-100',  'text' => 'text-green-800',  'iconBg' => 'bg-green-500'],
            'updated'  => ['icon' => '✏️',  'label' => 'Actualización', 'ring' => 'ring-blue-300',   'bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'iconBg' => 'bg-blue-500'],
            'deleted'  => ['icon' => '🗑️', 'label' => 'Eliminación',   'ring' => 'ring-red-300',    'bg' => 'bg-red-100',    'text' => 'text-red-800',    'iconBg' => 'bg-red-500'],
            'restored' => ['icon' => '♻️', 'label' => 'Restauración',  'ring' => 'ring-yellow-300', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'iconBg' => 'bg-yellow-500'],
            default    => ['icon' => '📋', 'label' => ucfirst($this->action), 'ring' => 'ring-gray-300', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'iconBg' => 'bg-gray-400'],
        };
    }

    public function actionSentence(): string
    {
        $model = $this->modelLabelEs();
        return match ($this->action) {
            'created'  => "creó un nuevo {$model}",
            'updated'  => "actualizó {$model} #{$this->model_id}",
            'deleted'  => "eliminó {$model} #{$this->model_id}",
            'restored' => "restauró {$model} #{$this->model_id}",
            default    => "{$this->action} {$model} #{$this->model_id}",
        };
    }

    public static function fieldLabel(string $field): string
    {
        return match ($field) {
            'name', 'full_name', 'company_name' => 'Nombre',
            'email'                              => 'Correo electrónico',
            'phone', 'phone_number'              => 'Teléfono',
            'status'                             => 'Estado',
            'amount'                             => 'Monto',
            'amount_pen'                         => 'Monto origen',
            'amount_ves'                         => 'Monto destino',
            'seller_id'                          => 'ID Vendedor',
            'user_id'                            => 'ID Usuario',
            'rate', 'exchange_rate'              => 'Tasa de cambio',
            'observation'                        => 'Observación',
            'code'                               => 'Código',
            'seller_commission'                  => 'Comisión vendedor',
            'boss_commission'                    => 'Comisión jefe',
            'created_at'                         => 'Fecha creación',
            'updated_at'                         => 'Última actualización',
            'ip_address'                         => 'Dirección IP',
            'country_id'                         => 'ID País',
            'bank_id'                            => 'ID Banco',
            'account_number'                     => 'Número de cuenta',
            'account_type'                       => 'Tipo de cuenta',
            'currency'                           => 'Divisa',
            'description'                        => 'Descripción',
            'is_active'                          => 'Activo',
            'percentage'                         => 'Porcentaje',
            default                              => ucfirst(str_replace('_', ' ', $field)),
        };
    }

    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'created'  => 'bg-green-100 text-green-800',
            'updated'  => 'bg-blue-100 text-blue-800',
            'deleted'  => 'bg-red-100 text-red-800',
            'restored' => 'bg-yellow-100 text-yellow-800',
            default    => 'bg-gray-100 text-gray-700',
        };
    }
}
