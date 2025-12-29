<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Midtrans\Config;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    public function midtransNotification(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $statusResponse = \Midtrans\Transaction::status($orderId);
            $status = is_array($statusResponse) ? (object) $statusResponse : $statusResponse;

            if (preg_match('/ORDER-(\d+)/', $orderId, $matches)) {
                $transactionId = $matches[1];
                $transaction = Transaction::find($transactionId);

                if (!$transaction) {
                    return response()->json(['message' => 'Not Found'], 404);
                }

                $transactionStatus = $status->transaction_status;
                $paymentType = $status->payment_type;
                $paymentMethod = $this->formatPaymentMethod($paymentType);

                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $newStatus = Transaction::STATUS_PAID;
                } elseif ($transactionStatus == 'pending') {
                    $newStatus = Transaction::STATUS_PENDING_PAYMENT;
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $newStatus = Transaction::STATUS_CANCELLED;
                } else {
                    $newStatus = $transaction->status;
                }

                $transaction->update([
                    'status' => $newStatus,
                    'payment_method' => $paymentMethod
                ]);

                return response()->json(['message' => 'OK']);
            }
            return response()->json(['message' => 'Invalid ID'], 400);
        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    private function formatPaymentMethod($paymentType): string
    {
        $paymentMethods = [
            'credit_card' => 'Kartu Kredit',
            'bank_transfer' => 'Transfer Bank',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS'
        ];
        return $paymentMethods[strtolower($paymentType)] ?? ucfirst(str_replace('_', ' ', (string) $paymentType));
    }
}