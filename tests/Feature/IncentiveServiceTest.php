<?php

use App\Models\IncentiveRule;
use App\Models\Transaction;
use App\Models\User;
use App\Services\IncentiveService;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->svc = app(IncentiveService::class);

    Role::firstOrCreate(['name' => 'cliente',  'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'vendedor', 'guard_name' => 'web']);

    $this->cliente = User::factory()->create();
    $this->cliente->assignRole('cliente');
});

function makeRule(array $overrides = []): IncentiveRule
{
    return IncentiveRule::create(array_merge([
        'name'                 => 'Regla Test ' . rand(1, 9999),
        'type'                 => 'extra_receptor',
        'target_type'          => 'todos_clientes',
        'value_type'           => 'fixed',
        'value'                => 10,
        'active'               => true,
        'starts_at'            => now()->subDay(),
        'uses_count'           => 0,
        'condition_new_client' => false,
        'created_by'           => null,
    ], $overrides));
}

// ── TESTS BÁSICOS ─────────────────────────────────────────────────────────

test('preview sin reglas activas → no hay bono', function () {
    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeFalse();
    expect($preview['bonus_pen'])->toBe(0.0);
    expect($preview['effective_pen'])->toBe(100.0);
});

test('bono fijo aplicado correctamente', function () {
    makeRule(['value' => 15]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeTrue();
    expect($preview['bonus_pen'])->toBe(15.0);
    expect($preview['effective_pen'])->toBe(115.0);
});

test('bono porcentaje calculado correctamente — 5% de S/200 = S/10', function () {
    makeRule(['value_type' => 'percentage', 'value' => 5]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 200);

    expect($preview['has_bonus'])->toBeTrue();
    expect($preview['bonus_pen'])->toBe(10.0);
});

test('múltiples reglas activas → bonos sumados', function () {
    makeRule(['value' => 10]);
    makeRule(['value' => 5]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['bonus_pen'])->toBe(15.0);
});

// ── TESTS DE LÍMITES ──────────────────────────────────────────────────────

test('max_uses agotado → regla excluida del scopeActive', function () {
    makeRule(['max_uses' => 3, 'uses_count' => 3]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeFalse();
});

test('max_uses no agotado → regla incluida', function () {
    makeRule(['max_uses' => 3, 'uses_count' => 2]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeTrue();
});

test('min_amount — monto por debajo bloquea el bono', function () {
    makeRule(['min_amount' => 200]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeFalse();
});

test('min_amount — monto por encima aplica el bono', function () {
    makeRule(['min_amount' => 200]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 300);

    expect($preview['has_bonus'])->toBeTrue();
    expect($preview['bonus_pen'])->toBe(10.0);
});

// ── TESTS DE CONDICIONES ──────────────────────────────────────────────────

test('condition_new_client — usuario con historial es excluido', function () {
    makeRule(['condition_new_client' => true]);

    // Crear un exchange_rate mínimo para satisfacer la FK
    $rateId = \Illuminate\Support\Facades\DB::table('exchange_rates')->insertGetId([
        'ves_rate'   => 35,
        'usd_rate'   => 1,
        'eur_rate'   => 0.9,
        'is_active'  => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    \Illuminate\Support\Facades\DB::table('transactions')->insert([
        'user_id'          => $this->cliente->id,
        'seller_id'        => null,
        'exchange_rate_id' => $rateId,
        'amount_pen'       => 100,
        'amount_ves'       => 3500,
        'bonus_amount_pen' => 0,
        'recipient_bank'   => 'Banco Test',
        'recipient_dni'    => '12345678',
        'recipient_phone'  => '04121234567',
        'sender_bank'      => 'BCP',
        'sender_dni'       => '12345678',
        'voucher'          => 'test.jpg',
        'status'           => 'pending',
        'operation_type'   => 'transferencia',
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeFalse();
});

test('condition_new_client — usuario sin historial recibe el bono', function () {
    makeRule(['condition_new_client' => true, 'value' => 20]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeTrue();
    expect($preview['bonus_pen'])->toBe(20.0);
});

// ── TESTS DE TARGETS ──────────────────────────────────────────────────────

test('extra_comision no aparece en preview del receptor', function () {
    makeRule(['type' => 'extra_comision', 'value' => 99]);

    $preview = $this->svc->getReceptorPreview($this->cliente, 100);

    expect($preview['has_bonus'])->toBeFalse();
    expect($preview['bonus_pen'])->toBe(0.0);
});

test('usuario anónimo ve bono de todos_clientes en simulador público', function () {
    makeRule(['value' => 10]);

    $preview = $this->svc->getReceptorPreview(null, 100);

    expect($preview['has_bonus'])->toBeTrue();
    expect($preview['bonus_pen'])->toBe(10.0);
});

test('usuario anónimo no ve bonos destinados a vendedores', function () {
    makeRule(['target_type' => 'todos_vendedores']);

    $preview = $this->svc->getReceptorPreview(null, 100);

    expect($preview['has_bonus'])->toBeFalse();
});
