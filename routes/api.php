<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoryController;

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

// Rutas públicas de autenticación
Route::prefix('auth')->group(function () {
    // Registro
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/verify-account', [RegisterController::class, 'verifyAccount']);
    
    // Login
    Route::post('/login', [LoginController::class, 'login']);
});

// Rutas protegidas
Route::middleware(['auth:sanctum'])->group(function () {
    // Logout
    Route::post('/auth/logout', [LoginController::class, 'logout']);
    
    // User routes
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    
    Route::get('/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'Dashboard del usuario',
            'user' => $request->user()
        ]);
    });

    // Categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    Route::get('/categories-tree', [CategoryController::class, 'tree']);
    Route::post('/categories/{id}/restore', [CategoryController::class, 'restore']);
});