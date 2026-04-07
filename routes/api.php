<?php


use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum', 'role:super-admin'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
});