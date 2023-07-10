<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\APIController;
use App\Http\Controllers\Api\V1\TaxController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\QuoteController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;


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
    Route::resource('/roles', RoleController::class)->except(['create', 'show', 'edit']);
    Route::get('/users/export', [UserController::class, 'export']);
    Route::resource('/users', UserController::class)->except(['create', 'edit']);

    /**
     * Categories management
     */
    Route::resource('/categories', CategoryController::class)->except(['create', 'edit']);

    /**
     * Products management
     */
    Route::get('/products/export', [ProductController::class, 'export']);
    Route::resource('/products', ProductController::class)->except(['create', 'edit']);

    /**
     * Taxes management
     */
    Route::resource('/taxes', TaxController::class)->except(['create', 'edit']);

    /**
     * Quotes management
     */
    Route::resource('/quotes', QuoteController::class)->except(['create', 'edit']);
});
