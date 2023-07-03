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
});
