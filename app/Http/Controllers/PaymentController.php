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
use App\Models\Voucher;
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
            ->with(['items.product', 'vouchers.base_voucher'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        $addresses = Address::where('user_id', Auth::id())->get();

        $subtotal = $cart->items->sum(function ($item) {
            $productPrice = $item->product->price;
            $discount = $item->product->discount_amount ?? 0;
            return ($productPrice - $discount) * $item->quantity;
        });

        $voucherDiscount = $cart->getTotalVoucherDiscount();
        $appliedVouchers = $cart->vouchers()
            ->whereNull('transaction_id')
            ->with('base_voucher')
            ->get();

        return view('shop.payment.index', [
            'cart' => $cart,
            'addresses' => $addresses,
            'subtotal' => $subtotal,
            'voucherDiscount' => $voucherDiscount,
            'appliedVouchers' => $appliedVouchers,
            'total' => $subtotal - $voucherDiscount,
        ]);
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'delivery_id' => 'required|exists:deliveries,id',
            'delivery_price' => 'required|numeric|min:0',
            'courier' => 'required|string|in:jne,jnt,gosend'
        ]);

        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['items.product', 'vouchers.base_voucher'])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }
        $subtotal = $cart->items->sum(function ($item) {
            $productPrice = $item->product->price;
            $discount = $item->product->discount_amount ?? 0;
            return ($productPrice - $discount) * $item->quantity;
        });

        $voucherDiscount = $cart->getTotalVoucherDiscount();
        $totalPrice = $subtotal - $voucherDiscount;
        $totalBill = $totalPrice + $validated['delivery_price'];

        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'address_id' => $validated['address_id'],
            'shopping_cart_id' => $cart->id,
            'delivery_id' => $validated['delivery_id'],
            'payment_method' => 'Pending',
            'delivery_price' => $validated['delivery_price'],
            'total_price' => $totalPrice,
            'status' => Transaction::STATUS_PENDING_PAYMENT,
        ]);

        $transaction->refresh();

        foreach ($cart->items as $item) {
            $productPrice = $item->product->price;
            $discount = $item->product->discount_amount ?? 0;

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $productPrice - $discount, // ✅ Simpan harga setelah diskon
            ]);

            $item->product->decrement('stocks', $item->quantity);
        }

        Voucher::where('shopping_cart_id', $cart->id)
            ->whereNull('transaction_id')
            ->update(['transaction_id' => $transaction->id]);

        return $this->generateSnapToken($transaction);
    }

    public function finish(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

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

        if ($transaction->snap_token && $transaction->status === Transaction::STATUS_PENDING_PAYMENT) {
            return response()->json([
                'snap_token' => $transaction->snap_token,
                'transaction_id' => $transaction->id
            ]);
        }

        return $this->generateSnapToken($transaction);
    }

    private function generateSnapToken(Transaction $transaction)
    {
        $totalBill = $transaction->total_price + $transaction->delivery_price;

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->order_id,
                'gross_amount' => (int) $totalBill,
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
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }
}
