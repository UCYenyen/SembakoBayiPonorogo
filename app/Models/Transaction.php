<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
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

    // Status constants
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_FAILED = 'failed';

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

    public function getStatusBadgeClass()
    {
        return match($this->status) {
            self::STATUS_PENDING_PAYMENT => 'bg-yellow-100 text-yellow-800',
            self::STATUS_PAID => 'bg-blue-100 text-blue-800',
            self::STATUS_PROCESSING => 'bg-purple-100 text-purple-800',
            self::STATUS_SHIPPED => 'bg-indigo-100 text-indigo-800',
            self::STATUS_DELIVERED => 'bg-green-100 text-green-800',
            self::STATUS_COMPLETED => 'bg-gray-100 text-gray-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            self::STATUS_FAILED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
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