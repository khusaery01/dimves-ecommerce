<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Dimsum',
            'description' => 'Aneka dimsum premium',
            'image' => 'dimsum.jpg',
        ]);

        Category::create([
            'name' => 'Minuman',
            'description' => 'Aneka minuman segar',
            'image' => 'minuman.jpg',
        ]);
    }
}