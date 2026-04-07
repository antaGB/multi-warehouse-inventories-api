<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('/users', UserController::class);
});