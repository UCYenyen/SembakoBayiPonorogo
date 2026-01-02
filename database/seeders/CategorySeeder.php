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
        $food = Category::create([ //1
            'name' => 'Makanan',
            'level' => '1',
            'parent_id' => null,
        ]);

        $tools = Category::create([//2
            'name' => 'Alat',
            'level' => '1',
            'parent_id' => null,
        ]);

        // Level 2 Categories (Sub Categories for Foods)
        $MakananBeratCategory = Category::create([//3
            'name' => 'Makanan Berat',
            'level' => '2',
            'parent_id' => $food->id,
        ]);

        $PelengkapMakanan = Category::create([//4
            'name' => 'Pelengkap Makanan',
            'level' => '2',
            'parent_id' => $food->id,
        ]);

        $CamilanCategory = Category::create([//5
            'name' => 'Camilan',
            'level' => '2',
            'parent_id' => $food->id,
        ]);

        // Level 3 Categories (Sub-Sub Categories for Cemilan)
        Category::create([//6
            'name' => 'Camilan Bayi',
            'level' => '3',
            'parent_id' => $CamilanCategory->id,
        ]);

        Category::create([//7
            'name' => 'Camilan Anak',
            'level' => '3',
            'parent_id' => $CamilanCategory->id,
        ]);

        Category::create([//8
            'name' => 'Camilan Umum',
            'level' => '3',
            'parent_id' => $CamilanCategory->id,
        ]);

        // Level 2 Categories (Sub Categories for Tools)
        $AlatMasakCategory = Category::create([//9
            'name' => 'Alat Masak',
            'level' => '2',
            'parent_id' => $tools->id,
        ]);

        $AlatMakanCategory = Category::create([//10
            'name' => 'Alat Makan',
            'level' => '2',
            'parent_id' => $tools->id,
        ]);

        $AlatMinumCategory = Category::create([//11
            'name' => 'Alat Minum',
            'level' => '2',
            'parent_id' => $tools->id,
        ]);

        // Level 3 Categories (Sub-Sub Categories)
        Category::create([//12
            'name' => 'Non Elektrik',
            'level' => '3',
            'parent_id' => $AlatMasakCategory->id,
        ]);

         Category::create([//13
            'name' => 'Elektrik',
            'level' => '3',
            'parent_id' => $AlatMasakCategory->id,
        ]);

        Category::create([//14
            'name' => 'Non Elektrik',
            'level' => '3',
            'parent_id' => $AlatMakanCategory->id,
        ]);

         Category::create([//15
            'name' => 'Elektrik',
            'level' => '3',
            'parent_id' => $AlatMakanCategory->id,
        ]);

        Category::create([//16
            'name' => 'Non Elektrik',
            'level' => '3',
            'parent_id' => $AlatMinumCategory->id,
        ]);

         Category::create([//17
            'name' => 'Elektrik',
            'level' => '3',
            'parent_id' => $AlatMinumCategory->id,
        ]);
    }
}
