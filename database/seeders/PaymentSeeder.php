<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $payments = [
            ['method' => 'Bank Transfer (BCA, BNI, BRI, Mandiri)'],
            ['method' => 'Credit Card / Debit Card'],
            ['method' => 'E-Wallet (GoPay, ShopeePay)'],
            ['method' => 'QRIS (Scan to Pay)'],
        ];

        foreach ($payments as $payment) {
            Payment::firstOrCreate($payment);
        }
    }
}
