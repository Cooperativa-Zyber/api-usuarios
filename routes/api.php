<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AdminUsuariosController;

Route::post('/registro', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Perfil del usuario autenticado
    Route::get('/perfil',  [PerfilController::class, 'show']);
    Route::put('/perfil',  [PerfilController::class, 'update']);

    // Backoffice: aprobar/rechazar usuarios
    Route::middleware('is_admin')->group(function () {
        Route::get('/admin/usuarios/pendientes',   [AdminUsuariosController::class, 'pendientes']);
        Route::put('/admin/usuarios/{ci}/estado',  [AdminUsuariosController::class, 'setEstado']);
    });
});