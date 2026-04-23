<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admin', [AdminController::class, 'index'])->name('index');
Route::get('/admin/users', [AdminController::class, 'users'])->name('users');
Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('users.create');
Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('users.store');
Route::get('/admin/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
Route::get('/admin/products', [AdminController::class, 'products'])->name('products');

Route::get('/admin/products/create', function () {
    return view('product.create');
});
Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('products.store');

Route::get('/admin/products/{id}/edit', [AdminController::class, 'editProduct'])->name('product.edit');
Route::put('/admin/products/{id}', [AdminController::class, 'updateProduct'])->name('product.update');
Route::delete('/admin/products/{id}', [AdminController::class, 'deleteProduct'])->name('product.delete');

Route::get('/admin/sales', [AdminController::class, 'sales'])->name('sales');
