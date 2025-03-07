<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Point_of_interest>
 */
class Point_of_interestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // get all max location_id from locations table
        $max_location_id = DB::table('locations')->max('location_id');

        return [
            "poi_name" => $this->faker->name(),
            "poi_type" => $this->faker->name(),
            "poi_gps_lat" => $this->faker->randomFloat(15, -180, 180),
            "poi_gps_lng" => $this->faker->randomFloat(15, -180, 180),
            "poi_address" => $this->faker->address(),
            "poi_location_id" => $this->faker->numberBetween(1, $max_location_id),
            "created_at" => now(),
            "updated_at" => now(),
            //
        ];
    }
    // $table->string('poi_name');
    // $table->string('type');
    // $table->double('gps_lat');
    // $table->double('gps_lng');
    // $table->string('address');
    // $table->bigInteger('location_id')->unsigned();
    // $table->foreign('location_id')->references('location_id')->on('locations');
    // $table->timestamps();

}
