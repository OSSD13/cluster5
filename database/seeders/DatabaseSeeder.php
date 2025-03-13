<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Orders;
use App\Models\Sales;
use App\Models\Branch_store;
use App\Models\Point_of_interest;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, create at least one manager (CEO or Supervisor)
        User::factory()->count(20)->state(fn() => [
            'role_name' => fake()->randomElement(['ceo', 'supervisor']),
        ])->create();

        // Then create sales, ensuring they can get assigned managers
        User::factory(100)->create(['role_name' => 'sale']);
        // create ceo user with mail ttawan475@gmail.com password 123456
        User::create([
            'name' => 'tawan',
            'email' => 'ttawan475@gmail.com',
            'password' => bcrypt('123456'),
            'user_status' => 'normal',
            'role_name' => 'ceo',
        ]);

        Point_of_interest::factory(100)->create();
        Branch_store::factory(100)->create();
        Sales::factory(100)->create();
        Orders::factory(100)->create();

        // User::factory(10)->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
