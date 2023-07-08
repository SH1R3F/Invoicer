<?php

use App\Http\Controllers\Api\V1\APIController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\RoleController;
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
    // Fetch Logged-in user data
    Route::get('/user', [ProfileController::class, 'user']);

    // Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Account settings
    Route::prefix('/account-settings')->group(function () {
        Route::put('/account', [ProfileController::class, 'profile']);
        Route::put('/password', [ProfileController::class, 'password']);
        Route::post('/deactive', [ProfileController::class, 'deactive']);
    });

    /**
     * Roles & Users management
     */
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);
});
