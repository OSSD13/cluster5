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

        // $table->id("sales_id");
        // $table->double("sales_amount");
        // $table->integer('sales_order_amount');
        // $table->integer('sales_branch_id');
        // // foreign key
        // $table->foreign('sales_branch_id')->references('bs_id')->on('branch_stores');
        // $table->timestamps();
        return [
            'sales_package_amount' => $sales_package_amount = fake()->numberBetween(1, 400),
            'sales_amount' => $sales_package_amount * fake()->randomFloat(2, 20, 50),
            'sales_branch_id' => fake()->numberBetween(1, $max_bs_id),
            'created_at' => fake()->dateTimeBetween('-1 years','now'),
            'sales_month' => fake()-> dateTimeBetween('-1 years','now'),
        ];
    }
}
