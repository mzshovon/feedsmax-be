<?php

namespace Database\Seeders;

use App\Models\Billing;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class BillingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Billing::count() > 0) {
            $faker = Faker::create();

            $billingRecords = [];

            // Package pricing (package_id => price)
            $packagePricing = [
                1 => 29.99,  // PriMax
                2 => 99.99,  // InteriMax
                3 => 199.99, // PremiuMax
                4 => 499.99, // SupreMax
                5 => 0.00    // FreemiuMax
            ];

            // Get all subscriptions with client IDs and package IDs
            $subscriptions = DB::table('subscriptions')
                ->select('id', 'client_id', 'package_id', 'start_date')
                ->get();

            foreach ($subscriptions as $subscription) {
                // Skip billing for free subscriptions
                if ($packagePricing[$subscription->package_id] == 0) {
                    continue;
                }

                // Generate billing date (1st of the month after subscription starts)
                $startDate = Carbon::parse($subscription->start_date);
                $billingDate = Carbon::create($startDate->year, $startDate->month, 1)->addMonth();

                // Create 3 monthly billing records for each subscription
                for ($i = 0; $i < 3; $i++) {
                    $currentBillingDate = (clone $billingDate)->addMonths($i);
                    $dueDate = (clone $currentBillingDate)->addDays(15);

                    // Determine if paid based on due date
                    $isPaid = $dueDate->isPast();
                    $status = $isPaid ? 'paid' : 'unpaid';
                    $paymentDate = $isPaid ? $faker->dateTimeBetween($currentBillingDate, $dueDate) : null;
                    $paymentMethod = $isPaid ? $faker->randomElement(['credit_card', 'bank_transfer', 'paypal']) : null;

                    $billingRecords[] = [
                        'client_id' => $subscription->client_id,
                        'subscription_id' => $subscription->id,
                        'amount' => $packagePricing[$subscription->package_id],
                        'billing_date' => $currentBillingDate,
                        'due_date' => $dueDate,
                        'status' => $status,
                        'payment_method' => $paymentMethod,
                        'payment_date' => $paymentDate,
                        'created_at' => $currentBillingDate,
                        'updated_at' => $isPaid ? $paymentDate : $currentBillingDate,
                    ];
                }
            }

            DB::table('billings')->insert($billingRecords);
        }
    }
}
