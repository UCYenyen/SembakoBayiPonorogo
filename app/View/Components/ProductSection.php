<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public static function getAllProducts()
    {
        return Product::with(['category', 'brand'])
            ->get();
    }
    public static function getLatestProducts($limit = 10)
    {
        return Product::with(['category', 'brand'])
            ->where('is_hidden', false)
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    public function getProductById(Product $product)
    {
        return view('product-details', ['product' => $product]);
    }
}
