<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'payment_id',
        'delivery_id',
        'shopping_cart_id',
        'address_id',
        'total_price',
        'delivery_price',
        'no_resi',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);

    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }
    public function shopping_cart()
    {
        return $this->belongsTo(ShoppingCart::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function transaction_items()
    {
        return $this->hasMany(TransactionItem::class);
    }
}