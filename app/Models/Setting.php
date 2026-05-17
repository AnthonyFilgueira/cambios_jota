<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    // Definición de todas las settings con sus defaults
    public const DEFINITIONS = [
        // Grupo: negocio
        'business_name'  => ['label' => 'Nombre del negocio',    'group' => 'negocio',      'type' => 'string',  'default' => 'Cambio J', 'description' => 'Aparece en correos y documentos'],
        'business_phone' => ['label' => 'Teléfono de contacto',  'group' => 'negocio',      'type' => 'string',  'default' => '',         'description' => 'WhatsApp o teléfono de soporte'],
        'business_email' => ['label' => 'Email de contacto',     'group' => 'negocio',      'type' => 'string',  'default' => '',         'description' => 'Email visible para clientes'],
        'business_address'=> ['label' => 'Dirección / RUC',      'group' => 'negocio',      'type' => 'string',  'default' => '',         'description' => 'Información legal del negocio'],

        // Grupo: transacciones
        'transaction_min_amount' => ['label' => 'Monto mínimo por transacción (S/)', 'group' => 'transacciones', 'type' => 'decimal', 'default' => '10',   'description' => 'El cliente no puede enviar menos de este monto'],
        'transaction_max_amount' => ['label' => 'Monto máximo por transacción (S/)', 'group' => 'transacciones', 'type' => 'decimal', 'default' => '5000', 'description' => 'Límite superior por operación'],
        'transaction_daily_limit'=> ['label' => 'Límite diario por cliente (S/)',    'group' => 'transacciones', 'type' => 'decimal', 'default' => '10000','description' => 'Máximo acumulado diario por cliente'],

        // Grupo: comisiones
        'default_seller_commission' => ['label' => 'Comisión vendedor por defecto (%)', 'group' => 'comisiones', 'type' => 'decimal', 'default' => '3', 'description' => 'Se aplica al crear un nuevo vendedor'],
        'default_boss_commission'   => ['label' => 'Comisión jefe por defecto (%)',     'group' => 'comisiones', 'type' => 'decimal', 'default' => '2', 'description' => 'Parte que retiene el negocio'],

        // Grupo: sistema
        'maintenance_mode'    => ['label' => 'Modo mantenimiento',        'group' => 'sistema', 'type' => 'boolean', 'default' => '0',  'description' => 'Muestra aviso de mantenimiento a los clientes'],
        'maintenance_message' => ['label' => 'Mensaje de mantenimiento',  'group' => 'sistema', 'type' => 'string',  'default' => 'Estamos realizando mejoras. Volvemos pronto.', 'description' => 'Texto visible cuando el modo mantenimiento está activo'],
        'simulator_note'      => ['label' => 'Nota en el simulador público', 'group' => 'sistema', 'type' => 'string', 'default' => '', 'description' => 'Mensaje informativo que aparece bajo el cotizador'],
    ];

    // Obtener un valor (con cache de 5 min)
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting:{$key}", 300, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) return $default ?? (self::DEFINITIONS[$key]['default'] ?? null);
            return static::cast($setting->value, $setting->type);
        });
    }

    // Guardar y limpiar cache
    public static function set(string $key, mixed $value): void
    {
        $def = self::DEFINITIONS[$key] ?? [];
        static::updateOrCreate(['key' => $key], [
            'value'       => (string) $value,
            'type'        => $def['type'] ?? 'string',
            'group'       => $def['group'] ?? 'general',
            'label'       => $def['label'] ?? $key,
            'description' => $def['description'] ?? null,
        ]);
        Cache::forget("setting:{$key}");
    }

    private static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int)  $value,
            'decimal' => (float) $value,
            default   => (string) $value,
        };
    }

    // Inicializar todas las settings con sus defaults si no existen
    public static function seedDefaults(): void
    {
        foreach (self::DEFINITIONS as $key => $def) {
            static::firstOrCreate(['key' => $key], [
                'value'       => $def['default'],
                'type'        => $def['type'],
                'group'       => $def['group'],
                'label'       => $def['label'],
                'description' => $def['description'] ?? null,
            ]);
        }
    }
}
