<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $faker = Faker::create();
        
        $subscriptions = [];
        
        // Get all client IDs
        $clientIds = DB::table('clients')->pluck('id')->toArray();
        
        // Get all package IDs
        $packageIds = DB::table('packages')->pluck('id')->toArray();
        
        foreach ($clientIds as $clientId) {
            // Randomly select a package for each client
            $packageId = $faker->randomElement($packageIds);
            
            // Generate random dates within the last year
            $startDate = Carbon::now()->subDays($faker->numberBetween(1, 365));
            $endDate = Carbon::parse($startDate)->addYear();
            
            $subscriptions[] = [
                'client_id' => $clientId,
                'package_id' => $packageId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $faker->randomElement(['active', 'pending']),
                'is_auto_renew' => $faker->boolean(70), // 70% chance of auto-renew
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('subscriptions')->insert($subscriptions);
    }
}
