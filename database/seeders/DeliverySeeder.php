<?php

namespace Database\Seeders;

use App\Models\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run(): void
    {
        $deliveries = [
            ['name' => 'JNE Regular'],
            ['name' => 'JNE YES'],
            ['name' => 'J&T Express'],
            ['name' => 'SiCepat Regular'],
        ];

        foreach ($deliveries as $delivery) {
            Delivery::firstOrCreate($delivery);
        }
    }
}
