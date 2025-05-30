<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AuthController;

// Route publik: siapa aja bisa lihat portofolio
Route::apiResource('portofolios', PortofolioController::class)->only(['index', 'show']);


Route::get('/test-users', function () {
    return response()->json(['count' => \App\Models\User::count()]);
});

Route::get('/photos', [PhotoController::class, 'index'])->middleware(\App\Http\Middleware\CorsMiddleware::class);

Route::post('/login', [AuthController::class, 'login'])->middleware(\App\Http\Middleware\CorsMiddleware::class);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route khusus admin (wajib login pakai sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('portofolios', PortofolioController::class)->except(['index', 'show']);
    Route::apiResource('photos', PhotoController::class)->only(['store', 'destroy']);
});
