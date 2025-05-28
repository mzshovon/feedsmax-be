<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $themes = [
            [
                'name' => 'light',
                'value' => json_encode([
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#EF4444',
                    'background_color' => '#FFFFFF',
                    'text_color' => '#1F2937',
                    'border_color' => '#E5E7EB',
                    'button_color' => '#10B981',
                    'button_text_color' => '#FFFFFF',
                    'card_background' => '#F9FAFB',
                    'shadow_color' => 'rgba(0, 0, 0, 0.1)',
                    'font_family' => 'Inter, sans-serif',
                    'font_size' => '14px',
                    'border_radius' => '8px',
                    'spacing' => '16px'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'dark',
                'value' => json_encode([
                    'primary_color' => '#6366F1',
                    'secondary_color' => '#F59E0B',
                    'background_color' => '#1F2937',
                    'text_color' => '#F9FAFB',
                    'border_color' => '#374151',
                    'button_color' => '#059669',
                    'button_text_color' => '#FFFFFF',
                    'card_background' => '#111827',
                    'shadow_color' => 'rgba(0, 0, 0, 0.3)',
                    'font_family' => 'Roboto, sans-serif',
                    'font_size' => '14px',
                    'border_radius' => '12px',
                    'spacing' => '20px'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'business',
                'value' => json_encode([
                    'primary_color' => '#1E40AF',
                    'secondary_color' => '#DC2626',
                    'background_color' => '#F8FAFC',
                    'text_color' => '#0F172A',
                    'border_color' => '#CBD5E1',
                    'button_color' => '#1D4ED8',
                    'button_text_color' => '#FFFFFF',
                    'card_background' => '#FFFFFF',
                    'shadow_color' => 'rgba(30, 64, 175, 0.1)',
                    'font_family' => 'Open Sans, sans-serif',
                    'font_size' => '15px',
                    'border_radius' => '6px',
                    'spacing' => '18px',
                    'header_height' => '60px',
                    'sidebar_width' => '280px'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'modern',
                'value' => json_encode([
                    'primary_color' => '#059669',
                    'secondary_color' => '#7C3AED',
                    'background_color' => '#ECFDF5',
                    'text_color' => '#064E3B',
                    'border_color' => '#A7F3D0',
                    'button_color' => '#047857',
                    'button_text_color' => '#FFFFFF',
                    'card_background' => '#FFFFFF',
                    'shadow_color' => 'rgba(5, 150, 105, 0.15)',
                    'font_family' => 'Poppins, sans-serif',
                    'font_size' => '14px',
                    'border_radius' => '10px',
                    'spacing' => '16px',
                    'accent_color' => '#F59E0B',
                    'success_color' => '#10B981',
                    'warning_color' => '#F59E0B',
                    'error_color' => '#EF4444'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('themes')->insert($themes);
        
        $this->command->info('Themes table seeded successfully with ' . count($themes) . ' records.');
    }
}