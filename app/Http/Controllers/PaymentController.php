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

        return view('shop.payment.index', [
            'cart' => $cart,
            'addresses' => $addresses,
            'subtotal' => $subtotal,
            'tax' => 0,
            'total' => $subtotal,
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
        $deliveryPrice = (int) $request->delivery_price;
        $totalPrice = (int) ($subtotal + $deliveryPrice);

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

        $item_details[] = [
            'id' => 'SHIPPING',
            'price' => $deliveryPrice,
            'quantity' => 1,
            'name' => 'Ongkos Kirim'
        ];

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $transaction->id,
                'gross_amount' => $totalPrice,
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
            $midtransStatus = \Midtrans\Transaction::status('ORDER-' . $transaction->id);

            if ($midtransStatus) {
                $paymentType = $midtransStatus->payment_type ?? null;
                $transactionStatus = $midtransStatus->transaction_status ?? null;
                $paymentMethod = $this->formatPaymentMethod($paymentType);

                if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
                    $transaction->update([
                        'status' => Transaction::STATUS_PAID,
                        'payment_method' => $paymentMethod
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Midtrans status check error: ' . $e->getMessage());
        }

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
        $totalPrice = (int) ($subtotal + $transaction->delivery_price);

        $item_details = $transaction->transaction_items->map(function ($item) {
            return [
                'id' => 'PRD-' . $item->product_id,
                'price' => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name' => substr($item->product->name, 0, 50),
            ];
        })->toArray();

        $item_details[] = [
            'id' => 'SHIPPING',
            'price' => (int) $transaction->delivery_price,
            'quantity' => 1,
            'name' => 'Ongkos Kirim'
        ];

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $transaction->id,
                'gross_amount' => $totalPrice,
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

    private function formatPaymentMethod($paymentType): string
    {
        $paymentMethods = [
            'credit_card' => 'Kartu Kredit',
            'bank_transfer' => 'Transfer Bank',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'permata_va' => 'Permata Virtual Account',
            'echannel' => 'Mandiri Bill Payment',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'qris' => 'QRIS',
            'akulaku' => 'Akulaku',
        ];

        return $paymentMethods[strtolower($paymentType)] ?? ucfirst(str_replace('_', ' ', (string) $paymentType));
    }
}