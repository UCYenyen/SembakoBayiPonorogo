<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'base_voucher_id',
        'user_id',
        'transaction_id',
        'shopping_cart_id'
    ];
    /** @use HasFactory<\Database\Factories\VoucherFactory> */
    public function base_voucher()
    {
        return $this->belongsTo(BaseVoucher::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function shopping_cart()
    {
        return $this->belongsTo(ShoppingCart::class);
    }
    use HasFactory;
}
