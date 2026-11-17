<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Users\OrderController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Vendors\VendorController;
use Illuminate\Support\Facades\Route;

Route::get('/up', static fn () => response()->json(['status' => 'ok'], 200))->name('health');

Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/policy', [FrontendController::class, 'policy'])->name('policy');
Route::get('/tos', [FrontendController::class, 'tos'])->name('tos');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/search', [SearchController::class, 'search'])->name('search');
Route::get('/product', [ProductController::class, 'show'])->name('product.show');

Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');
Route::post('/reset', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.update');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:web')->prefix('user')->group(function () {
    Route::get('/profile', [UserController::class, 'index'])->name('user.profile');
    Route::get('/profile/edit/{id}', [UserController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [UserController::class, 'update'])->name('profile.update');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/remove/{productId}', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/paypal/payment', [PayPalController::class, 'createPayment'])->name('paypal.create');
    Route::get('/paypal/payment/execute', [PayPalController::class, 'executePayment'])->name('paypal.execute');
    Route::get('/paypal/payment/cancel/{reference}', [PayPalController::class, 'cancelPayment'])->name('paypal.cancel');
});

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors.index');
    Route::patch('/vendors/{vendor}', [AdminController::class, 'updateVendor'])->name('vendors.update');
    Route::get('/products', [AdminController::class, 'products'])->name('products.index');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/support', [AdminController::class, 'support'])->name('support');
});

Route::middleware('auth:vendor')->prefix('vendor')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'index'])->name('vendor.dashboard');
    Route::post('/profile', [VendorController::class, 'updateProfile'])->name('vendor.profile.update');
    Route::post('/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::put('/products/{product}', [VendorController::class, 'updateProduct'])->name('vendor.products.update');
    Route::delete('/products/{product}', [VendorController::class, 'destroyProduct'])->name('vendor.products.destroy');
    Route::patch('/orders/{order}/status', [VendorController::class, 'updateOrderStatus'])->name('vendor.orders.status');
});
