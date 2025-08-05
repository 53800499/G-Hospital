<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PatientController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/', function () {
    return response()->json(' Le backend marche parfaitement');
});

// Toutes les routes protégées par Sanctum (cookies/session)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('users', UserController::class);

    // Routes pour les patients
    Route::apiResource('patients', PatientController::class);
    Route::get('/patients/{id}/export-pdf', [PatientController::class, 'exportPatientPdf']);
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients/gender', [PatientController::class, 'getByGender'])->name('patients.by-gender');
});