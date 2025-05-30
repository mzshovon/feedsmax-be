<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PackageSeeder::class,
            UsageLimitSeeder::class,
            ClientSeeder::class,
            ClientPortalSeeder::class,
            SubscriptionSeeder::class,
            UsageTrackingSeeder::class,
            BillingSeeder::class,
            EventsSeeder::class,
            ThemeSeeder::class,
            UserSeeder::class,
        ]);
    }
}
