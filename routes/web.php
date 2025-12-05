<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ShopController;
use App\Http\Controllers\Pages\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\AdminPageGuard;

Route::get('/', [HomeController::class, 'index']);
Route::get('/unauthorized', [AdminController::class, 'unauthorized']);
Route::get('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'register']);
Route::get('/shop', [ShopController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/checkout', [PaymentController::class, 'showCheckout'])->name('payment.checkout');

    // 2. Route untuk membuat transaksi Snap Token (POST, dipanggil via AJAX dari Blade)
    Route::post('/transaction/create', [PaymentController::class, 'createTransaction'])->name('payment.create');

    // 3. Route untuk Notifikasi Midtrans (IPN). Ini HARUS POST.
    // URL ini harus Anda daftarkan di Dashboard Midtrans Anda (Settings > Configuration > Payment Notification URL).
    Route::post('/midtrans/notification', [PaymentController::class, 'notificationHandler'])->name('midtrans.notification');
});

Route::middleware(['auth', AdminPageGuard::class])->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index']);
    
    // Product Routes
    Route::get('/dashboard/admin/products', [AdminController::class, 'products']);
    Route::get('/dashboard/admin/products/create', [AdminController::class, 'createProduct']);
    Route::post('/dashboard/admin/products', [ProductController::class, 'create']);
    Route::get('/dashboard/admin/products/{id}/edit', [ProductController::class, 'edit']);
    Route::patch('/dashboard/admin/products/{id}', [ProductController::class, 'editProduct']);
    Route::patch('/dashboard/admin/products/{id}/toggle-visibility', [ProductController::class, 'toggleVisibility']);
    Route::delete('/dashboard/admin/products/{id}', [ProductController::class, 'delete']);
});

require __DIR__ . '/auth.php';
