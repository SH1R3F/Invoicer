<?php

use App\Http\Controllers\Api\V1\APIController;
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
