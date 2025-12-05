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
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\AddressController; // ✅ Add this import
use App\Http\Middleware\AdminPageGuard;

Route::get('/', [HomeController::class, 'index']);
Route::get('/unauthorized', [AdminController::class, 'unauthorized']);
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/products/{product}', [ProductController::class, 'showDetails'])->name('product.show');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::middleware('auth')->group(function () {
    // Complete Profile
    Route::get('/complete-profile', [CompleteProfileController::class, 'show'])
        ->name('profile.complete')
        ->middleware('auth');
    Route::post('/complete-profile', [CompleteProfileController::class, 'store'])
        ->name('profile.complete.store')
        ->middleware('auth');

    // User Dashboard
    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/user/transactions/{transaction}', [UserDashboardController::class, 'show'])->name('user.transaction.show');

    // ✅ Address Management Routes
    Route::prefix('dashboard/user/addresses')->name('user.addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/create', [AddressController::class, 'create'])->name('create');
        Route::post('/', [AddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [AddressController::class, 'update'])->name('update');
        Route::patch('/{address}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
    });

    // Shopping Cart Routes
    Route::get('/cart', [ShoppingCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [ShoppingCartController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/item/{cartItem}', [ShoppingCartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/item/{cartItem}', [ShoppingCartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('/cart/clear', [ShoppingCartController::class, 'clearCart'])->name('cart.clear');

    // Payment Routes
    Route::get('/payment', [PaymentController::class, 'showCheckout'])->name('payment.checkout');
    Route::post('/payment/create', [PaymentController::class, 'createTransaction'])->name('payment.create');
    Route::get('/payment/finish/{transaction}', [PaymentController::class, 'paymentFinish'])->name('payment.finish');

    Route::post('/api/shipping-options', [PaymentController::class, 'getShippingOptions'])->name('api.shipping.options');
});

// Midtrans Notification (outside auth middleware)
Route::post('/payment/notification', [PaymentController::class, 'notificationHandler'])->name('payment.notification');

// Admin Routes
Route::prefix('/dashboard/admin')->middleware(['auth', AdminPageGuard::class])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/admin/products', [AdminController::class, 'products'])->name('admin.products.index');
    Route::get('/dashboard/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/dashboard/admin/products/create', [ProductController::class, 'create'])->name('admin.products.store');
    Route::get('/dashboard/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/dashboard/admin/products/{product}', [ProductController::class, 'editProduct'])->name('admin.products.update');
    Route::patch('/dashboard/admin/products/{product}/toggle-visibility', [ProductController::class, 'toggleVisibility'])->name('admin.products.toggle');
    Route::delete('/dashboard/admin/products/{product}', [ProductController::class, 'delete'])->name('admin.products.delete');
});

Route::get('/api/search', [ProductController::class, 'liveSearch'])->name('api.search');
