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

Route::prefix('product_cat')->group(function () {
    Route::post('/select/{id?}', [\App\Http\Controllers\ProductCatController::class,'select']);
    Route::post('/insert', [\App\Http\Controllers\ProductCatController::class,'insert']);
    Route::post('/update', [\App\Http\Controllers\ProductCatController::class,'update']);
    Route::post('/delete', [\App\Http\Controllers\ProductCatController::class,'delete']);
});

Route::prefix('product_dynamic')->group(function () {
    Route::post('/select/{pid?}', [\App\Http\Controllers\ProductDynamic::class,'select']);
    Route::post('/insert', [\App\Http\Controllers\ProductDynamic::class,'insert']);
    Route::post('/update', [\App\Http\Controllers\ProductDynamic::class,'update']);
    Route::post('/delete', [\App\Http\Controllers\ProductDynamic::class,'delete']);
});

Route::prefix('attribute')->group(function () {
    Route::post('/select/{id?}', [\App\Http\Controllers\AttrController::class,'select']);
    Route::post('/select/cat/{cid?}', [\App\Http\Controllers\AttrController::class,'select_cat']);
    Route::post('/insert', [\App\Http\Controllers\AttrController::class,'insert']);
    Route::post('/update', [\App\Http\Controllers\AttrController::class,'update']);
    Route::post('/delete', [\App\Http\Controllers\AttrController::class,'delete']);
});


Route::get('/powered/{PIN}',function ($PIN){
    if ($PIN == 1317){
        return 'powered by omid nami';
    }
    return 'bad request';
});
