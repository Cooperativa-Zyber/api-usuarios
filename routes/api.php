<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\AdminUsuariosController;

Route::prefix('v1')->group(function () {
    Route::post('/registro', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me',      [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/perfil',  [PerfilController::class, 'show']);
        Route::put('/perfil',  [PerfilController::class, 'update']);

        Route::middleware('is_admin')->group(function () {
            Route::get('/admin/usuarios/pendientes',  [AdminUsuariosController::class, 'pendientes']);
            Route::put('/admin/usuarios/{ci}/estado', [AdminUsuariosController::class, 'setEstado']);
        });
    });
});
