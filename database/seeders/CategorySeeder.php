<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Level 1 Categories (Main Categories)
        $food = Category::create([
            'name' => 'Food',
            'level' => '1',
            'parent_id' => null,
        ]);

        $tools = Category::create([
            'name' => 'Tools',
            'level' => '1',
            'parent_id' => null,
        ]);

        // Level 2 Categories (Sub Categories for Food)
        Category::create([
            'name' => 'Baby Formula',
            'level' => '2',
            'parent_id' => $food->id,
        ]);

        Category::create([
            'name' => 'Baby Cereal',
            'level' => '2',
            'parent_id' => $food->id,
        ]);

        Category::create([
            'name' => 'Purees & Snacks',
            'level' => '2',
            'parent_id' => $food->id,
        ]);

        Category::create([
            'name' => 'Beverages',
            'level' => '2',
            'parent_id' => $food->id,
        ]);

        // Level 2 Categories (Sub Categories for tools)
        Category::create([
            'name' => 'Obeng',
            'level' => '2',
            'parent_id' => $tools->id,
        ]);

        Category::create([
            'name' => 'Palu',
            'level' => '2',
            'parent_id' => $tools->id,
        ]);

        Category::create([
            'name' => 'Penenang',
            'level' => '2',
            'parent_id' => $tools->id,
        ]);
    }
}
