<?php

namespace Database\Factories;

use App\Models\Point_of_interests;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;
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
        // Retrieve all existing User IDs for branch managers.
        $userIds = DB::table('users')->where('role_name', '<>', 'ceo')->pluck('user_id')->toArray();
        $managerId = null;
        if (empty($userIds)) {
            // If no user exists, create one.
            $manager = User::factory()->create();
            $managerId = $manager->getUserId();
        } else {
            $managerId = fake()->randomElement($userIds);
        }


        // Retrieve all existing POI IDs.
        $POIIds = DB::table('point_of_interests')->pluck('poi_id')->toArray();
        $POIId = fake()->randomElement($POIIds);


        return [
            'bs_name'    => fake()->name(),
            'bs_detail'  => fake()->sentence(),
            'bs_address' => fake()->address(),
            'bs_poi_id'  => $POIId,
            'bs_manager' => $managerId,
        ];
    }
}
