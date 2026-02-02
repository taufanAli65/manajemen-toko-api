<?php

use App\Http\Controllers\Api\V1\AuthController;
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
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (require JWT authentication)
    Route::middleware('jwt.auth')->group(function () {
        
        // Auth routes
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Example: Superadmin only routes
        Route::middleware('role:superadmin')->group(function () {
            // Add superadmin-only routes here
        });

        // Example: Admin and Superadmin routes
        Route::middleware('role:superadmin,admin')->group(function () {
            // Add admin routes here
        });

        // Example: All authenticated users (superadmin, admin, kasir)
        Route::middleware('role:superadmin,admin,kasir')->group(function () {
            // Add routes accessible by all roles here
        });
    });
});
