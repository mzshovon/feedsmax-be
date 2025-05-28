<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsageLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usageLimits = [
            // PriMax
            [
                'package_id' => 1,
                'limit_type' => 'daily',
                'limit_value' => 100,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 1,
                'limit_type' => 'monthly',
                'limit_value' => 1000,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // InteriMax
            [
                'package_id' => 2,
                'limit_type' => 'daily',
                'limit_value' => 500,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 2,
                'limit_type' => 'monthly',
                'limit_value' => 10000,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // PremiuMax
            [
                'package_id' => 3,
                'limit_type' => 'daily',
                'limit_value' => 1000,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 3,
                'limit_type' => 'monthly',
                'limit_value' => 25000,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // SupreMax
            [
                'package_id' => 4,
                'limit_type' => 'daily',
                'limit_value' => 5000,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 4,
                'limit_type' => 'monthly',
                'limit_value' => 100000,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // FreemiuMax
            [
                'package_id' => 5,
                'limit_type' => 'daily',
                'limit_value' => 25,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 5,
                'limit_type' => 'monthly',
                'limit_value' => 250,
                'feature_name' => 'api_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Additional features for packages
            [
                'package_id' => 1,
                'limit_type' => 'monthly',
                'limit_value' => 5,
                'feature_name' => 'reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 2,
                'limit_type' => 'monthly',
                'limit_value' => 15,
                'feature_name' => 'reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 3,
                'limit_type' => 'monthly',
                'limit_value' => 50,
                'feature_name' => 'reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 4,
                'limit_type' => 'monthly',
                'limit_value' => 100,
                'feature_name' => 'reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_id' => 5,
                'limit_type' => 'monthly',
                'limit_value' => 2,
                'feature_name' => 'reports',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('usage_limits')->insert($usageLimits);
    }
}
