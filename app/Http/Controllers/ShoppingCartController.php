<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $cart = ShoppingCart::firstOrCreate(
            ['user_id' => $user->id, 'status' => ShoppingCart::STATUS_ACTIVE]
        );

        $cartItems = $cart->items()
            ->with('product.category', 'product.brand')
            ->get();

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $tax = $subtotal * 0.11;
        $shippingCost = $cartItems->isNotEmpty() ? 15000 : 0;

        return view('shop.cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shippingCost' => $shippingCost,
            'total' => $subtotal + $tax + $shippingCost,
        ]);
    }

    public function addToCart(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($product->stocks < $request->quantity) {
            return back()->with('error', 'Not enough stock available');
        }

        $cart = ShoppingCart::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => ShoppingCart::STATUS_ACTIVE]
        );

        // Cek apakah produk sudah ada di cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($product->stocks < $newQuantity) {
                return back()->with('error', 'Not enough stock available');
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index');
    }

    public function updateQuantity(Request $request, ShoppingCartItem $cartItem)
    {
        $this->validateCartItemOwnership($cartItem);

        $request->validate(['quantity' => 'required|integer|min:1']);

        if ($cartItem->product->stocks < $request->quantity) {
            return back()->with('error', 'Not enough stock available');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back();
    }

    public function removeItem(ShoppingCartItem $cartItem)
    {
        $this->validateCartItemOwnership($cartItem);

        $cartItem->delete();
        return back();
    }

    private function validateCartItemOwnership(ShoppingCartItem $cartItem)
    {
        $activeCart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', ShoppingCart::STATUS_ACTIVE)
            ->first();

        if (!$activeCart || $cartItem->shopping_cart_id !== $activeCart->id) {
            abort(403, 'Unauthorized action on this cart item.');
        }
    }

    public function clearCart()
    {
        $cart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', ShoppingCart::STATUS_ACTIVE)
            ->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return back();
    }
}
