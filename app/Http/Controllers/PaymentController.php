<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\ShoppingCart;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Address;
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
            'total' => $subtotal,
        ]);
    }

    public function processPayment(Request $request)
    {
        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['items.product'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart empty'], 400);
        }

        $subtotal = $cart->items->sum(fn($i) => $i->product->price * $i->quantity);
        $totalPrice = (int) ($subtotal + $request->delivery_price);

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'address_id' => $request->address_id,
            'shopping_cart_id' => $cart->id,
            'delivery_id' => $request->delivery_id ?? 1,
            'payment_method' => 'Pending',
            'delivery_price' => $request->delivery_price,
            'total_price' => $totalPrice,
            'status' => Transaction::STATUS_PENDING_PAYMENT,
        ]);

        $transaction->refresh();

        foreach ($cart->items as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        return $this->generateSnapToken($transaction);
    }

    public function finish(Transaction $transaction)
    {
        if ($transaction->shopping_cart_id) {
            ShoppingCartItem::where('shopping_cart_id', $transaction->shopping_cart_id)->delete();
            $transaction->shopping_cart->update(['status' => ShoppingCart::STATUS_ORDERED]);
        }

        return view('shop.payment.finish', [
            'transaction' => $transaction->load(['transaction_items.product', 'delivery', 'address'])
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
        if ($transaction->user_id !== Auth::id()) abort(403);

        // Jika snap_token sudah ada dan status masih pending_payment, pakai token lama
        if ($transaction->snap_token && $transaction->status === Transaction::STATUS_PENDING_PAYMENT) {
            return response()->json([
                'snap_token' => $transaction->snap_token,
                'transaction_id' => $transaction->id
            ]);
        }

        // Jika belum ada snap_token, generate baru
        return $this->generateSnapToken($transaction);
    }
    private function generateSnapToken(Transaction $transaction)
    {
        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => (int) $transaction->total_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => ltrim(Auth::user()->phone_number ?? '81234567890', '0+62'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

            if ($transaction->shopping_cart_id) {
                ShoppingCart::find($transaction->shopping_cart_id)->update(['status' => ShoppingCart::STATUS_CHECKED_OUT]);
            }

            return response()->json([
                'snap_token' => $snapToken,
                'transaction_id' => $transaction->id
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Error: ' . $e->getMessage());
            return response()->json(['error' => 'Payment failed'], 500);
        }
    }
}
