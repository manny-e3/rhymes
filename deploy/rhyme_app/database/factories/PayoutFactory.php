<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payout>
 */
class PayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount_requested' => $this->faker->randomFloat(2, 10000, 1000000),
            'status' => $this->faker->randomElement(['pending', 'approved', 'denied']),
            'admin_notes' => $this->faker->sentence(),
        ];
    }
}