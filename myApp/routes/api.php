<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\UserController;

// Simple GET route to test
Route::get('/test-api', [TestController::class, 'testMethod']);

//--------------------Routes------

Route::prefix('v1/users')
     ->controller(UserController::class)
     ->group(function () {
         Route::get('/',       'index')->name('users.index');
         Route::get('/{id}',   'show')->name('users.show');
         Route::post('/',      'store')->name('users.store');
         Route::put('/{id}',   'updateUser')->name('users.update');
         Route::delete('/{id}','deleteUser')->name('users.destroy');
     });


