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
        User::factory(100)->create();
        Orders::factory(100)->create();
        Sales::factory(100)->create();
        Branch_store::factory(100)->create();
        Point_of_interest::factory(100)->create();

        // User::factory(10)->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
