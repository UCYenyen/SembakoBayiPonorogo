<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            BrandsSeeder::class,
            ProductSeeder::class,
            PaymentSeeder::class,
            DeliverySeeder::class,
            AddressSeeder::class,
        ]);
    }
}
