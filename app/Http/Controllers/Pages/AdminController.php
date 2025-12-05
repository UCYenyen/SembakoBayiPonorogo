<?php

namespace App\Http\Controllers\Pages;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;

class AdminController extends Controller
{
    public function unauthorized()
    {
        return view('unauthorized');
    }
    
    public function index()
    {
        $currentUser = Auth::user();
        return view('dashboard.admin.index', [
            'adminName' => $currentUser->name
        ]);
    }
    
    public function products()
    {
        // Get all products with pagination
        $products = Product::with(['category', 'brand'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('dashboard.admin.products.index', [
            'products' => $products
        ]);
    }
    
    public function createProduct()
    {
        $categories = Category::all();
        $brands = Brand::all();
        
        return view('dashboard.admin.products.create', [
            'categories' => $categories,
            'brands' => $brands
        ]);
    }
}
