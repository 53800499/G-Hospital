<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/csrf-cookie', function () {
    return response()->json(['csrf_token' => csrf_token()]);
}); // ou juste utiliser /sanctum/csrf-cookie

Route::get('/', function () {
    return response()->json(' Le backend marche parfaitement');
});

// Toutes les routes protégées par Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('users', UserController::class);
});
