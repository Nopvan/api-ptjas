<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StatController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;

// Route publik: siapa aja bisa lihat portofolio
Route::apiResource('portofolios', PortofolioController::class)->only(['index', 'show']);


Route::get('/test-users', function () {
    return response()->json(['count' => \App\Models\User::count()]);
});

Route::get('/photos', [PhotoController::class, 'index'])->middleware(\App\Http\Middleware\CorsMiddleware::class);

Route::get('/portofoliosindex', [PortofolioController::class, 'getPortfolioforIndex']);

Route::get('/portofoliosindex/{id}', [PortofolioController::class, 'showPortfolioforIndex']);

Route::post('/login', [AuthController::class, 'login'])->middleware(\App\Http\Middleware\CorsMiddleware::class);

Route::get('/stats', [StatController::class, 'index']);

// Semua item, bisa search dan filter kategori
Route::get('/public-items', [ItemController::class, 'publicIndex']);

// List kategori publik (misal untuk dropdown filter)
Route::get('/public-categories', [CategoryController::class, 'index']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Route khusus admin (wajib login pakai sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('portofolios', PortofolioController::class)->except(['index', 'show']);
    Route::apiResource('photos', PhotoController::class)->only(['store', 'destroy']);

    // ADMIN: CRUD items & categories
    Route::apiResource('items', ItemController::class);
    Route::apiResource('categories', CategoryController::class);
});
