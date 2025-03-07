<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
use App\Models\Point_of_interest; // Ensure you have this model and its factory
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch_store>
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
        // Retrieve all existing Point of Interest IDs.
        $poiIds = DB::table('point_of_interests')->pluck('poi_id')->toArray();
        if (empty($poiIds)) {
            // If no point of interest exists, create one.
            $poi = Point_of_interest::factory()->create();
            $poiId = $poi->getPOIId();
        } else {
            $poiId = fake()->randomElement($poiIds);
        }

        // Retrieve all existing User IDs for branch managers.
        $userIds = DB::table('users')->pluck('user_id')->toArray();
        $managerId = null;
        if (empty($userIds)) {
            // If no user exists, create one.
            $manager = User::factory()->create();
            $managerId = $manager->getUserId();
        } else {
            $managerId = fake()->randomElement($userIds);
        }

        return [
            'bs_name'    => fake()->name(),
            'bs_detail'  => fake()->sentence(),
            'bs_address' => fake()->address(),
            'bs_poi_id'  => $poiId,
            'bs_manager' => $managerId,
        ];
    }
}
