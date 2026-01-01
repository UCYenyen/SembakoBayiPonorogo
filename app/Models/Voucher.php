<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'base_voucher_id',
        'user_id',
        'transaction_id',
        'shopping_cart_id'
    ];
    
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
    
    // Helper methods
    public function isAvailable()
    {
        return is_null($this->transaction_id);
    }
    
    public function isUsed()
    {
        return !is_null($this->transaction_id);
    }
}
