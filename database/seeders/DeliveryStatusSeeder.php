<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('delivery_statuses')->insert([
            ['status_name' => 'Pending Courier'],
            ['status_name' => 'Courier Accepted Delivery'],
            ['status_name' => 'En Route'],
            ['status_name' => 'Delivery Completed'],
        ]);
    }
}
