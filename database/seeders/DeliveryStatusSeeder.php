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
        // Lista de statuses que você deseja adicionar
        $statuses = [
            'Pending Courier',
            'Courier Accepted Delivery',
            'En Route',
            'Delivery Completed',
            'Collected', // Adicionando o novo status
        ];

        foreach ($statuses as $status) {
            // Verifica se o status já existe antes de inserir
            if (!DB::table('delivery_statuses')->where('status_name', $status)->exists()) {
                DB::table('delivery_statuses')->insert([
                    'status_name' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
