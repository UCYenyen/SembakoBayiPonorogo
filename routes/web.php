<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pages\HomeController;
use App\Http\Controllers\Pages\ShopController;
use App\Http\Controllers\Pages\AdminController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pages\UserDashboardController;
use App\Http\Middleware\AdminPageGuard;

Route::get('/', [HomeController::class, 'index']);
Route::get('/unauthorized', [AdminController::class, 'unauthorized']);
Route::get('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'register']);
Route::get('/shop', [ShopController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/user', [UserDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(AdminPageGuard::class)->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

require __DIR__.'/auth.php';
