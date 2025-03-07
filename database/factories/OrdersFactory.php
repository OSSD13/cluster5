<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
            'order_bs_id' => fake()-> randomDigit(),
        ];
        // // $table->id("order_id");
        // $table->string('order_name');
        // $table->string('total_amount'); 
        // $table->string('quantity');
        // $table->string('order_bs_id');
    }
}
