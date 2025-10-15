<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Rutas públicas de autenticación
Route::prefix('auth')->group(function () {
    // Registro
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/verify-account', [RegisterController::class, 'verifyAccount']);
    
    // Login
    Route::post('/login', [LoginController::class, 'login']);
});

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Logout
    Route::post('/auth/logout', [LoginController::class, 'logout']);
    
    // Dashboard y perfil de usuario
    Route::get('/dashboard', function (Request $request) {
        return response()->json([
            'message' => 'Dashboard del usuario',
            'user' => $request->user()
        ]);
    });
    
    // Obtener información del usuario actual
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
});

// Rutas públicas de productos (ejemplo)
Route::get('/products', function () {
    return response()->json([
        ['id' => 1, 'name' => 'Producto 1', 'price' => 100],
        ['id' => 2, 'name' => 'Producto 2', 'price' => 200],
    ]);
    
});