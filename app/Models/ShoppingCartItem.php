<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCartItem extends Model
{
    /** @use HasFactory<\Database\Factories\ShoppingCartItemFactory> */
    use HasFactory;
    protected $fillable = [
        'shopping_cart_id',
        'product_id',
        'quantity',
    ];
    public function shoppingCart()
    {
        return $this->belongsTo(ShoppingCart::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
