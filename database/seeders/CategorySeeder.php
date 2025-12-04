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

        $clothing = Category::create([
            'name' => 'Clothing',
            'level' => '1',
            'parent_id' => null,
        ]);

        $toys = Category::create([
            'name' => 'Toys',
            'level' => '1',
            'parent_id' => null,
        ]);

        $healthcare = Category::create([
            'name' => 'Healthcare',
            'level' => '1',
            'parent_id' => null,
        ]);

        $nursery = Category::create([
            'name' => 'Nursery',
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

        // Level 2 Categories (Sub Categories for Clothing)
        Category::create([
            'name' => 'Bodysuits',
            'level' => '2',
            'parent_id' => $clothing->id,
        ]);

        Category::create([
            'name' => 'Sleepwear',
            'level' => '2',
            'parent_id' => $clothing->id,
        ]);

        Category::create([
            'name' => 'Outerwear',
            'level' => '2',
            'parent_id' => $clothing->id,
        ]);

        // Level 2 Categories (Sub Categories for Healthcare)
        Category::create([
            'name' => 'Diapers',
            'level' => '2',
            'parent_id' => $healthcare->id,
        ]);

        Category::create([
            'name' => 'Wipes',
            'level' => '2',
            'parent_id' => $healthcare->id,
        ]);

        Category::create([
            'name' => 'Bath & Skincare',
            'level' => '2',
            'parent_id' => $healthcare->id,
        ]);

        Category::create([
            'name' => 'Medicine & Vitamins',
            'level' => '2',
            'parent_id' => $healthcare->id,
        ]);

        // Level 2 Categories (Sub Categories for Toys)
        Category::create([
            'name' => 'Soft Toys',
            'level' => '2',
            'parent_id' => $toys->id,
        ]);

        Category::create([
            'name' => 'Educational Toys',
            'level' => '2',
            'parent_id' => $toys->id,
        ]);

        Category::create([
            'name' => 'Activity Toys',
            'level' => '2',
            'parent_id' => $toys->id,
        ]);

        // Level 2 Categories (Sub Categories for Nursery)
        Category::create([
            'name' => 'Cribs & Bedding',
            'level' => '2',
            'parent_id' => $nursery->id,
        ]);

        Category::create([
            'name' => 'Baby Monitors',
            'level' => '2',
            'parent_id' => $nursery->id,
        ]);

        Category::create([
            'name' => 'Feeding Accessories',
            'level' => '2',
            'parent_id' => $nursery->id,
        ]);
    }
}
