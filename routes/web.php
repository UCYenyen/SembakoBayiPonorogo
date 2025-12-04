<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ShopController;
use App\Http\Controllers\Pages\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\AdminPageGuard;

Route::get('/', [HomeController::class, 'index']);
Route::get('/unauthorized', [AdminController::class, 'unauthorized']);
Route::get('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'register']);
Route::get('/shop', [ShopController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', AdminPageGuard::class])->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index']);
    Route::get('/dashboard/admin/products', [AdminController::class, 'products']);
    Route::get('/dashboard/admin/products/create', [AdminController::class, 'createProduct']);
    Route::post('/dashboard/admin/products', [ProductController::class, 'create']);
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('dashboard-admin');
});

require __DIR__ . '/auth.php';
