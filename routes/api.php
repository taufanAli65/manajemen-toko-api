<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TokoController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    
    // Public routes
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected routes (require JWT authentication)
    Route::middleware('jwt.auth')->group(function () {
        
        // Superadmin only routes
        Route::middleware('role:superadmin')->group(function () {
            Route::get('/auth/users', [AuthController::class, 'listUsers']);
            
            // Toko management
            Route::post('/toko', [TokoController::class, 'store']);
            Route::put('/toko/{toko_id}', [TokoController::class, 'update']);
            Route::delete('/toko/{toko_id}', [TokoController::class, 'destroy']);
        });

        // Admin and Superadmin routes
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::post('/auth/register', [AuthController::class, 'register']);
            
            // Toko management
            Route::get('/toko', [TokoController::class, 'index']);
            Route::post('/toko/{toko_id}/assign', [TokoController::class, 'assignUser']);
            Route::delete('/toko/{toko_id}/users/{user_id}', [TokoController::class, 'removeUser']);
            Route::get('/toko/{toko_id}/users', [TokoController::class, 'listUsers']);
            
            // Product management
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{product_id}', [ProductController::class, 'update']);
            Route::delete('/products/{product_id}', [ProductController::class, 'destroy']);
        });

        // All authenticated users (superadmin, admin, kasir)
        Route::middleware('role:superadmin,admin,kasir')->group(function () {
            Route::post('/auth/logout', [AuthController::class, 'logout']);
            Route::get('/auth/me', [AuthController::class, 'me']);
            Route::put('/auth/users/{user_id}', [AuthController::class, 'update']);
            
            // Toko access
            Route::get('/toko/{toko_id}', [TokoController::class, 'show']);
            Route::get('/my-toko', [TokoController::class, 'myTokos']);
            
            // Product access
            Route::get('/products', [ProductController::class, 'index']);
            Route::get('/products/{product_id}', [ProductController::class, 'show']);
            
            // Transaction creation (all authenticated users)
            Route::post('/transactions', [TransactionController::class, 'store']);
            
            // Transaction listing and summary (role-based filtering applied in controller)
            Route::get('/transactions', [TransactionController::class, 'index']);
            Route::get('/transactions/summary', [TransactionController::class, 'summary']);
        });
    });
});
