<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointOfInterest>
 */
class PointofinterestFactory extends Factory
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
        // [
        //     'poi_name' => $feature['properties']['name'],
        //     'poi_type' => $poiType,
        //     'poi_gps_lat' => $feature['geometry']['coordinates'][1],
        //     'poi_gps_lng' => $feature['geometry']['coordinates'][0],
        //     'poi_address' => $feature['properties']['address'] ?? null,
        //     'poi_location_id' => null,
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ];

        // get all distinct poi_type from point_of_interest_type table
        $poi_type = DB::table('point_of_interest_type')->pluck('poit_type')->toArray();
        return [
            "poi_name" => $this->faker->name(),
            "poi_type" => $this->faker->randomElement($poi_type),
            "poi_gps_lat" => $this->faker->randomFloat(15, -180, 180),
            "poi_gps_lng" => $this->faker->randomFloat(15, -180, 180),
            "poi_address" => $this->faker->address(),
            "poi_location_id" => $this->faker->numberBetween(1, $max_location_id),
            "created_at" => now(),
            "updated_at" => now(),
            //
        ];
    }
<<<<<<< HEAD
    // $table->string('poi_name');
    // $table->string('type');
    // $table->double('gps_lat');
    // $table->double('gps_lng');
    // $table->string('address');
    // $table->bigInteger('location_id')->unsigned();
    // $table->foreign('location_id')->references('location_id')->on('locations');
    // $table->timestamps();

=======
>>>>>>> origin/mos
}