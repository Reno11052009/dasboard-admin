<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admin', [AdminController::class, 'index'])->name('index');
Route::get('/admin/users', [AdminController::class, 'users'])->name('users');
Route::get('/admin/products', [AdminController::class, 'products'])->name('products');

Route::get('/admin/products/create', function () {
    return view('product.create');
});
Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('products.store');

Route::get('/admin/products/{id}/edit', [AdminController::class, 'editProduct'])->name('product.edit');
Route::put('/admin/products/{id}', [AdminController::class, 'updateProduct'])->name('product.update');
Route::delete('/admin/products/{id}', [AdminController::class, 'deleteProduct'])->name('product.delete');

Route::get('/admin/sales', [AdminController::class, 'sales'])->name('sales');
