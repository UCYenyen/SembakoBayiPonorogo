<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        if ($search) {
            $products = Product::with(['category', 'brand'])
                ->where(function($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%");
                })
                ->where('is_hidden', false) 
                ->limit(20)
                ->get();
        } else {
            $products = collect();
        }
        
        $latestProducts = ProductController::getLatestProducts(8);
        
        return view('welcome', [
            'products' => $products,
            'latestProducts' => $latestProducts,
            'searchQuery' => $search
        ]);
    }
}
