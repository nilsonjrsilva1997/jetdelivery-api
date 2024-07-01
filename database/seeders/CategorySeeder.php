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

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
