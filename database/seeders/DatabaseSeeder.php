<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Orders;
use App\Models\Sales;
use App\Models\Branch_store;
use App\Models\PointOfInterest;
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
        User::factory()->count(10)->create([
            'role_name' => 'ceo',
        ]);
        User::factory()->count(10)->create([
            'role_name' => 'supervisor',
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

        // Insert VIP Users
        Log::info('Inserting VIP users...');
        $VIPUserDatas = [
            'apipol@myorder.ai' => [ 'password' => '$2y$12$yZGHjoBtTLZswV4/56.k.eQIMWiG001s4kx.XdewhXVNMx0EIKyCu', 'user_status' => 'normal', 'role_name' => 'ceo', 'name' => 'Apipol Sukgler'],
            'phattharaphon@myorder.ai' => [ 'password' => '$2y$12$OqYASD1zWOQan3ZIIlHDbOGD6nvRmh6JBsrSHx.1rfqvACDI2yMhO', 'user_status' => 'normal', 'role_name' => 'ceo', 'name' => 'Phattharaphon'],
            'sompob@myorder.ai' => [ 'password' => '$2y$12$C.ZQo9iOCN7euUSGIxRfA.VpKHjG1y1Oe1QNDYlUSw.XIbWt3Ca.m', 'user_status' => 'normal', 'role_name' => 'ceo', 'name' => 'Sompob'],
        ];

        foreach ($VIPUserDatas as $email => $data) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                User::create([
                    'name' => $data['name'],
                    'email' => $email,
                    'password' => $data['password'],
                    'user_status' => $data['user_status'],
                    'role_name' => $data['role_name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info("VIP user {$email} inserted.");
            } else {
                Log::info("VIP user {$email} already exists.");
            }
        }

        // Create points of interest
        // Log::info('Creating points of interest...');
        // PointOfInterest::factory(100)->create();
        // Log::info('Points of interest created.');

        // Assign at least one branch to each sales user
        Log::info('Assigning branches to sales users...');
        $salesUsers = User::where('role_name', 'sale')->get();

        $locations = DB::table('locations')->inRandomOrder();
        $branchIndex = 1;
        foreach ($salesUsers as $user) {
            // random location
            // make new poi 
            $poi = PointOfInterest::factory()->create([
                'poi_name' => "Branch {$branchIndex}",
                'poi_type' => 'branch',
                'poi_gps_lat' => fake()->latitude(),
                'poi_gps_lng' => fake()->longitude(),
                'poi_address' => fake()->address(),
                'poi_location_id' => $locations->first()->location_id,
            ]);
            Branch_store::factory()->create([
                'bs_name' => "Branch {$branchIndex}",
                'bs_detail' => fake()->text(),
                'bs_address' => $poi->poi_address,
                'bs_poi_id' => $poi->poi_id,
                'bs_manager' => $user->user_id,
            ]);
            Log::info("Branch assigned to sales user with ID: {$user->user_id}");
            $branchIndex++;
        }
        Log::info('Each salesperson assigned at least one branch.');

        // Create additional branches in bulk
        // Log::info('Creating additional branches...');
        // Branch_store::factory(100)->create();
        Log::info('Additional branches created.');

        // Create sales data for branches efficiently
        Log::info('Creating sales data for branches...');
        $branchIds = Branch_store::pluck('bs_id')->toArray();

        foreach ($branchIds as $branchId) {
            for ($month = 0; $month < 12; $month++) {
                Sales::factory()->create([
                    'sales_branch_id' => $branchId,
                    'created_at' => now()->subMonthsNoOverflow($month)->startOfMonth()->setTimezone('UTC'),
                    'sales_month' => now()->subMonthsNoOverflow($month)->startOfMonth()->setTimezone('UTC'),
                ]);
            }
        }
        Log::info('Sales data inserted.');

        Log::info('Database seeding completed.');
    }
}