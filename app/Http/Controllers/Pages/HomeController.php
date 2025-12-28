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
        $latestProducts = ProductController::getLatestProducts(8);
        
        return view('welcome', [
            'latestProducts' => $latestProducts
        ]);
    }
}