<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Orders;
use App\Models\Sales;
use App\Models\Branch_store;
use App\Models\Point_of_interests;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, create at least one manager (CEO or   )
        User::factory()->count(20)->state(fn() => [
            'role_name' => fake()->randomElement(['ceo', 'supervisor']),
        ])->create();

        // Then create sales, ensuring they can get assigned managers
        User::factory(100)->create(['role_name' => 'sale']);
        $testUserJeng = User::where('email', '=', value: 'ttawan475@gmail.com')->first();
        if (!$testUserJeng) {
            User::create([
                'name' => 'tawan',
                'email' => 'ttawan475@gmail.com',
                'password' => bcrypt('123456'),
                'user_status' => 'normal',
                'role_name' => 'ceo',
            ]);
        }

        $testUserJeng = User::where('email', '=', value: 'torlap.ritchai@gmail.com')->first();
        if (!$testUserJeng) {
            User::create([
                'name' => 'jeng',
                'email' => 'torlap.ritchai@gmail.com',
                'password' => bcrypt('123456'),
                'user_status' => 'normal',
                'role_name' => 'sale',
            ]);
        }

        // create if not exist these mails with password 123456 and role_name = ceo
        // 66160106@go.buu.ac.th
        // 66160082@go.buu.ac.th
        // 66160084@go.buu.ac.th
        // 66160230@go.buu.ac.th
        // 66160229@go.buu.ac.th
        // 66160354@go.buu.ac.th
        // 66160357@go.buu.ac.th
        // 66160358@go.buu.ac.th
        // 66160369@go.buu.ac.th
        // 66160370@go.buu.ac.th

        $emails = [
            '66160106@go.buu.ac.th',
            '66160082@go.buu.ac.th',
            '66160084@go.buu.ac.th',
            '66160230@go.buu.ac.th',
            '66160229@go.buu.ac.th',
            '66160354@go.buu.ac.th',
            '66160357@go.buu.ac.th',
            '66160358@go.buu.ac.th',
            '66160369@go.buu.ac.th',
            '66160370@go.buu.ac.th',
        ];

        foreach ($emails as $email) {
            $user = User::where('email', '=', $email)->first();
            if (!$user) {
                User::create([
                    'name' => explode('@', $email)[0],
                    'email' => $email,
                    'password' => bcrypt('123456'),
                    'user_status' => 'normal',
                    'role_name' => 'ceo',
                ]);
            }
        }

        $emails = [
            '66160106@go.buu.ac.th',
            '66160082@go.buu.ac.th',
            '66160084@go.buu.ac.th',
            '66160230@go.buu.ac.th',
            '66160229@go.buu.ac.th',
            '66160354@go.buu.ac.th',
            '66160357@go.buu.ac.th',
            '66160358@go.buu.ac.th',
            '66160369@go.buu.ac.th',
            '66160370@go.buu.ac.th',
        ];


        // Point_of_interest::factory(100)->create();
        Branch_store::factory(100)->create();
        $branches = Branch_store::all();
        foreach ($branches as $branch) {
            for ($month = 0; $month < 12; $month++) {
                Sales::factory()->create([
                    'sales_branch_id' => $branch->bs_id,
                    'created_at' => now()->subMonths($month),
                    'sales_month' => now()->subMonths($month),
                ]);
            }
        }

        // User::factory(10)->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
