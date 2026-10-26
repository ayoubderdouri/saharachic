<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ProductLandingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/produits/{product:slug}', [ProductLandingController::class, 'show'])->name('produits.show');
Route::post('/produits/{product:slug}/commander', [ProductLandingController::class, 'order'])
    ->middleware('throttle:5,1')
    ->name('produits.order');

// Only administrators can authenticate: registration is disabled.
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/orders');
    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::resource('products', Admin\ProductController::class)->except('show');
    Route::resource('colors', Admin\ColorController::class)->except('show');
    Route::resource('sizes', Admin\SizeController::class)->except('show');
    Route::post('products/{product}/variants', [Admin\ProductVariantController::class, 'store'])->name('variants.store');
    Route::post('products/{product}/variants/generate', [Admin\ProductVariantController::class, 'generate'])->name('variants.generate');
    Route::get('variants/{variant}/edit', [Admin\ProductVariantController::class, 'edit'])->name('variants.edit');
    Route::put('variants/{variant}', [Admin\ProductVariantController::class, 'update'])->name('variants.update');
    Route::patch('variants/{variant}', [Admin\ProductVariantController::class, 'quickUpdate'])->name('variants.quick-update');
    Route::delete('variants/{variant}', [Admin\ProductVariantController::class, 'destroy'])->name('variants.destroy');
    Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update']);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
