<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('product')->group(function () {
    Route::post('/select/{id?}', [\App\Http\Controllers\ProductController::class,'select']);
    Route::post('/insert', [\App\Http\Controllers\ProductController::class,'insert']);
    Route::post('/update', [\App\Http\Controllers\ProductController::class,'update']);
    Route::post('/delete', [\App\Http\Controllers\ProductController::class,'delete']);
});
