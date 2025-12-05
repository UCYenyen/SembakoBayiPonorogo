<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $payments = [
            ['method' => 'Bank Transfer'],
            ['method' => 'Credit Card'],
            ['method' => 'E-Wallet (GoPay, OVO, Dana)'],
            ['method' => 'Convenience Store'],
        ];

        foreach ($payments as $payment) {
            Payment::firstOrCreate($payment);
        }
    }
}
