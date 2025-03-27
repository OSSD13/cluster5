<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Orders;
use App\Models\Sales;
use App\Models\Branch_store;
use App\Models\Point_of_interests;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Log::info('Starting database seeding.');

        // Create managers
        Log::info('Creating managers...');
        User::factory()->count(20)->create([
            'role_name' => fake()->randomElement(['ceo', 'supervisor']),
        ]);
        Log::info('Managers created.');

        // Create sales
        Log::info('Creating sales...');
        User::factory(100)->create(['role_name' => 'sale']);
        Log::info('Sales created.');

        // Create specific users
        Log::info('Creating specific users...');
        $emails = [
            'ttawan475@gmail.com' => ['name' => 'tawan', 'role_name' => 'ceo'],
            'torlap.ritchai@gmail.com' => ['name' => 'jeng', 'role_name' => 'sale'],
        ];
        $existingUsers = User::whereIn('email', array_keys($emails))->pluck('email')->toArray();
        $newUsers = [];

        foreach ($emails as $email => $data) {
            if (!in_array($email, $existingUsers)) {
                $newUsers[] = [
                    'name' => $data['name'],
                    'email' => $email,
                    'password' => Hash::make('123456'),
                    'user_status' => 'normal',
                    'role_name' => $data['role_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if ($newUsers) {
            User::insert($newUsers);
            Log::info('Specific users inserted.');
        }

        // Create users from email list
        Log::info('Creating users from email list...');
        $emailList = [
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
        $existingEmails = User::whereIn('email', $emailList)->pluck('email')->toArray();
        $usersToInsert = [];

        foreach ($emailList as $email) {
            if (!in_array($email, $existingEmails)) {
                $usersToInsert[] = [
                    'name' => explode('@', $email)[0],
                    'email' => $email,
                    'password' => Hash::make('123456'),
                    'user_status' => 'normal',
                    'role_name' => 'ceo',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        if ($usersToInsert) {
            User::insert($usersToInsert);
            Log::info('Users from email list inserted.');
        }

        // Create points of interest
        Log::info('Creating points of interest...');
        Point_of_interests::factory(100)->create();
        Log::info('Points of interest created.');

        // Assign at least one branch to each sales user
        Log::info('Assigning branches to sales users...');
        $salesUsers = User::where('role_name', 'sale')->get();

        foreach ($salesUsers as $user) {
            Branch_store::factory()->create([
                'bs_manager' => $user->user_id,
            ]);
            Log::info("Branch assigned to sales user with ID: {$user->user_id}");
        }
        Log::info('Each salesperson assigned at least one branch.');

        // Create additional branches in bulk
        Log::info('Creating additional branches...');
        Branch_store::factory(100)->create();
        Log::info('Additional branches created.');

        // Create sales data for branches efficiently
        Log::info('Creating sales data for branches...');
        $branchIds = Branch_store::pluck('bs_id')->toArray();

        foreach ($branchIds as $branchId) {
            for ($month = 0; $month < 12; $month++) {
                Sales::factory()->create([
                    'sales_branch_id' => $branchId,
                    'created_at' => now()->subMonths($month),
                    'sales_month' => now()->subMonths($month),
                ]);
            }
        }
        Log::info('Sales data inserted.');

        Log::info('Database seeding completed.');
    }
}
