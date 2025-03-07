<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orders>
 */
class OrdersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_name' => fake()-> word(),
            'total_amount' => fake()-> randomFloat(),
            'quantity' => fake()-> randomDigit(),
            'order_bs_id' => fake()-> randomDigit()
        ];
    }
}
