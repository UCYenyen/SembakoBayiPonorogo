<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
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
            $transactionStatus = $request->input('transaction_status');
            $paymentType = $request->input('payment_type');

            $transaction = Transaction::where('order_id', $orderId)->first();

            if (!$transaction) {
                return response()->json(['message' => 'Not Found'], 404);
            }

            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                $transaction->update([
                    'status' => Transaction::STATUS_PAID,
                    'payment_method' => $this->formatPaymentMethod($paymentType)
                ]);
                $this->updateUserPoints($transaction);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function updateUserPoints(Transaction $transaction)
    {
        $user = $transaction->user;
        $pointsToAdd = floor($transaction->total_bill / 100000) * 10;
        $user->points += $pointsToAdd;

        if($user->role == 'guest' && $user->points >= 10) {
            $user->role = 'member';
        }
    
        $user->save();
    }

    public function complete(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->update(['status' => Transaction::STATUS_COMPLETED]);

        return back();
    }

    private function formatPaymentMethod($paymentType): string
    {
        $methods = [
            'credit_card' => 'Kartu Kredit',
            'bank_transfer' => 'Transfer Bank',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS'
        ];
        return $methods[strtolower($paymentType)] ?? ucfirst(str_replace('_', ' ', (string) $paymentType));
    }
}
