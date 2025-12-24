<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $latestProducts = Product::latest()->take(6)->get();
        return view('welcome')->with('latestProducts', $latestProducts);
    }
}
