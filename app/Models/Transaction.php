<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'payment_method',
        'delivery_id',
        'order_id',
        'shopping_cart_id',
        'address_id',
        'total_price',
        'delivery_price',
        'no_resi',
        'status',
        'snap_token',
    ];
    
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

    public function getSubtotalAttribute()
    {
        return $this->transaction_items->sum(fn($item) => $item->price * $item->quantity);
    }

    public function getTotalBillAttribute()
    {
        return $this->subtotal + $this->delivery_price;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    // Helper methods
    public function isPendingPayment()
    {
        return $this->status === self::STATUS_PENDING_PAYMENT;
    }

    public function isPaid()
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isShipped()
    {
        return $this->status === self::STATUS_SHIPPED;
    }

    public function isDelivered()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_PENDING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_PAID => 'Sudah Dibayar',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_SHIPPED => 'Sedang Dikirim',
            self::STATUS_DELIVERED => 'Sudah Diterima',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            self::STATUS_FAILED => 'Gagal',
            default => 'Unknown',
        };
    }
}