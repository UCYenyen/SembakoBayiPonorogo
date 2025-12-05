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

        Log::info('Midtrans Config:', [
            'server_key_exists' => !empty(config('midtrans.server_key')),
            'server_key_prefix' => substr(config('midtrans.server_key'), 0, 10),
            'is_production' => config('midtrans.is_production'),
        ]);
    }

    public function showCheckout()
    {
        $user = Auth::user();
        
        $addresses = Address::where('user_id', $user->id)->get();
        
        if ($addresses->isEmpty()) {
            return redirect()->route('user.addresses.create')
                ->with('error', 'Please add a shipping address before checkout.');
        }
        
        $cart = ShoppingCart::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        // Calculate totals
        $subtotal = $cart->items->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.11;
        
        // ✅ Shipping cost will be calculated dynamically
        $shippingCost = 0;
        $total = $subtotal + $tax + $shippingCost;

        $payments = Payment::all();

        // ✅ Remove deliveries - akan di-fetch via AJAX
        return view('shop.payment.index', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'addresses' => $addresses,
            'payments' => $payments,
        ]);
    }

    // ✅ New endpoint to get shipping options
    public function getShippingOptions(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        $address = Address::findOrFail($request->address_id);

        if (!$address->city_id) {
            return response()->json([
                'error' => 'City information not available for this address'
            ], 400);
        }

        // Get cart to calculate total weight
        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['items.product'])
            ->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 404);
        }

        // Calculate total weight (assume 500g per product, adjust as needed)
        $totalWeight = $cart->items->sum(function($item) {
            return 500 * $item->quantity; // 500 grams per item
        });

        // Your shop city ID (e.g., Ponorogo = 321)
        // Get from RajaOngkir city list
        $origin = 321; // Change to your city ID

        $shippingOptions = $this->rajaOngkir->getShippingOptions(
            $origin,
            $address->city_id,
            $totalWeight
        );

        return response()->json([
            'success' => true,
            'shipping_options' => $shippingOptions,
            'total_weight' => $totalWeight,
        ]);
    }

    public function createTransaction(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'address_id' => 'required|exists:addresses,id',
                'payment_id' => 'required|exists:payments,id',
                'delivery_id' => 'required|exists:deliveries,id', // ✅ Tetap validasi delivery_id
                'shipping_cost' => 'required|integer|min:0',
                'shipping_service' => 'required|string', // ✅ Service name untuk display
            ]);

            Log::info('Payment Request Received:', $validated);

            $user = Auth::user();
            
            // Get active cart
            $cart = ShoppingCart::where('user_id', $user->id)
                ->where('status', 'active')
                ->with(['items.product'])
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                Log::error('Cart is empty or not found');
                return response()->json(['error' => 'Cart is empty'], 400);
            }

            Log::info('Cart found:', ['cart_id' => $cart->id, 'items_count' => $cart->items->count()]);

            // Calculate totals
            $subtotal = $cart->items->sum(function($item) {
                return $item->product->price * $item->quantity;
            });

            $tax = (int) round($subtotal * 0.11);
            $shippingCost = (int) $request->shipping_cost; // ✅ Dynamic dari RajaOngkir
            $total = (int) ($subtotal + $tax + $shippingCost);

            Log::info('Order Totals:', [
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shippingCost,
                'total' => $total
            ]);

            // Create transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'shopping_cart_id' => $cart->id,
                'address_id' => $request->address_id,
                'payment_id' => $request->payment_id,
                'delivery_id' => $request->delivery_id, // ✅ Delivery ID dari table deliveries
                'total_price' => $subtotal,
                'delivery_price' => $shippingCost,
                'status' => Transaction::STATUS_PENDING_PAYMENT,
            ]);

            Log::info('Transaction created:', ['transaction_id' => $transaction->id]);

            // Create transaction items
            foreach ($cart->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
            }

            // Prepare item details for Midtrans
            $itemDetails = [];
            foreach ($cart->items as $item) {
                $itemDetails[] = [
                    'id' => 'PROD-' . $item->product->id,
                    'price' => (int) $item->product->price,
                    'quantity' => (int) $item->quantity,
                    'name' => substr($item->product->name, 0, 50),
                ];
            }

            // Add tax and shipping
            $itemDetails[] = [
                'id' => 'TAX',
                'price' => $tax,
                'quantity' => 1,
                'name' => 'Tax 11%',
            ];

            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => $shippingCost,
                'quantity' => 1,
                'name' => $request->shipping_service, // ✅ Display service name
            ];

            // Get address
            $address = Address::findOrFail($request->address_id);
            Log::info('Address found:', ['address_detail' => $address->detail]);

            // Parse address detail
            $addressParts = explode(',', $address->detail);
            $city = isset($addressParts[1]) ? trim($addressParts[1]) : 'Jakarta';
            $postalCode = '12345';

            // Generate unique order ID
            $orderId = 'TRX-' . $transaction->id . '-' . time();
            
            // Get selected payment method
            $payment = Payment::findOrFail($request->payment_id);

            // Prepare Midtrans parameters
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $total,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => substr($user->name, 0, 50),
                    'email' => $user->email,
                    'phone' => $user->phone_number ?? '081234567890',
                    'billing_address' => [
                        'first_name' => substr($user->name, 0, 50),
                        'address' => substr($address->detail, 0, 200),
                        'city' => substr($city, 0, 50),
                        'postal_code' => substr($postalCode, 0, 10),
                        'country_code' => 'IDN',
                    ],
                    'shipping_address' => [
                        'first_name' => substr($user->name, 0, 50),
                        'address' => substr($address->detail, 0, 200),
                        'city' => substr($city, 0, 50),
                        'postal_code' => substr($postalCode, 0, 10),
                        'country_code' => 'IDN',
                    ],
                ],
            ];

            // Set enabled payments
            $enabledPayments = $payment->getEnabledPayments();
            if (!empty($enabledPayments)) {
                $params['enabled_payments'] = $enabledPayments;
                
                Log::info('Enabled Payments Set:', [
                    'payment_method' => $payment->method,
                    'payment_type' => $payment->getPaymentType(),
                    'enabled_payments' => $enabledPayments
                ]);
            }

            Log::info('Midtrans Params:', $params);

            // Check if server key is set
            if (empty(config('midtrans.server_key'))) {
                throw new \Exception('Midtrans server key is not configured. Please check your .env file.');
            }

            // Get Snap Token
            $snapToken = Snap::getSnapToken($params);

            Log::info('Snap Token Generated Successfully', ['token_prefix' => substr($snapToken, 0, 20)]);

            // Update transaction with order_id
            $transaction->update(['no_resi' => $orderId]);

            // Update cart status
            $cart->update(['status' => 'ordered']);

            Log::info('Transaction completed successfully', [
                'transaction_id' => $transaction->id,
                'order_id' => $orderId,
                'delivery_id' => $request->delivery_id
            ]);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'transaction_id' => $transaction->id,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error("Transaction Creation Error", [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]); 
            
            return response()->json([
                'error' => 'Failed to create transaction.',
                'message' => $e->getMessage(),
                'hint' => 'Please check server logs for details'
            ], 500);
        }
    }

    public function paymentFinish($transactionId)
    {
        $transaction = Transaction::with(['transaction_items.product', 'user'])
            ->findOrFail($transactionId);

        return view('shop.payment.finish', [
            'transaction' => $transaction,
        ]);
    }

    public function notificationHandler(Request $request)
    {
        try {
            $notif = new Notification();
            
            $transactionStatus = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;
            $fraudStatus = $notif->fraud_status ?? 'accept';

            Log::info('Midtrans Notification:', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
                'type' => $type,
                'fraud' => $fraudStatus,
            ]);

            // Find transaction by order_id (no_resi)
            $transaction = Transaction::where('no_resi', $orderId)->first();

            if (!$transaction) {
                Log::error("Transaction not found: " . $orderId);
                return response()->json(['message' => 'Transaction not found'], 404);
            }

            // Update transaction status based on Midtrans notification
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'accept') {
                    $transaction->update(['status' => Transaction::STATUS_PAID]);
                }
            } else if ($transactionStatus == 'settlement') {
                $transaction->update(['status' => Transaction::STATUS_PAID]);
            } else if ($transactionStatus == 'pending') {
                $transaction->update(['status' => Transaction::STATUS_PENDING_PAYMENT]);
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                $transaction->update(['status' => Transaction::STATUS_CANCELLED]);
            }

            Log::info("Transaction {$orderId} updated to status: {$transactionStatus}");

            return response()->json(['message' => 'Notification handled successfully']);
        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['message' => 'Notification handling failed'], 500);
        }
    }
}
