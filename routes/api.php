<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SatkerController;
use App\Http\Controllers\Api\PerkinController;
use App\Http\Controllers\Api\IkskController;
use App\Http\Controllers\Api\KinerjaHarianController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PeriodeController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Admin & General Base Routing (Implement roles logic in middleware/controller later as needed)
    Route::apiResource('users', UserController::class);
    Route::apiResource('satkers', SatkerController::class);
    Route::apiResource('periodes', PeriodeController::class);
    
    Route::post('perkins/import', [PerkinController::class, 'importExcel']);
    Route::apiResource('perkins', PerkinController::class);
    Route::post('perkins/{id}/assign-satker', [PerkinController::class, 'assignSatker']);
    
    Route::apiResource('iksks', IkskController::class);
    
    Route::apiResource('kinerja-harian', KinerjaHarianController::class);
    
    Route::get('/dashboard/bawahan', [DashboardController::class, 'pekerjaanBawahan']);
});
