<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    protected $with = ['category', 'brand'];

    protected $fillable = [
        'name',
        'description',
        'price',
        'weight',
        'stocks',
        'is_hidden',
        'discount_amount',
        'is_on_sale',
        'image_url',
        'category_id',
        'brand_id',
        'avg_rating',
    ];
    
    public function getImagePathAttribute()
    {
        if (Storage::disk('public')->exists($this->image_url)) {
            return asset('storage/' . $this->image_url);
        }
        $publicPath = 'images/products/' . basename($this->image_url);
        if (file_exists(public_path($publicPath))) {
            return asset($publicPath);
        }
        return asset('images/placeholder.jpg');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
