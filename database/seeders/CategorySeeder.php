<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Italiana'],
            ['name' => 'Chinesa'],
            ['name' => 'Mexicana'],
            ['name' => 'Japonesa'],
            ['name' => 'Indiana'],
            ['name' => 'Francesa'],
            ['name' => 'Tailandesa'],
            ['name' => 'Grega'],
            ['name' => 'Espanhola'],
            ['name' => 'Americana'],
        ];

        // Inserir ou atualizar as categorias na tabela
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']], // Condição para verificar se o registro já existe
                $category // Dados para criar ou atualizar o registro
            );
        }
    }
}
