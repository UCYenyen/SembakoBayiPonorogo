<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;
    
    // ✅ Add status constants matching migration ENUM values
    const STATUS_ACTIVE = 'active';
    const STATUS_ORDERED = 'ordered';
    const STATUS_CHECKED_OUT = 'checked_out';
    
    protected $fillable = [
        'user_id',
        'status',
    ];
    
    // ✅ Set default status
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
    
    // ✅ Helper methods
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
