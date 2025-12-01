<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ProductController;

class ShopController extends Controller
{
    public function index()
    {
        return view('shop.index', ['allProducts' => ProductController::getAllProducts()]);
    }
}
