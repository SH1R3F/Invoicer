<?php

use App\Http\Controllers\Api\V1\APIController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;


/**
 * Just for Testing
 */
Route::get('/', function () {
    return 'Hello world, this is API!';
});


/**
 * Localization Endpoint
 */
Route::get('/localization', [APIController::class, 'localization']);


/**
 * Authentication Endpoints
 */
Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
});


/**
 * Authenticated Routes
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
});
