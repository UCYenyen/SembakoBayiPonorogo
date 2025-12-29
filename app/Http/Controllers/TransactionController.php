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

    /**
     * Handle Midtrans Webhook Notification
     * Endpoint: POST /webhook/midtrans
     */
    public function midtransNotification(Request $request)
    {
        Log::info('Midtrans Webhook Received', [
            'payload' => $request->all()
        ]);

        try {
            // Verify notification signature
            $orderId = $request->input('order_id');
            $transactionIdInput = $request->input('transaction_id');

            // Get full notification from Midtrans
            $statusResponse = \Midtrans\Transaction::status($transactionIdInput ?? $orderId);

            // Cast response ke object jika array
            if (is_array($statusResponse)) {
                $status = (object) $statusResponse;
            } else {
                $status = $statusResponse;
            }

            Log::info('Midtrans Status from Webhook', [
                'order_id' => $orderId,
                'status' => json_encode($status)
            ]);

            // Extract order ID (format: ORDER-{transaction_id}-{timestamp})
            if (is_string($orderId) && preg_match('/ORDER-(\d+)-/', $orderId, $matches)) {
                $transactionId = (int) $matches[1];
                $transaction = Transaction::find($transactionId);

                if (!$transaction) {
                    Log::error('Transaction not found', ['transaction_id' => $transactionId]);
                    return response()->json(['message' => 'Transaction not found'], 404);
                }

                // Get payment type
                $paymentType = (string) ($status->payment_type ?? 'Midtrans');
                $paymentMethod = $this->formatPaymentMethod($paymentType);

                // Map Midtrans transaction status to our status
                $transactionStatus = (string) ($status->transaction_status ?? '');

                if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                    $newStatus = Transaction::STATUS_PAID;
                } elseif ($transactionStatus === 'pending') {
                    $newStatus = Transaction::STATUS_PENDING_PAYMENT;
                } elseif ($transactionStatus === 'deny') {
                    $newStatus = Transaction::STATUS_FAILED;
                } elseif ($transactionStatus === 'cancel' || $transactionStatus === 'expire') {
                    $newStatus = Transaction::STATUS_CANCELLED;
                } else {
                    $newStatus = $transaction->status;
                }

                // Update transaction
                $transaction->update([
                    'status' => $newStatus,
                    'payment_method' => $paymentMethod
                ]);

                Log::info('Transaction updated via webhook', [
                    'transaction_id' => $transactionId,
                    'payment_type' => $paymentType,
                    'payment_method' => $paymentMethod,
                    'new_status' => $newStatus
                ]);

                return response()->json(['message' => 'Notification processed'], 200);
            } else {
                Log::error('Invalid order ID format', ['order_id' => $orderId]);
                return response()->json(['message' => 'Invalid order ID format'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }

    /**
     * Format payment method dari Midtrans response
     * @param string $paymentType
     * @return string
     */
    private function formatPaymentMethod(string $paymentType): string
    {
        $paymentMethods = [
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'gcg_payment' => 'GCG Payment',
            'bank_transfer' => 'Bank Transfer',
            'akulaku' => 'Akulaku',
            'buy_now_pay_later' => 'Buy Now Pay Later',
            'cimb_clicks' => 'CIMB Clicks',
            'danamon_online' => 'Danamon Online',
            'epay_bri' => 'E-Pay BRI',
            'gopay' => 'GoPay',
            'go_pay' => 'GoPay',
            'klik_bca' => 'Klik BCA',
            'klik_ksei' => 'Klik KSEI',
            'mandiri_clickpay' => 'Mandiri ClickPay',
            'maybank' => 'Maybank',
            'mobile_legends' => 'Mobile Legends',
            'ovo' => 'OVO',
            'permata_va' => 'Permata VA',
            'bca_va' => 'BCA VA',
            'bni_va' => 'BNI VA',
            'bri_va' => 'BRI VA',
            'qris' => 'QRIS',
            'shopeepay' => 'ShopeePay',
            'spay_later' => 'SPayLater',
            'telkomsel_cashback' => 'Telkomsel Cashback',
            'uob_click' => 'UOB Click',
            'other_va' => 'Virtual Account',
            'e_wallet' => 'E-Wallet',
            'bank_account' => 'Bank Account',
            'pay_later' => 'Pay Later',
            'other_qris' => 'QRIS',
        ];

        $key = strtolower(str_replace(' ', '_', $paymentType));

        return (string) ($paymentMethods[$key] ?? ucfirst(str_replace('_', ' ', $paymentType)));
    }
}
