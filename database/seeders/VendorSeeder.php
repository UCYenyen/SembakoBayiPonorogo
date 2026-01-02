<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::create([
            'name' => 'Tokopedia',
            'phone_number' => '081231847161',
            'type' => 'Online',
            'link' => 'https://rectorcupuc.com',
        ]);
        Vendor::create([
            'name' => 'Pak Budi',
            'phone_number' => '081234567890',
            'type' => 'Offline',
            'link' => 'https://vendor-a.example.com',
        ]);
    }
}
