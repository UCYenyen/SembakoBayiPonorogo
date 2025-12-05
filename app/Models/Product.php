<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $with = ['category', 'brand'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'stocks',
        'is_hidden',
        'discount_amount',
        'is_on_sale',
        'image_url',
        'image_public_id',
        'category_id',
        'brand_id',
    ];

    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return $this->with(['category', 'brand'])
    //         ->where($field ?? 'id', $value)
    //         ->firstOrFail();
    // }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
