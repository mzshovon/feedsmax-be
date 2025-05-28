<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $channels = [
            [
                'tag' => 'website-feedback',
                'name' => 'Website Feedback Channel',
                'retry' => json_encode([
                    'max_attempts' => 3,
                    'delay_seconds' => 60,
                    'backoff_multiplier' => 2
                ]),
                'status' => 1,
                'pagination' => 10,
                'created_by' => 1,
                'updated_by' => null,
                'client_id' => 1,
                'theme_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tag' => 'mobile-app-survey',
                'name' => 'Mobile App Survey',
                'retry' => json_encode([
                    'max_attempts' => 5,
                    'delay_seconds' => 30,
                    'backoff_multiplier' => 1.5
                ]),
                'status' => 1,
                'pagination' => 15,
                'created_by' => 1,
                'updated_by' => 1,
                'client_id' => 2,
                'theme_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tag' => 'customer-support',
                'name' => 'Customer Support Feedback',
                'retry' => null,
                'status' => 0,
                'pagination' => 5,
                'created_by' => 2,
                'updated_by' => null,
                'client_id' => 1,
                'theme_id' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tag' => 'product-review',
                'name' => 'Product Review Portal',
                'retry' => json_encode([
                    'max_attempts' => 2,
                    'delay_seconds' => 120,
                    'backoff_multiplier' => 3
                ]),
                'status' => 1,
                'pagination' => 20,
                'created_by' => 1,
                'updated_by' => 2,
                'client_id' => 3,
                'theme_id' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'tag' => 'email-campaign',
                'name' => 'Email Campaign Response',
                'retry' => json_encode([
                    'max_attempts' => 4,
                    'delay_seconds' => 45,
                    'backoff_multiplier' => 2.5
                ]),
                'status' => 1,
                'pagination' => 12,
                'created_by' => 2,
                'updated_by' => null,
                'client_id' => 2,
                'theme_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('channels')->insert($channels);
        
        $this->command->info('Channels table seeded successfully with ' . count($channels) . ' records.');
    }
}