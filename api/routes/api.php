<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Customers
    Route::apiResource('customers', CustomerController::class);
    Route::post('/customers/{id}/restore', [CustomerController::class, 'restore']);

    // Products
    Route::apiResource('products', ProductController::class);

    // Orders
    Route::apiResource('orders', OrderController::class);
});
