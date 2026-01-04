<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    /** @use HasFactory<\Database\Factories\VendorFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'phone_number',
        'type',
        'link',
    ];

   public function products()
    {
        return $this->belongsToMany(Product::class, 'product_vendors', 'vendor_id', 'product_id')
                    ->withTimestamps();
    }
}
