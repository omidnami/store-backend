<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;

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
//Route::prefix('api/v1/')->middleware([\App\Http\Middleware\DataUserSave::class])->group(function () {
Route::prefix('api/v1/')->group(function () {

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
        Route::post('/select/{pid?}', [\App\Http\Controllers\ProductDynamicController::class,'select']);
        Route::post('/insert', [\App\Http\Controllers\ProductDynamicController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\ProductDynamicController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\ProductDynamicController::class,'delete']);
    });

    Route::prefix('attribute')->group(function () {
        Route::post('/select/{id?}', [\App\Http\Controllers\AttrController::class,'select']);
        Route::post('/select/cat/{cid?}', [\App\Http\Controllers\AttrController::class,'select_cat']);
        Route::post('/insert', [\App\Http\Controllers\AttrController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\AttrController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\AttrController::class,'delete']);
    });

    Route::prefix('user')->group(function () {
        Route::get('/select/{id?}', [\App\Http\Controllers\UserController::class,'select']);
        Route::post('/insert', [\App\Http\Controllers\UserController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\UserController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\UserController::class,'delete']);
        Route::get('/search', [\App\Http\Controllers\UserController::class,'search']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/check_admin', [\App\Http\Controllers\AuthController::class,'checkAdmin']);
        Route::post('/login', [\App\Http\Controllers\AuthController::class,'login']);
        Route::post('/logout', [\App\Http\Controllers\AuthController::class,'logout']);
        Route::post('/check_login', function (Request $request){
            if ($request->token){
                $user = \App\Models\User::where('token',$request->token)->where('rol', 1)->first();
                        error_log($request->token);
                return $user;
            }
            return false;
        });
    });
});
Route::get('/powered/{PIN}',function ($PIN){
    if ($PIN == 1317){
        return 'powered by omid nami';
    }
    return 'bad request';
});

