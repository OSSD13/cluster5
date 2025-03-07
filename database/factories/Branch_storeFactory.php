<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class Branch_storeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bs_map_id' => fake()-> randomDigit(),
            'bs_user_id' => fake()-> randomDigit(),
            'bs_sales_id' => fake()-> randomDigit(),
            'bs_name' => fake()-> name()
            
            //
        ];
    }
}
