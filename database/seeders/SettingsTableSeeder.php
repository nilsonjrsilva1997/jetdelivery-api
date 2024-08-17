<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'base_rate',
                'display_name' => 'Taxa Base',
                'value' => '5.00',
            ],
            [
                'key' => 'rate_per_km',
                'display_name' => 'Taxa por KM',
                'value' => '2.00',
            ],
            [
                'key' => 'management_fee_percentage',
                'display_name' => 'Taxa de Gestão Percentual',
                'value' => '10',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
