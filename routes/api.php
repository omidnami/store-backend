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
        Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class,'login']);
        Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class,'logout']);
        Route::post('/check', [\App\Http\Controllers\Admin\AuthController::class,'check']);
    });

    Route::prefix('product')->group(function () {
        Route::post('/select/{status?}', [\App\Http\Controllers\Admin\ProductController::class,'select']);
        Route::post('/insert', [\App\Http\Controllers\Admin\ProductController::class,'insert']);
        Route::post('/store', [\App\Http\Controllers\Admin\ProductController::class,'store']);
        Route::post('/update', [\App\Http\Controllers\Admin\ProductController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\Admin\ProductController::class,'delete']);
        Route::post('/delete_lang', [\App\Http\Controllers\Admin\ProductController::class,'deleteLang']);
        Route::post('/add_gallery', [\App\Http\Controllers\Admin\ProductController::class,'addGallery']);
        Route::post('/remove_gallery', [\App\Http\Controllers\Admin\ProductController::class,'removeGallery']);
        Route::post('/default_gallery', [\App\Http\Controllers\Admin\ProductController::class,'defaultGallery']);
        Route::post('/get_ext', [\App\Http\Controllers\Admin\ProductController::class,'extend']);
        Route::post('/set_settings', [\App\Http\Controllers\Admin\ProductController::class,'setSetting']);
        Route::post('/search_engin/insert', [\App\Http\Controllers\Admin\ProductController::class,'searchEnginInsert']);
        Route::post('/search_engin/select', [\App\Http\Controllers\Admin\ProductController::class,'searchEnginSelect']);
        Route::post('/get_dynamic', [\App\Http\Controllers\Admin\ProductController::class,'getDynamic']);
    });

    Route::prefix('product_cat')->group(function () {
        Route::post('/select/{id?}', [\App\Http\Controllers\Admin\ProductCatController::class,'select']);
        Route::post('/select_detail/{id?}', [\App\Http\Controllers\Admin\ProductCatController::class,'selectByDetail']);
        Route::post('/insert', [\App\Http\Controllers\Admin\ProductCatController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\Admin\ProductCatController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\Admin\ProductCatController::class,'delete']);
        Route::post('/get_ext', [\App\Http\Controllers\Admin\ProductCatController::class,'CatExtend']);
        Route::post('/select_child/{unique}', [\App\Http\Controllers\Admin\ProductCatController::class,'selectChildByLang']);
    });

    Route::prefix('depo')->group(function () {
        Route::post('/depo_servisce', [\App\Http\Controllers\Admin\DepoController::class,'depoServisce']);
        Route::post('/insert/depomain', [\App\Http\Controllers\Admin\DepoController::class,'insert']);
        Route::post('/select', [\App\Http\Controllers\Admin\DepoController::class,'select']);
    });

    Route::prefix('brand')->group(function () {
        Route::post('/select/{status?}', [\App\Http\Controllers\Admin\Brand::class,'select']);
        Route::post('/insert', [\App\Http\Controllers\Admin\Brand::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\Admin\Brand::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\Admin\Brand::class,'delete']);
        Route::post('/get_ext', [\App\Http\Controllers\Admin\Brand::class,'extend']);
        Route::post('/delete_lang', [\App\Http\Controllers\Admin\Brand::class,'deleteLang']);
        Route::post('/select_detail', [\App\Http\Controllers\Admin\Brand::class,'selectByDetail']);

    });

    Route::prefix('product_dynamic')->group(function () {
        Route::post('/select/{pid?}', [\App\Http\Controllers\Admin\ProductDynamicController::class,'select']);
        Route::post('/insert', [\App\Http\Controllers\Admin\ProductDynamicController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\Admin\ProductDynamicController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\Admin\ProductDynamicController::class,'delete']);
    });

    Route::prefix('attribute')->group(function () {
        Route::post('/select/{id?}', [\App\Http\Controllers\Admin\AttrController::class,'select']);
        Route::post('/select/cat/{cid?}', [\App\Http\Controllers\Admin\AttrController::class,'select_cat']);
        Route::post('/select/gp/cat', [\App\Http\Controllers\Admin\AttrController::class,'selectGpByCat']);
        Route::post('/insert', [\App\Http\Controllers\Admin\AttrController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\Admin\AttrController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\Admin\AttrController::class,'delete']);
        Route::post('/Attr_gp/get_ext', [\App\Http\Controllers\Admin\AttrController::class,'CatExtend']);
        Route::post('/get_ext', [\App\Http\Controllers\Admin\AttrController::class,'attrExtend']);
        Route::post('/delete_lang', [\App\Http\Controllers\Admin\AttrController::class,'deleteLang']);
    });

    Route::prefix('user')->group(function () {
        Route::post('/select/{id?}', [\App\Http\Controllers\Admin\UserController::class,'select']);
        Route::post('/trash_list', [\App\Http\Controllers\Admin\UserController::class,'trashList']);
        Route::post('/insert', [\App\Http\Controllers\Admin\UserController::class,'insert']);
        Route::post('/update/{id}', [\App\Http\Controllers\Admin\UserController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\Admin\UserController::class,'delete']);
        Route::get('/search', [\App\Http\Controllers\Admin\UserController::class,'search']);
    });
});

