<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingCart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Payment;
use App\Models\Address;
use App\Models\Delivery;
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
        $payments = Payment::all();

        $subtotal = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.11;
        $total = $subtotal + $tax;

        return view('shop.payment.index', [
            'cart' => $cart,
            'addresses' => $addresses,
            'payments' => $payments,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'payment_id' => 'required|exists:payments,id',
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

        $subtotal = $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.11;
        $deliveryPrice = (int) $request->delivery_price;
        $totalPrice = $subtotal + $tax + $deliveryPrice;

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'address_id' => $request->address_id,
            'shopping_cart_id' => $cart->id,
            'delivery_id' => $delivery->id,
            'payment_id' => $request->payment_id,
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
            'id' => 'TAX-11',
            'price' => (int) $tax,
            'quantity' => 1,
            'name' => 'Pajak PPN (11%)',
        ];

        $item_details[] = [
            'id' => 'SHIPPING',
            'price' => (int) $deliveryPrice,
            'quantity' => 1,
            'name' => 'Ongkos Kirim (' . strtoupper($request->courier) . ')',
        ];

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $transaction->id . '-' . time(),
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

            return response()->json([
                'snap_token' => $snapToken,
                'transaction_id' => $transaction->id
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment processing failed'], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $signatureKey = hash(
            "sha512",
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                $serverKey
        );
        if ($signatureKey !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }
        $transaction = \App\Models\Transaction::find($request->order_id);
        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }
        // Update status berdasarkan notifikasi
        if (in_array($request->transaction_status, ['settlement', 'capture'])) {
            $transaction->status = 'paid';
        } elseif (in_array($request->transaction_status, ['cancel', 'expire'])) {
            $transaction->status = 'failed';
        } elseif ($request->transaction_status == 'pending') {
            $transaction->status = 'pending';
        }
        $transaction->save();
        return response()->json(['message' => 'Webhook processed successfully']);
    }

    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        if (!$orderId) return redirect()->route('dashboard');

        preg_match('/ORDER-(\d+)-/', $orderId, $matches);
        $transactionId = $matches[1] ?? null;

        $transaction = Transaction::with(['transaction_items.product', 'payment', 'delivery'])
            ->find($transactionId);

        if (!$transaction || $transaction->user_id !== Auth::id()) abort(403);

        return view('shop.payment.finish', ['transaction' => $transaction]);
    }

    public function unfinish(Request $request)
    {
        $orderId = $request->get('order_id');
        if (!$orderId) return redirect()->route('dashboard');

        preg_match('/ORDER-(\d+)-/', $orderId, $matches);
        $transactionId = $matches[1] ?? null;

        $transaction = Transaction::with(['transaction_items.product'])
            ->find($transactionId);

        if (!$transaction || $transaction->user_id !== Auth::id()) return redirect()->route('dashboard');

        return view('shop.payment.unfinish', ['transaction' => $transaction]);
    }
}
