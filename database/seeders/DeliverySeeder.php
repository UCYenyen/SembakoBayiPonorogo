<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $deliveries = [
            ['name' => 'JNE', 'courier_code' => 'jne'],
            ['name' => 'J&T', 'courier_code' => 'jnt'],
            ['name' => 'GoSend', 'courier_code' => 'gosend'],
        ];

        foreach ($deliveries as $delivery) {
            Delivery::create($delivery);
        }
    }
}
