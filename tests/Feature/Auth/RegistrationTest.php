<?php

use Spatie\Permission\Models\Role;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('new users can register', function () {
    Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

    $seller = \App\Models\Seller::create([
        'name'               => 'Test Seller',
        'code'               => 'TESTSEL',
        'seller_commission'  => 2.0,
        'boss_commission'    => 1.0,
    ]);

    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'phone'                 => '+1234567890',
        'vendor_code'           => $seller->code,
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
