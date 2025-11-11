<?php

namespace Database\Seeders;

use App\Models\ClientPortal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ClientPortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (ClientPortal::count() === 0) {
            $clientPortals = [];

            // Get all client IDs
            $clientIds = DB::table('clients')->pluck('id')->toArray();

            foreach ($clientIds as $clientId) {
                // Get client company name
                $companyName = DB::table('clients')
                    ->where('id', $clientId)
                    ->value('company_name');

                // Create a sanitized subdomain based on company name
                $subdomain = Str::slug(strtolower($companyName));

                $clientPortals[] = [
                    'client_id' => $clientId,
                    'subdomain' => $subdomain,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('client_portals')->insert($clientPortals);
        }
    }
}
