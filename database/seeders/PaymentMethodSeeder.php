<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Cartão de Crédito',
            ],
            [
                'name' => 'Cartão de Débito',
            ],
            [
                'name' => 'Dinheiro',
            ],
            [
                'name' => 'Pix',
            ],
        ];

        // Inserir os métodos de pagamento na tabela
        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::create($paymentMethod);
        }
    }
}
