<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    /**
     * Display shopping cart
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get or create ACTIVE shopping cart (bukan checked_out atau ordered)
        $cart = ShoppingCart::firstOrCreate(
            ['user_id' => $user->id, 'status' => ShoppingCart::STATUS_ACTIVE],
            ['user_id' => $user->id, 'status' => ShoppingCart::STATUS_ACTIVE]
        );

        // Get cart items dengan product details
        // Hanya tampilkan item dari cart yang status 'active'
        $cartItems = ShoppingCartItem::where('shopping_cart_id', $cart->id)
            ->with('product.category', 'product.brand')
            ->get();

        // Calculate totals
        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        $tax = $subtotal * 0.11; // 11% PPN
        $shippingCost = $cartItems->count() > 0 ? 15000 : 0; // Flat shipping
        $total = $subtotal + $tax + $shippingCost;

        return view('shop.cart.index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shippingCost' => $shippingCost,
            'total' => $total,
        ]);
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Check stock
        if ($product->stocks < $request->quantity) {
            return back()->with('error', 'Not enough stock available');
        }

        // Get atau create ACTIVE cart saja
        $cart = ShoppingCart::firstOrCreate(
            ['user_id' => $user->id, 'status' => ShoppingCart::STATUS_ACTIVE],
            ['user_id' => $user->id, 'status' => ShoppingCart::STATUS_ACTIVE]
        );

        // Check if item already exists in active cart
        $cartItem = ShoppingCartItem::where('shopping_cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($product->stocks < $newQuantity) {
                return back()->with('error', 'Not enough stock available');
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            ShoppingCartItem::create([
                'shopping_cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart!');
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(Request $request, ShoppingCartItem $cartItem)
    {
        $this->validateCartItemOwnership($cartItem);

        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $product = $cartItem->product;

        if ($product->stocks < $request->quantity) {
            return back()->with('error', 'Not enough stock available');
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated successfully!');
    }

    /**
     * Remove item from cart
     */
    public function removeItem(ShoppingCartItem $cartItem)
    {
        $this->validateCartItemOwnership($cartItem);
        
        $cartItem->delete();
        return back()->with('success', 'Item removed from cart!');
    }

    /**
     * Validate cart item belongs to user's active cart
     */
    private function validateCartItemOwnership(ShoppingCartItem $cartItem)
    {
        $activeCart = ShoppingCart::where('user_id', Auth::id())
            ->where('status', ShoppingCart::STATUS_ACTIVE)
            ->first();

        if (!$activeCart || $cartItem->shopping_cart_id !== $activeCart->id) {
            abort(403, 'Cannot perform action on item from inactive cart');
        }
    }

    /**
     * Clear entire active cart
     */
    public function clearCart()
    {
        $user = Auth::user();
        $cart = ShoppingCart::where('user_id', $user->id)
            ->where('status', ShoppingCart::STATUS_ACTIVE)
            ->first();

        if ($cart) {
            ShoppingCartItem::where('shopping_cart_id', $cart->id)->delete();
        }

        return back()->with('success', 'Cart cleared!');
    }
}