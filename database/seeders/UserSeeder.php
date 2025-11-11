<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() === 0) {
            $faker = Faker::create();

            $users = [];

            // Get all client IDs
            $clientIds = DB::table('clients')->pluck('id')->toArray();

            foreach ($clientIds as $clientId) {
                // Create an admin user for each client
                $users[] = [
                    'client_id' => $clientId,
                    'username' => 'admin_' . $clientId,
                    'email' => 'admin_' . $clientId . '@example.com',
                    'password_hash' => Hash::make('password123'),
                    'role' => 'admin',
                    'is_active' => true,
                    'last_login' => $faker->dateTimeThisMonth(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Create 1-3 additional users for each client
                $numUsers = $faker->numberBetween(1, 3);
                for ($i = 0; $i < $numUsers; $i++) {
                    $role = $faker->randomElement(['manager', 'user']);

                    $users[] = [
                        'client_id' => $clientId,
                        'username' => $faker->userName() . '_' . $clientId . '_' . $i,
                        'email' => $faker->unique()->safeEmail(),
                        'password_hash' => Hash::make('password123'),
                        'role' => $role,
                        'is_active' => $faker->boolean(80), // 80% chance of being active
                        'last_login' => $faker->optional(70)->dateTimeThisMonth(), // 70% chance of having logged in
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DB::table('users')->insert($users);
        }
    }
}
