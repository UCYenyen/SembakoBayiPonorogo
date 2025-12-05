<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ShopController;
use App\Http\Controllers\Pages\AdminController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\CompleteProfileController;
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\AdminPageGuard;

Route::get('/', [HomeController::class, 'index']);
Route::get('/unauthorized', [AdminController::class, 'unauthorized']);
Route::get('/shop', [ShopController::class, 'index']);

// Google Auth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Profile Completion Routes (auth tapi belum complete profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile/complete', [CompleteProfileController::class, 'show'])->name('profile.complete');
    Route::post('/profile/complete', [CompleteProfileController::class, 'store'])->name('profile.complete.store');
    
    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Protected Routes (auth + profile complete)
Route::middleware(['auth', 'profile.complete'])->group(function () {
    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/checkout', [PaymentController::class, 'showCheckout'])->name('payment.checkout');
    Route::post('/transaction/create', [PaymentController::class, 'createTransaction'])->name('payment.create');
    Route::post('/midtrans/notification', [PaymentController::class, 'notificationHandler'])->name('midtrans.notification');
});

// Admin Routes
Route::middleware(['auth', 'profile.complete', AdminPageGuard::class])->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index']);
    Route::get('/dashboard/admin/products', [AdminController::class, 'products'])->name('admin.products.index');
    Route::get('/dashboard/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/dashboard/admin/products/create', [ProductController::class, 'create'])->name('admin.products.store');
    Route::get('/dashboard/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/dashboard/admin/products/{product}', [ProductController::class, 'editProduct'])->name('admin.products.update');
    Route::patch('/dashboard/admin/products/{product}/toggle-visibility', [ProductController::class, 'toggleVisibility'])->name('admin.products.toggle');
    Route::delete('/dashboard/admin/products/{product}', [ProductController::class, 'delete'])->name('admin.products.delete');
});

Route::get('/api/search', [ProductController::class, 'liveSearch'])->name('api.search');