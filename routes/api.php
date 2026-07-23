<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController; 

// ==========================================
// 🔓 ROTAS PÚBLICAS (Acesso livre para clientes)
// ==========================================

// Autenticação
Route::post('/login', [AuthController::class, 'login']);

// Catálogo (Listagens)
Route::get('/categorias', [CategoryController::class, 'index']);
Route::get('/produtos', [ProductController::class, 'index']);
Route::get('/descontos', [DiscountController::class, 'index']);

// Configurações e Encomendas
Route::get('/settings', [SettingController::class, 'index']);
Route::post('/encomendas', [OrderController::class, 'store']);


// ==========================================
// 🔒 ROTAS PRIVADAS / GESTÃO (Apenas Funcionários/Admins com Token)
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // 1. Autenticação (Sair e Validar Sessão)
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // 2. Gestão de Categorias (Lixeira + CRUDS)
    Route::get('categorias/trashed', [CategoryController::class, 'trashed']);
    Route::put('categorias/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('categorias/{id}/force', [CategoryController::class, 'forceDestroy']);
    Route::apiResource('categorias', CategoryController::class)->except(['index', 'show']);

    // 3. Gestão de Produtos (Lixeira + CRUDS)
    Route::get('produtos/trashed', [ProductController::class, 'trashed']);
    Route::put('produtos/{id}/restore', [ProductController::class, 'restore']);
    Route::delete('produtos/{id}/force', [ProductController::class, 'forceDestroy']);
    Route::apiResource('produtos', ProductController::class)->except(['index', 'show']);

    // 4. Gestão de Descontos (Lixeira, Sincronização + CRUDS)
    Route::post('/descontos/{id}/sync', [DiscountController::class, 'syncItems']);
    Route::get('/descontos-lixeira', [DiscountController::class, 'trashed']);
    Route::post('/descontos/{id}/restore', [DiscountController::class, 'restore']);
    Route::delete('/descontos/{id}/force', [DiscountController::class, 'forceDelete']);
    Route::apiResource('descontos', DiscountController::class)->except(['index']);
    
    // 5. Gestão de Encomendas (Listar, Editar Estado, Apagar)
    Route::get('/encomendas', [OrderController::class, 'index']);
    Route::get('/encomendas/{order}', [OrderController::class, 'show']);
    Route::put('/encomendas/{order}', [OrderController::class, 'update']);
    Route::delete('/encomendas/{order}', [OrderController::class, 'destroy']);

    // 6. Gestão de Utilizadores / Funcionários
    Route::apiResource('utilizadores', UserController::class);

    // 7. Gestão de Configurações da Plataforma
    Route::post('/settings', [SettingController::class, 'update']);

});

// ==========================================
// 🔓 ROTAS PÚBLICAS DINÂMICAS (Ficam no fim para não colidir)
// ==========================================
Route::get('/categorias/{categoria}', [CategoryController::class, 'show']);
Route::get('/produtos/{produto}', [ProductController::class, 'show']);