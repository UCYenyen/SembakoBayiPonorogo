<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ShopController;
use App\Http\Controllers\Pages\AdminController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BaseVoucherController;
use App\Http\Controllers\TestimoniesController;
use App\Http\Controllers\VoucherController;
use App\Http\Middleware\AdminPageGuard;

Route::get('/', [HomeController::class, 'index']);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::get('/unauthorized', [AdminController::class, 'unauthorized']);

Route::get('/api/products/search', [ProductController::class, 'liveSearch'])->name('api.products.search');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/products/{product}', [ProductController::class, 'showDetails'])->name('product.show');
Route::post('/webhook/midtrans', [TransactionController::class, 'midtransNotification']);
Route::put('/webhook/shipping', [DeliveryController::class, 'handleWebhook']);


Route::middleware('auth')->group(function () {
    Route::get('/api/cities/search', [AddressController::class, 'searchCities'])->name('api.cities.search');

    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/user/transactions/{transaction}', [UserDashboardController::class, 'show'])
        ->name('user.transaction.show');
    Route::patch('/dashboard/user/transactions/{transaction}/complete', [TransactionController::class, 'complete'])
        ->name('user.transaction.complete');

    Route::prefix('dashboard/user/addresses')->name('user.addresses.')->group(function () {
        Route::get('/cities/{provinceId}', [AddressController::class, 'getCities'])->name('cities');
        Route::get('/districts/{cityId}', [AddressController::class, 'getDistricts'])->name('districts');
        Route::get('/sub-districts/{districtId}', [AddressController::class, 'getSubDistricts'])->name('sub-districts');
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/create', [AddressController::class, 'create'])->name('create');
        Route::post('/create', [AddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
        Route::patch('/{address}/set-default', [AddressController::class, 'setDefault'])->name('set-default');
    });

    Route::prefix('dashboard/user/testimonies')->name('user.testimonies.')->group(function () {
        Route::get('/create/{transactionItem}', [TestimoniesController::class, 'create'])->name('create');
        Route::post('/store', [TestimoniesController::class, 'store'])->name('store');
        Route::get('/view/{testimony}', [TestimoniesController::class, 'show'])->name('show');
        Route::get('/edit/{testimony}', [TestimoniesController::class, 'edit'])->name('edit');
        Route::patch('/update/{testimony}', [TestimoniesController::class, 'update'])->name('update');
        Route::delete('/delete/{testimony}', [TestimoniesController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('dashboard/user/vouchers')->name('user.vouchers.')->group(function () {
        Route::get('/', [VoucherController::class, 'index'])->name('index');
        Route::get('/create', [VoucherController::class, 'create'])->name('create');
        Route::post('/store', [VoucherController::class, 'store'])->name('store');
    });

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [ShoppingCartController::class, 'index'])->name('index');
        Route::post('/add/{product}', [ShoppingCartController::class, 'addToCart'])->name('add');
        Route::patch('/update/{cartItem}', [ShoppingCartController::class, 'updateQuantity'])->name('update');
        Route::delete('/remove/{cartItem}', [ShoppingCartController::class, 'removeItem'])->name('remove');
        Route::delete('/clear', [ShoppingCartController::class, 'clearCart'])->name('clear');

        Route::post('/voucher/apply', [ShoppingCartController::class, 'applyVoucher'])->name('voucher.apply');
        Route::delete('/voucher/remove', [ShoppingCartController::class, 'removeVoucher'])->name('voucher.remove');
    });

    Route::prefix('payment')->name('payment.')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('checkout');
        Route::post('/process', [PaymentController::class, 'processPayment'])->name('process');
        Route::get('/finish/{transaction}', [PaymentController::class, 'finish'])->name('finish');
        Route::get('/unfinish/{transaction}', [PaymentController::class, 'unfinish'])->name('unfinish');
        Route::post('/retry/{transaction}', [PaymentController::class, 'retryPayment'])->name('retry');
        Route::get('/check-status', [PaymentController::class, 'checkStatus'])->name('check-status');
    });

    Route::get('/check-ongkir/{address}', [DeliveryController::class, 'checkOngkir']);
    Route::post('/track-delivery/{transaction}', [DeliveryController::class, 'trackDelivery'])->name('track.package');

    Route::middleware(AdminPageGuard::class)->group(function () {
        Route::prefix('dashboard/admin')->name('admin.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('dashboard');
            Route::get('/products', [AdminController::class, 'products'])->name('products');
            Route::get('/products/create', [AdminController::class, 'createProduct'])
                ->name('products.create');
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])
                ->name('products.edit');
            Route::put('/products/{product}', [ProductController::class, 'update'])
                ->name('products.update');
            Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleVisibility'])
                ->name('products.toggle');
            Route::delete('/products/{product}', [ProductController::class, 'destroy'])
                ->name('products.delete');

            Route::get('/vouchers', [BaseVoucherController::class, 'showVouchers'])->name('vouchers.index');
            Route::get('/vouchers/create', [BaseVoucherController::class, 'createVoucher'])->name('vouchers.create');
            Route::post('/vouchers', [BaseVoucherController::class, 'store'])->name('vouchers.store');
            Route::get('/vouchers/{baseVoucher}/edit', [BaseVoucherController::class, 'editVoucher'])->name('vouchers.edit');
            Route::put('/vouchers/{baseVoucher}', [BaseVoucherController::class, 'update'])->name('vouchers.update');
            Route::delete('/vouchers/{baseVoucher}', [BaseVoucherController::class, 'destroy'])->name('vouchers.delete');
            Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors.index');
            Route::get('/vendors/create', [AdminController::class, 'createVendor'])->name('vendors.create');
            Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
            Route::get('/vendors/{vendor}/edit', [AdminController::class, 'editVendor'])->name('vendors.edit');
            Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
            Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.delete');

            Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions.index');
            Route::get('/transactions/{transaction}/detail', [AdminController::class, 'showTransaction'])->name('transactions.detail');
            Route::get('/transactions/{transaction}/edit', [AdminController::class, 'editTransaction'])
                ->name('transactions.edit');
            Route::patch('/transactions/{transaction}/update', [AdminController::class, 'updateTransactionStatus'])
                ->name('transactions.update-status');
        });
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

require __DIR__ . '/auth.php';
