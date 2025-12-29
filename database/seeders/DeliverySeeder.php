<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $deliveries = [
            ['name' => 'jne', 'courier_code' => 'jne'],
            ['name' => 'jnt',  'coruier_code => jnt'],
            ['name' => 'gosend', 'courier_code => gosend'],
        ];

        foreach ($deliveries as $delivery) {
            Delivery::firstOrCreate($delivery);
        }
    }
}
