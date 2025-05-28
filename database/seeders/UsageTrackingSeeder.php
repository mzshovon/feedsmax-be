<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class UsageTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $usageRecords = [];
        
        // Get all subscriptions with client IDs
        $subscriptions = DB::table('subscriptions')
            ->select('id', 'client_id', 'start_date')
            ->where('status', 'active')
            ->get();
            
        // Feature names to track usage for
        $features = ['api_calls', 'reports'];
        
        foreach ($subscriptions as $subscription) {
            // Generate usage records for the last 30 days
            for ($i = 0; $i < 30; $i++) {
                $date = Carbon::now()->subDays($i);
                
                // Skip dates before subscription started
                if ($date < Carbon::parse($subscription->start_date)) {
                    continue;
                }
                
                // Create usage for each feature
                foreach ($features as $feature) {
                    // Random usage count based on feature
                    $usageCount = ($feature === 'api_calls') 
                        ? $faker->numberBetween(5, 100) 
                        : $faker->numberBetween(0, 3);
                    
                    if ($usageCount > 0) {
                        $usageRecords[] = [
                            'client_id' => $subscription->client_id,
                            'subscription_id' => $subscription->id,
                            'feature_name' => $feature,
                            'usage_date' => $date,
                            'usage_count' => $usageCount,
                            'created_at' => $date,
                        ];
                    }
                }
            }
        }
        
        // Insert in chunks to avoid memory issues
        foreach (array_chunk($usageRecords, 100) as $chunk) {
            DB::table('usage_tracking')->insert($chunk);
        }
    }
}
