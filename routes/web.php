<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/admin', [DashboardController::class, 'index'])->name('index');

Route::get('/admin/users', [UserController::class, 'index'])->name('users');
Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('users.update');
Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('users.delete');

Route::get('/admin/products', [ProductController::class, 'index'])->name('products');
Route::get('/admin/products/create', [ProductController::class, 'create'])->name('product.create');
Route::post('/admin/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
Route::put('/admin/products/{id}', [ProductController::class, 'update'])->name('product.update');
Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('product.delete');

Route::get('/admin/orders', [OrderController::class, 'index'])->name('orders');
Route::get('/admin/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
Route::put('/admin/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy'])->name('orders.delete');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
