<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OderController;

// Base Automated Routes (GET, POST, PUT, DELETE)
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('discounts', DiscountController::class);
Route::apiResource('orders', OrderController::class);

// Personalized Routes (Pivot Tables)
Route::post('/discounts/{id}/products', [DiscountController::class, 'applyToProducts']);
Route::post('/discounts/{id}/categories', [DiscountController::class, 'applyToCategories']);