<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;

// Simple GET route to test
Route::get('/test-api', [TestController::class, 'testMethod']);

//--------------------Routes------


Route::prefix('v1/users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}',[UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'updateUser']);
    Route::delete('/{id}',[UserController::class, 'deleteUser']);
});

