<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1/')->middleware(\App\Http\Middleware\DataUserSave::class)->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/login', [\App\Http\Controllers\AuthController::class,'login']);
        Route::post('/logout', [\App\Http\Controllers\AuthController::class,'logout']);
        Route::post('/check', [\App\Http\Controllers\AuthController::class,'check']);
    });
});

