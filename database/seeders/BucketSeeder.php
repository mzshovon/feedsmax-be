<?php

namespace Database\Seeders;

use App\Models\Bucket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BucketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Bucket::count() === 0) {
            DB::table('buckets')->insert([
                [
                    'name' => 'Premium Storage',
                    'description' => 'High-performance storage bucket for premium users with enhanced features and priority support.',
                    'status' => 1,
                    'quota' => 107374182400, // 100 GB in bytes
                    'served' => 32212254720, // 30 GB in bytes
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Basic Storage',
                    'description' => 'Standard storage bucket for regular users with basic functionality.',
                    'status' => 1,
                    'quota' => 53687091200, // 50 GB in bytes
                    'served' => 16106127360, // 15 GB in bytes
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Archive Bucket',
                    'description' => 'Long-term storage solution for archival data with cost-effective pricing.',
                    'status' => 0,
                    'quota' => 1073741824000, // 1 TB in bytes
                    'served' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Development Bucket',
                    'description' => 'Testing and development environment storage for application development.',
                    'status' => 1,
                    'quota' => 21474836480, // 20 GB in bytes
                    'served' => 5368709120, // 5 GB in bytes
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Backup Storage',
                    'description' => null,
                    'status' => 1,
                    'quota' => 536870912000, // 500 GB in bytes
                    'served' => 161061273600, // 150 GB in bytes
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}