<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerfilController;

/*
|--------------------------------------------------------------------------
| Preflight (CORS) — responde 204 a cualquier OPTIONS /api/*
|--------------------------------------------------------------------------
*/
Route::options('{any}', function () {
    return response()->noContent(); // 204
})->where('any', '.*');

/*
|--------------------------------------------------------------------------
| Endpoints públicos
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Endpoints protegidos
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [PerfilController::class, 'me']);
});