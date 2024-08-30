<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending'],
            ['name' => 'Processing'],
            ['name' => 'Shipped'],
            ['name' => 'Delivered'],
            ['name' => 'Cancelled'],
        ];

        // Inserir ou atualizar os status na tabela
        foreach ($statuses as $status) {
            OrderStatus::updateOrCreate(
                ['name' => $status['name']], // Condição para verificar se o registro já existe
                $status // Dados para criar ou atualizar o registro
            );
        }
    }
}
