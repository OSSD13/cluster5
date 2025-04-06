<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointOfInterestType>
 */
class PointOfInterestTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $max_location_id = DB::table('locations')->max('location_id');

        // get all distinct poi_type from point_of_interest_type table
        $poi_type = DB::table('point_of_interest_type')->pluck('poit_type')->toArray();
        return [
            // "poit_type" => $this->faker->randomElement($poi_type),
            // "poit_name" => $this->faker->name(),
            // "poit_icon" => $this->faker->imageUrl(),
            // "poit_color" => $this->faker->colorName(),
            // "poit_description" => $this->faker->text(),
            //
        ];
    }
}