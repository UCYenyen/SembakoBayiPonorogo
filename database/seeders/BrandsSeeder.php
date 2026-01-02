<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $brands = [
            ['name' => 'Yummy Bites'],
            ['name' => 'Bumbu Bunda Elia'],
            ['name' => 'Milna'],
            ['name' => 'Baby Safe'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
