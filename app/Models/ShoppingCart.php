<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    
    const STATUS_ACTIVE = 'active';
    const STATUS_ORDERED = 'ordered';
    const STATUS_CHECKED_OUT = 'checked_out';
    
    protected $fillable = [
        'user_id',
        'status',
    ];
    
    protected $attributes = [
        'status' => self::STATUS_ACTIVE,
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function items()
    {
        return $this->hasMany(ShoppingCartItem::class);
    }
    
    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
    
    // Method untuk menghitung total diskon dari vouchers yang di-preview di cart ini
    public function getTotalVoucherDiscount()
    {
        return $this->vouchers()
            ->whereNull('transaction_id') // available vouchers only
            ->with('base_voucher')
            ->get()
            ->sum(fn($v) => $v->base_voucher->disc_amt);
    }
    
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    public function isCheckedOut()
    {
        return $this->status === self::STATUS_CHECKED_OUT;
    }
    
    public function isOrdered()
    {
        return $this->status === self::STATUS_ORDERED;
    }
}
