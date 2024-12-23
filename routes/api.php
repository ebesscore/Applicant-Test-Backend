<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\UserController;
use App\Enums\TokenAbility;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Auth
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login'); //TODO: put back on POST method
    Route::post('register', [AuthController::class, 'register'])->name('register'); //TODO: put back on POST method
    
    // Sanctum's
    Route::middleware('auth:sanctum', 'ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value)->group(function () {
        Route::get('refresh-token', [AuthController::class, 'refreshToken']);
    });
    Route::middleware('auth:sanctum', 'ability:' . TokenAbility::ACCESS_API->value)->get('/me', function (Request $request) {
        return response()->json($request->user());
    });
});

// Users
Route::prefix('users')->group(function() {
    Route::get('{id}/devices', [UserController::class, 'getDevices']);
    Route::get('{id}', [UserController::class, 'show']);
});

// Devices
Route::prefix('devices')->group(function() {
    Route::get('{id}', [DeviceController::class, 'show']);
});