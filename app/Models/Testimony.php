<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimony extends Model
{
    /** @use HasFactory<\Database\Factories\TestimoniesFactory> */
    use HasFactory;
    protected $fillable = [
        'transaction_item_id',
        'rating',
        'description',
    ];
    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
