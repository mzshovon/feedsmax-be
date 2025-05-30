<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quarantine_policies')->insert([
            [
                'name' => 'Feedback Accept Day Policy',
                'call_object_notation' => 'feedback_accept_day_policy',
                'order' => 1,
                'args' => json_encode([1, 20]),
                'update_params' => json_encode([0, 1]),
                'definition' => 'Policy to define acceptable days for feedback submission and review process',
                'event_id' => 1,
                'status' => true,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Feedback Reject Day Policy',
                'call_object_notation' => 'feedback_reject_day_policy',
                'order' => 2,
                'args' => json_encode([5, 30]),
                'update_params' => json_encode([1, 0]),
                'definition' => 'Policy to define rejection criteria and timeframe for feedback submissions',
                'event_id' => 1,
                'status' => true,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'User Session Count Policy',
                'call_object_notation' => 'user_session_count_policy',
                'order' => 3,
                'args' => json_encode([3, 15]),
                'update_params' => json_encode([0, 1]),
                'definition' => 'Policy to manage maximum user session counts and concurrent access limits',
                'event_id' => 2,
                'status' => true,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Password Reset Attempt Policy',
                'call_object_notation' => 'password_reset_attempt_policy',
                'order' => 4,
                'args' => json_encode([2, 10]),
                'update_params' => json_encode([1, 1]),
                'definition' => 'Policy to limit password reset attempts within specified timeframe',
                'event_id' => 2,
                'status' => false,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'File Upload Size Policy',
                'call_object_notation' => 'file_upload_size_policy',
                'order' => 5,
                'args' => json_encode([10, 100]),
                'update_params' => json_encode([0, 0]),
                'definition' => 'Policy to enforce file upload size restrictions and validation rules',
                'event_id' => 3,
                'status' => true,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Email Notification Rate Policy',
                'call_object_notation' => 'email_notification_rate_policy',
                'order' => 6,
                'args' => json_encode([1, 5]),
                'update_params' => json_encode([1, 0]),
                'definition' => 'Policy to control email notification frequency and rate limiting',
                'event_id' => 3,
                'status' => true,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Account Lockout Duration Policy',
                'call_object_notation' => 'account_lockout_duration_policy',
                'order' => 7,
                'args' => json_encode([60, 1440]),
                'update_params' => json_encode([0, 1]),
                'definition' => 'Policy to define account lockout duration after failed login attempts',
                'event_id' => 4,
                'status' => true,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'API Rate Limit Policy',
                'call_object_notation' => 'api_rate_limit_policy',
                'order' => 8,
                'args' => json_encode([100, 1000]),
                'update_params' => json_encode([1, 1]),
                'definition' => 'Policy to manage API request rate limits and throttling mechanisms',
                'event_id' => 4,
                'status' => false,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}