<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        User::updateOrCreate(
            ['email' => 'bfernando@student.ciputra.ac.id'],
            [
                'name' => 'Bryan Fernando Dinata',
                'email' => 'bfernando@student.ciputra.ac.id',
                'phone_number' => '+6281231847161',
                'email_verified_at' => now(), // ✅ Sudah verified
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );
        $this->call([
            CategorySeeder::class,
            BrandsSeeder::class,
        ]);
    }
}
