<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales>
 */
class SalesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // get all max bs_id from branch_stores table
        $max_bs_id = DB::table('branch_stores')->max('bs_id');
        return [
            'sales_package_amount' => $sales_package_amount = fake()->numberBetween(1, 400),
            'sales_amount' => $sales_package_amount * fake()->randomFloat(2, 20, 50),
            'sales_bs_id' => fake()->numberBetween(1, $max_bs_id),
            'created_at' => fake()->dateTimeBetween('-1 years','now'),
            'sales_month' => fake()-> dateTimeBetween('-1 years','now'),
        ];
    }
}
