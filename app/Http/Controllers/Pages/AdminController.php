<?php

namespace App\Http\Controllers\Pages;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function unauthorized()
    {
        return view('unauthorized');
    }
    public function index()
    {
        $currentUser = Auth::user();
        return view('admin.index', [
            'adminName' => $currentUser->name
        ]);
    }
}
