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
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;

        // Set Midtrans configuration
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
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $address = Address::findOrFail($request->address_id);

        if (!$address->subdistrict_id) {
            return response()->json([
                'error' => 'Address location not available. Please update your address.'
            ], 400);
        }

        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        $totalWeight = $cart->items->sum(function($item) {
            return 500 * $item->quantity;
        });

        // ✅ Your shop subdistrict ID (Ponorogo)
        // Get from: https://rajaongkir.komerce.id/api/v1/destination/domestic-destination?search=ponorogo
        $originSubdistrictId = '6152'; // Example: Ponorogo subdistrict ID

        try {
            $rates = $this->rajaOngkir->getShippingOptions(
                $originSubdistrictId,
                $address->subdistrict_id,
                $totalWeight
            );

            if (empty($rates)) {
                return response()->json([
                    'error' => 'No shipping options available.',
                    'shipping_options' => []
                ], 400);
            }

            return response()->json([
                'success' => true,
                'shipping_options' => $rates,
                'total_weight' => $totalWeight,
            ]);

        } catch (\Exception $e) {
            Log::error('Komerce getRates Error:', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to fetch shipping options.',
                'message' => $e->getMessage()
            ], 500);
        }
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

        // ✅ FIX: Use Transaction constant instead of string
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'address_id' => $request->address_id,
            'shopping_cart_id' => $cart->id,
            'delivery_id' => $request->delivery_id,
            'payment_id' => $request->payment_id,
            'delivery_price' => $deliveryPrice,
            'total_price' => $totalPrice,
            'status' => Transaction::STATUS_PENDING_PAYMENT, // ✅ Use constant
        ]);

        // Create transaction items
        foreach ($cart->items as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        // Prepare Midtrans payment
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

        // Add delivery cost as item
        $params['item_details'][] = [
            'id' => 'DELIVERY',
            'price' => (int) $deliveryPrice,
            'quantity' => 1,
            'name' => 'Shipping Cost',
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

            // ✅ FIX: Use ShoppingCart constant
            // Mark cart as checked out
            $cart->update(['status' => ShoppingCart::STATUS_CHECKED_OUT]); // ✅ Now constant exists

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

    /**
     * Handle Midtrans notification (webhook)
     */
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

            // Extract transaction ID from order_id (ORDER-1-1765003598 -> 1)
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
    
    /**
     * Show payment finish page
     */
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
        
        // Check if user owns this transaction
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('shop.payment.finish', ['transaction' => $transaction]);
    }

    /**
     * Show payment unfinish page (pending payment)
     */
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
    
    /**
     * Check transaction status manually (for debugging & sandbox)
     */
    public function checkStatus(Request $request)
    {
        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return response()->json(['error' => 'Order ID required'], 400);
        }

        try {
            // Get status from Midtrans
            /** @var object $status */
            $status = \Midtrans\Transaction::status($orderId);
            
            Log::info('Manual Status Check:', [
                'order_id' => $orderId,
                'transaction_status' => $status->transaction_status,
                'fraud_status' => $status->fraud_status ?? 'N/A'
            ]);

            preg_match('/ORDER-(\d+)-/', $orderId, $matches);
            $transactionId = $matches[1] ?? null;
            
            if (!$transactionId) {
                return response()->json(['error' => 'Invalid order ID format'], 400);
            }

            $transaction = Transaction::find($transactionId);
            
            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Update based on Midtrans status
            $transactionStatus = $status->transaction_status;
            $fraudStatus = $status->fraud_status ?? null;

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
                Log::info('Transaction still PENDING_PAYMENT', ['transaction_id' => $transactionId]);
            } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $transaction->update(['status' => Transaction::STATUS_FAILED]);
                Log::info('Transaction marked as FAILED', ['transaction_id' => $transactionId]);
            } elseif ($transactionStatus == 'cancel') {
                $transaction->update(['status' => Transaction::STATUS_CANCELLED]);
                Log::info('Transaction marked as CANCELLED', ['transaction_id' => $transactionId]);
            }

            return response()->json([
                'success' => true,
                'transaction_id' => $transactionId,
                'status' => $transaction->status,
                'midtrans_status' => $transactionStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Status Check Error:', [
                'order_id' => $orderId,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to check status',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
