<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_poi_id' => fake()-> randomDigit(),
            'province' => fake()-> country(),
            'district' => fake()-> citySuffix() ,
            'sub_district' => fake()-> cityPrefix(),
            'postal_code' => fake()-> postcode() 
            //
        ];
    }
}
