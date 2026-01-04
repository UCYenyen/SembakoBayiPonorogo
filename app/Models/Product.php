<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Testimony;

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
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function getAverageRatingAttribute()
    {
        if (array_key_exists('average_rating', $this->attributes)) {
            return round($this->attributes['average_rating'], 1);
        }

        $avgRating = Testimony::whereHas('transactionItem', function ($query) {
            $query->where('product_id', $this->id);
        })->avg('rating_star');

        return $avgRating ? round($avgRating, 1) : 0;
    }
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'product_vendors', 'product_id', 'vendor_id')
                    ->using(ProductVendor::class)
                    ->withTimestamps();
    }

    public function getTotalReviewsAttribute()
    {
        return Testimony::whereHas('transactionItem', function ($query) {
            $query->where('product_id', $this->id);
        })->count();
    }
}
