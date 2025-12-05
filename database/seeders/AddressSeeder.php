<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if ($user) {
            Address::firstOrCreate([
                'user_id' => $user->id,
            ], [
                'detail' => 'Jl. Sudirman No. 123, Kelurahan Manggarai, Kecamatan Tebet, Jakarta Selatan, DKI Jakarta 12850, Indonesia',
                'is_default' => true,
            ]);

            $this->command->info('Address seeded successfully for user: ' . $user->email);
        } else {
            $this->command->warn('No users found. Please create a user first.');
        }
    }
}
