<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DiscountController;

//Categorias
Route::get('categorias/trashed', [CategoryController::class, 'trashed']);
Route::put('categorias/{id}/restore', [CategoryController::class, 'restore']);
Route::delete('categorias/{id}/force', [CategoryController::class, 'forceDestroy']);
Route::apiResource('categorias', CategoryController::class);


//Produtos
Route::get('produtos/trashed', [ProductController::class, 'trashed']);
Route::put('produtos/{id}/restore', [ProductController::class, 'restore']);
Route::delete('produtos/{id}/force', [ProductController::class, 'forceDestroy']);
Route::apiResource('produtos', ProductController::class);

//Descontos
Route::apiResource('descontos', DiscountController::class);
Route::post('/descontos/{id}/sync', [\App\Http\Controllers\DiscountController::class, 'syncItems']);
Route::get('/descontos-lixeira', [\App\Http\Controllers\DiscountController::class, 'trashed']);
Route::post('/descontos/{id}/restore', [\App\Http\Controllers\DiscountController::class, 'restore']);
Route::delete('/descontos/{id}/force', [\App\Http\Controllers\DiscountController::class, 'forceDelete']);

//Encomendas
Route::get('/encomendas', [OrderController::class, 'index']);           // Listar todas (para a tabela)
Route::post('/encomendas', [OrderController::class, 'store']);          // Criar nova (Checkout)
Route::get('/encomendas/{order}', [OrderController::class, 'show']);    // Ver detalhes de uma
Route::put('/encomendas/{order}', [OrderController::class, 'update']);  // Atualizar (ex: o estado)
Route::delete('/encomendas/{order}', [OrderController::class, 'destroy']);