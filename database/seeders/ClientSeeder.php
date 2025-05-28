<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $clients = [];
        
        for ($i = 0; $i < 10; $i++) {
            $clients[] = [
                'company_tag' => $faker->uuid(),
                'company_name' => $faker->company(),
                'contact_name' => $faker->name(),
                'email' => $faker->unique()->companyEmail(),
                'phone' => $faker->numerify('##########'),
                'address' => $faker->address(),
                'client_key' => $faker->uuid(),
                'client_secret' => $faker->md5(),
                'status' => $faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('clients')->insert($clients);
    }
}
