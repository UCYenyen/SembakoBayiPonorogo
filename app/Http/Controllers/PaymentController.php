<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingCart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Address;
use App\Models\Delivery;
use App\Models\ShoppingCartItem;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    public function index()
    {
        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $addresses = Address::where('user_id', Auth::id())->get();

        $subtotal = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;

        return view('shop.payment.index', [
            'cart' => $cart,
            'addresses' => $addresses,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'delivery_price' => 'required|numeric|min:0',
            'courier' => 'required|string'
        ]);

        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty'], 400);
        }

        $delivery = Delivery::where('courier_code', $request->courier)->first() ?? Delivery::first();

        $subtotal = $cart->items->sum(fn($item) => $item->product->price * $item->quantity);
        $tax = $subtotal * 0.11;
        $deliveryPrice = (int) $request->delivery_price;
        $totalPrice = $subtotal + $tax + $deliveryPrice;

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'address_id' => $request->address_id,
            'shopping_cart_id' => $cart->id,
            'delivery_id' => $delivery->id,
            'payment_method' => 'Pending',
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
            'status' => Transaction::STATUS_PENDING_PAYMENT,
        ]);

        foreach ($cart->items as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        $item_details = $cart->items->map(function ($item) {
            return [
                'id' => 'PRD-' . $item->product_id,
                'price' => (int) $item->product->price,
                'quantity' => (int) $item->quantity,
                'name' => substr($item->product->name, 0, 50),
            ];
        })->toArray();

        $item_details[] = ['id' => 'TAX-11', 'price' => (int) $tax, 'quantity' => 1, 'name' => 'Pajak PPN (11%)'];
        $item_details[] = ['id' => 'SHIPPING', 'price' => (int) $deliveryPrice, 'quantity' => 1, 'name' => 'Ongkos Kirim (' . strtoupper($request->courier) . ')'];

        $orderId = 'ORDER-' . $transaction->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $totalPrice,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone_number ?? '08123456789',
            ],
            'item_details' => $item_details,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            $cart->update(['status' => ShoppingCart::STATUS_CHECKED_OUT]);

            Log::info('Snap token generated', [
                'transaction_id' => $transaction->id,
                'order_id' => $orderId,
                'gross_amount' => $totalPrice
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'transaction_id' => $transaction->id
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            $transaction->update(['status' => Transaction::STATUS_FAILED]);
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    public function finish(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            // PERBAIKAN: Cek status menggunakan transaction ID dari database
            $midtransStatus = \Midtrans\Transaction::status($transaction->id);
            
            Log::info('Midtrans Status Response', [
                'transaction_id' => $transaction->id,
                'midtrans_response' => json_encode($midtransStatus)
            ]);

            if ($midtransStatus) {
                $paymentType = $midtransStatus->payment_type ?? null;
                $transactionStatus = $midtransStatus->transaction_status ?? null;
                
                // PERBAIKAN: Extract payment type dengan lebih detail
                $paymentMethod = 'Midtrans';
                
                if ($paymentType) {
                    $paymentMethod = $this->formatPaymentMethod($paymentType);
                    Log::info('Payment type extracted from Midtrans', [
                        'raw_payment_type' => $paymentType,
                        'formatted_method' => $paymentMethod
                    ]);
                } else {
                    // Jika payment_type kosong tapi ada settlement_time, coba ambil dari field lain
                    if (isset($midtransStatus->settlement_time)) {
                        Log::warning('Payment type empty but settlement_time exists', [
                            'full_response' => (array) $midtransStatus
                        ]);
                        // Fallback: gunakan transaction_status untuk hint
                        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                            $paymentMethod = 'Midtrans Payment';
                        }
                    }
                }
                
                // Update transaction dengan status dan payment method
                $transaction->update([
                    'status' => Transaction::STATUS_PAID,
                    'payment_method' => $paymentMethod
                ]);

                Log::info('Transaction updated', [
                    'transaction_id' => $transaction->id,
                    'status' => Transaction::STATUS_PAID,
                    'payment_method' => $paymentMethod
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Midtrans status check error', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // PERBAIKAN: Jika masih pending dan ada snap_token, update jadi paid dengan fallback method
            if ($transaction->isPendingPayment() && $transaction->snap_token) {
                $transaction->update([
                    'status' => Transaction::STATUS_PAID,
                    'payment_method' => 'Midtrans'
                ]);
                
                Log::info('Transaction updated with fallback', [
                    'transaction_id' => $transaction->id,
                    'reason' => 'Could not fetch Midtrans status but has snap_token'
                ]);
            }
        }

        // Clear shopping cart
        if ($transaction->shopping_cart_id) {
            ShoppingCartItem::where('shopping_cart_id', $transaction->shopping_cart_id)->delete();
            $transaction->shopping_cart->update(['status' => ShoppingCart::STATUS_ORDERED]);
        }

        return view('shop.payment.finish', [
            'transaction' => $transaction->load(['transaction_items.product', 'delivery'])
        ]);
    }

    public function unfinish(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        return view('shop.payment.unfinish', ['transaction' => $transaction->load(['transaction_items.product'])]);
    }

    public function retryPayment(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$transaction->snap_token) {
            return $this->generateSnapToken($transaction);
        }

        return response()->json([
            'snap_token' => $transaction->snap_token,
            'transaction_id' => $transaction->id
        ]);
    }

    private function generateSnapToken(Transaction $transaction)
    {
        $transaction->load(['transaction_items.product']);
        $subtotal = $transaction->transaction_items->sum(fn($item) => $item->price * $item->quantity);
        $tax = $subtotal * 0.11;

        $item_details = $transaction->transaction_items->map(function ($item) {
            return [
                'id' => 'PRD-' . $item->product_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => substr($item->product->name, 0, 50),
            ];
        })->toArray();

        $item_details[] = ['id' => 'TAX-11', 'price' => (int) $tax, 'quantity' => 1, 'name' => 'Pajak PPN (11%)'];
        $item_details[] = ['id' => 'SHIPPING', 'price' => (int) $transaction->delivery_price, 'quantity' => 1, 'name' => 'Ongkos Kirim'];

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $transaction->id . '-' . time(),
                'gross_amount' => (int) $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone_number ?? '08123456789',
            ],
            'item_details' => $item_details,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            return response()->json(['snap_token' => $snapToken, 'transaction_id' => $transaction->id]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    /**
     * Format payment method dari Midtrans response
     * @param mixed $paymentType
     * @return string
     */
    private function formatPaymentMethod($paymentType): string
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

        $key = strtolower(str_replace(' ', '_', (string) $paymentType));
        
        return (string) ($paymentMethods[$key] ?? ucfirst(str_replace('_', ' ', $key)));
    }
}