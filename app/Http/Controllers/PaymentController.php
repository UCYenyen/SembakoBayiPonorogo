<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function showCheckout()
    {
        return view('payment.index');
    }

    public function createTransaction(Request $request)
    {
        $orderId = 'ORDER-' . time() . '-' . rand(100, 999);
        $grossAmount = 55000; 

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => 'Budi',
                'last_name' => 'Santoso',
                'email' => 'budi.santoso@example.com',
                'phone' => '08123456789',
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
            ]);

        } catch (\Exception $e) {
            Log::error("Midtrans API Error: " . $e->getMessage()); 
            
            return response()->json([
                'error' => 'Gagal membuat transaksi ke Midtrans.',
                'detail' => $e->getMessage() 
            ], 500);
        }
    }

    public function notificationHandler(Request $request)
    {
        $notif = new Notification();
        
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        if ($transaction == 'settlement') {
            // Update status pesanan di database ke 'success'
        } else if ($transaction == 'pending') {
            // Update status pesanan di database ke 'pending'
        } else if ($transaction == 'expire' || $transaction == 'cancel' || $transaction == 'deny') {
            // Update status pesanan di database ke 'failed/expired/cancelled'
        }

        return response()->json(['message' => 'Notification processed successfully']);
    }
}
