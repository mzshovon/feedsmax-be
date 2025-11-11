<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Event::count() === 0) {
            $now = Carbon::now();

            // Sample events data covering different survey types, contexts, and languages
            $events = [
                // NPS Events
                [
                    'type' => 'nps',
                    'name' => 'website-nps',
                    'bucket_id' => 1,
                    'context' => 'visitor',
                    'description' => 'Net Promoter Score survey for website visitors',
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                    'client_id' => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'nps',
                    'name' => 'website-nps-bn',
                    'bucket_id' => 2,
                    'context' => 'visitor',
                    'description' => 'ওয়েবসাইট ভিজিটরদের জন্য নেট প্রমোটার স্কোর সার্ভে',
                    'lang' => 'bn',
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                    'client_id' => 1,
                    'channel_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'nps',
                    'name' => 'subscriber-loyalty',
                    'bucket_id' => 3,
                    'context' => 'subscriber',
                    'description' => 'NPS survey for premium subscribers',
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'client_id' => 2,
                    'channel_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'nps',
                    'name' => 'service-rating',
                    'bucket_id' => 4,
                    'context' => 'rate',
                    'description' => 'Post-service NPS rating survey',
                    'lang' => 'en',
                    'status' => false,
                    'created_by' => 2,
                    'updated_by' => null,
                    'client_id' => 2,
                    'channel_id' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],

                // CSAT Events
                [
                    'type' => 'csat',
                    'name' => 'customer-satisfaction',
                    'bucket_id' => 2,
                    'context' => 'visitor',
                    'description' => 'General customer satisfaction survey',
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                    'client_id' => 2,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'csat',
                    'name' => 'customer-satisfaction-bn',
                    'bucket_id' => 3,
                    'context' => 'subscriber',
                    'description' => 'সাবস্ক্রাইবারদের সন্তুষ্টি জরিপ',
                    'lang' => 'bn',
                    'status' => true,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'client_id' => 1,
                    'channel_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'csat',
                    'name' => 'product-satisfaction',
                    'bucket_id' => 1,
                    'context' => 'rate',
                    'description' => 'Product-specific satisfaction rating',
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                    'client_id' => 3,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],

                // CES Events
                [
                    'type' => 'ces',
                    'name' => 'customer-effort',
                    'bucket_id' => 2,
                    'context' => 'visitor',
                    'description' => 'Measure customer effort in completing tasks',
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                    'client_id' => 1,
                    'channel_id' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'ces',
                    'name' => 'support-interaction',
                    'bucket_id' => 3,
                    'context' => 'subscriber',
                    'description' => 'Effort required for support interactions',
                    'lang' => 'en',
                    'status' => false,
                    'created_by' => 2,
                    'updated_by' => 1,
                    'client_id' => 2,
                    'channel_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'ces',
                    'name' => 'customer-effort-bn',
                    'bucket_id' => 4,
                    'context' => 'rate',
                    'description' => 'সেবা প্রাপ্তিতে গ্রাহকের প্রচেষ্টার পরিমাপ',
                    'lang' => 'bn',
                    'status' => true,
                    'created_by' => 1,
                    'updated_by' => null,
                    'client_id' => 3,
                    'channel_id' => 4,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'ces',
                    'name' => 'website-navigation',
                    'bucket_id' => 1,
                    'context' => 'visitor',
                    'description' => 'Effort required to navigate the website',
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'client_id' => 1,
                    'channel_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],

                // Additional mixed examples
                [
                    'type' => 'nps',
                    'name' => 'mobile-app-nps',
                    'bucket_id' => 3,
                    'context' => 'rate',
                    'description' => 'মোবাইল অ্যাপ্লিকেশনের জন্য NPS সার্ভে',
                    'lang' => 'bn',
                    'status' => false,
                    'created_by' => 1,
                    'updated_by' => null,
                    'client_id' => 2,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'csat',
                    'name' => 'checkout-process',
                    'bucket_id' => 4,
                    'context' => 'visitor',
                    'description' => null, // Testing nullable description
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => null, // Testing nullable created_by
                    'updated_by' => null,
                    'client_id' => 3,
                    'channel_id' => 3,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'type' => 'ces',
                    'name' => 'account-setup',
                    'bucket_id' => 4,
                    'context' => 'subscriber',
                    'description' => 'Effort required for new account setup',
                    'lang' => 'en',
                    'status' => true,
                    'created_by' => 2,
                    'updated_by' => null,
                    'client_id' => 1,
                    'channel_id' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ];

            DB::table('events')->insert($events);

            $this->command->info('Events table seeded successfully with ' . count($events) . ' records.');
        }
    }
}