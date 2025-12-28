<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ShopController;
use App\Http\Controllers\Pages\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\VoucherController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/unauthorized', [AdminController::class, 'unauthorized']);

Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');

Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/products/{product}', [ProductController::class, 'showDetails'])->name('product.show');
Route::post('/payment/notification', [PaymentController::class, 'notificationHandler'])
    ->name('payment.notification');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/user/transactions/{transaction}', [UserDashboardController::class, 'show'])
        ->name('user.transaction.show');

    Route::prefix('dashboard/user/addresses')->name('user.addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/create', [AddressController::class, 'create'])->name('create');
        Route::post('/', [AddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
    });

    Route::get('/api/cities/search', [AddressController::class, 'searchCities'])->name('api.cities.search');
    Route::get('/api/cities/{subdistrictId}', [AddressController::class, 'getCityById'])->name('api.cities.show');

    Route::get('/cart', [ShoppingCartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [ShoppingCartController::class, 'addToCart'])->name('cart.add');
    Route::patch('/cart/item/{cartItem}', [ShoppingCartController::class, 'updateQuantity'])->name('cart.update');
    Route::delete('/cart/item/{cartItem}', [ShoppingCartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('/cart/clear', [ShoppingCartController::class, 'clearCart'])->name('cart.clear');

    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.checkout');
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/check-status', [PaymentController::class, 'checkStatus'])->name('payment.check-status');

    Route::post('/api/shipping-options', [PaymentController::class, 'getShippingOptions'])
        ->name('api.shipping.options');
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/dashboard/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/dashboard/admin/products/create', [AdminController::class, 'createProduct'])
        ->name('admin.products.create');
    Route::post('/dashboard/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/dashboard/admin/products/{product}/edit', [AdminController::class, 'editProduct'])
        ->name('admin.products.edit');
    Route::put('/dashboard/admin/products/{product}', [ProductController::class, 'update'])
        ->name('admin.products.update');
    Route::patch('/dashboard/admin/products/{product}/toggle', [ProductController::class, 'toggleVisibility'])
        ->name('admin.products.toggle');
    Route::delete('/dashboard/admin/products/{product}', [ProductController::class, 'destroy'])
        ->name('admin.products.delete');

    Route::get('/dashboard/admin/vouchers', [AdminController::class, 'vouchers'])->name('admin.vouchers.index');
    Route::get('/dashboard/admin/vouchers/create', [AdminController::class, 'createVoucher'])->name('admin.vouchers.create');
    Route::post('/dashboard/admin/vouchers', [VoucherController::class, 'store'])->name('admin.vouchers.store');
    Route::get('/dashboard/admin/vouchers/{baseVoucher}/edit', [AdminController::class, 'editVoucher'])->name('admin.vouchers.edit');
    Route::put('/dashboard/admin/vouchers/{baseVoucher}', [VoucherController::class, 'update'])->name('admin.vouchers.update');
    Route::delete('/dashboard/admin/vouchers/{baseVoucher}', [VoucherController::class, 'destroy'])->name('admin.vouchers.delete');

    Route::get('/dashboard/admin/transactions', [AdminController::class, 'transactions'])->name('admin.transactions.index');
    Route::get('/dashboard/admin/transactions/{transaction}', [AdminController::class, 'showTransaction'])->name('admin.transactions.show');
    Route::patch('/dashboard/admin/transactions/{transaction}/edit', [AdminController::class, 'updateTransactionStatus'])->name('admin.transactions.update-status');
});

require __DIR__ . '/auth.php';
