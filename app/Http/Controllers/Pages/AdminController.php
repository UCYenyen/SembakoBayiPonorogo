<?php

namespace App\Http\Controllers\Pages;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

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
}
