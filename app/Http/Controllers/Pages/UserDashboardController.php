<?php

namespace App\Http\Controllers\Pages;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    //
    public function index()
    {
        return view('dashboard.user.index');
    }
    public function cart(){
        return view('dashboard.user.cart');
    }
}
