<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingCart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Address;
use App\Services\RajaOngkirService;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

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

        // Calculate totals
        $subtotal = $cart->items->sum(function($item) {
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

    public function getShippingOptions(Request $request)
    {
        
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'delivery_id' => 'required|exists:deliveries,id',
            'payment_id' => 'required|exists:payments,id',
            'delivery_price' => 'required|numeric|min:0',
        ]);

        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'error' => 'Your cart is empty'
            ], 400);
        }

        $cartTotal = $cart->items->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        $deliveryPrice = $request->delivery_price;
        $totalPrice = $cartTotal + $deliveryPrice;

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'address_id' => $request->address_id,
            'shopping_cart_id' => $cart->id,
            'delivery_id' => $request->delivery_id,
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
            'item_details' => $cart->items->map(function($item) {
                return [
                    'id' => $item->product_id,
                    'price' => (int) $item->product->price,
                    'quantity' => $item->quantity,
                    'name' => $item->product->name,
                ];
            })->toArray(),
        ];

        $params['item_details'][] = [
            'id' => 'DELIVERY',
            'price' => (int) $deliveryPrice,
            'quantity' => 1,
            'name' => 'Shipping Cost',
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
            Log::error('Midtrans Snap Error:', [
                'message' => $e->getMessage(),
                'transaction_id' => $transaction->id
            ]);

            return response()->json([
                'error' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function notificationHandler(Request $request)
    {
        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId = $notification->order_id;
            $fraudStatus = $notification->fraud_status;

            Log::info('Midtrans Notification Received:', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus ?? 'N/A'
            ]);

            preg_match('/ORDER-(\d+)-/', $orderId, $matches);
            $transactionId = $matches[1] ?? null;

            if (!$transactionId) {
                Log::error('Invalid order_id format: ' . $orderId);
                return response()->json(['status' => 'error', 'message' => 'Invalid order ID'], 400);
            }

            $transaction = Transaction::find($transactionId);

            if (!$transaction) {
                Log::error('Transaction not found: ' . $transactionId);
                return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
            }

            // Update status based on Midtrans response
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $transaction->update(['status' => Transaction::STATUS_PAID]);
                    Log::info('Transaction marked as PAID (capture accepted)', ['transaction_id' => $transactionId]);
                }
            } elseif ($transactionStatus == 'settlement') {
                $transaction->update(['status' => Transaction::STATUS_PAID]);
                Log::info('Transaction marked as PAID (settlement)', ['transaction_id' => $transactionId]);
            } elseif ($transactionStatus == 'pending') {
                $transaction->update(['status' => Transaction::STATUS_PENDING_PAYMENT]);
                Log::info('Transaction marked as PENDING_PAYMENT', ['transaction_id' => $transactionId]);
            } elseif ($transactionStatus == 'deny') {
                $transaction->update(['status' => Transaction::STATUS_FAILED]);
                Log::info('Transaction marked as FAILED (denied)', ['transaction_id' => $transactionId]);
            } elseif ($transactionStatus == 'expire') {
                $transaction->update(['status' => Transaction::STATUS_FAILED]);
                Log::info('Transaction marked as FAILED (expired)', ['transaction_id' => $transactionId]);
            } elseif ($transactionStatus == 'cancel') {
                $transaction->update(['status' => Transaction::STATUS_CANCELLED]);
                Log::info('Transaction marked as CANCELLED', ['transaction_id' => $transactionId]);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid order ID');
        }
        
        // Extract transaction ID from order_id (ORDER-1-1765003598 -> 1)
        preg_match('/ORDER-(\d+)-/', $orderId, $matches);
        $transactionId = $matches[1] ?? null;
        
        if (!$transactionId) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid order ID format');
        }
        
        $transaction = Transaction::with(['transaction_items.product', 'payment', 'delivery'])
            ->find($transactionId);
        
        if (!$transaction) {
            return redirect()->route('dashboard')
                ->with('error', 'Transaction not found');
        }
        
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('shop.payment.finish', ['transaction' => $transaction]);
    }

    public function unfinish(Request $request)
    {
        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid order ID');
        }
        
        preg_match('/ORDER-(\d+)-/', $orderId, $matches);
        $transactionId = $matches[1] ?? null;
        
        if (!$transactionId) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid order ID format');
        }
        
        $transaction = Transaction::with(['transaction_items.product'])
            ->find($transactionId);
        
        if (!$transaction || $transaction->user_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Transaction not found');
        }
        
        return view('shop.payment.unfinish', ['transaction' => $transaction]);
    }
}
