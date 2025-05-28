<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'package_name' => 'PriMax',
                'description' => 'Basic package with essential features',
                'is_active' => true,
                'amount' => 1999.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'InteriMax',
                'description' => 'Intermediate package with expanded capabilities',
                'is_active' => true,
                'amount' => 3999.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'PremiuMax',
                'description' => 'Premium package with advanced features',
                'is_active' => true,
                'amount' => 8999.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'SupreMax',
                'description' => 'Superior package with comprehensive solutions',
                'is_active' => true,
                'amount' => 15999.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'package_name' => 'FreemiuMax',
                'description' => 'Free trial package with limited features',
                'is_active' => true,
                'amount' => 00.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('packages')->insert($packages);
    }
}
