<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Seller;
use App\Models\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amountPen = fake()->randomFloat(2, 50, 5000); // Entre 50 y 5000 soles
        $exchangeRate = ExchangeRate::inRandomOrder()->first() ?? ExchangeRate::factory()->create();
        $amountVes = $amountPen * $exchangeRate->ves_rate;

        return [
            'user_id' => User::factory(),
            'seller_id' => fake()->boolean(70) ? Seller::inRandomOrder()->first()?->id : null, // 70% tienen vendedor
            'amount_pen' => $amountPen,
            'amount_ves' => $amountVes,
            'exchange_rate_id' => $exchangeRate->id,
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'notes' => fake()->boolean(30) ? fake()->sentence() : null,
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
