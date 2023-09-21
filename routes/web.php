<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

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
    $user = User::find(23);
    $lang = 'EN';
    event(new \App\Events\RegisterNotyEvent($user, $lang));
    //dispatch(new \App\Listeners\SendRegisterListener($user));
    return 'welcome';
});

//Route::prefix('api/v1/')->middleware([\App\Http\Middleware\DataUserSave::class])->group(function () {
Route::prefix('api/v1/')->middleware(\App\Http\Middleware\DataUserSave::class)->group(function () {

    Route::prefix('product')->group(function () {
        Route::post('/select/{status?}', [\App\Http\Controllers\ProductController::class,'select']);
        Route::post('/insert', [\App\Http\Controllers\ProductController::class,'insert']);
        Route::post('/store', [\App\Http\Controllers\ProductController::class,'store']);
        Route::post('/update', [\App\Http\Controllers\ProductController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\ProductController::class,'delete']);
        Route::post('/delete_lang', [\App\Http\Controllers\ProductController::class,'deleteLang']);
        Route::post('/add_gallery', [\App\Http\Controllers\ProductController::class,'addGallery']);
        Route::post('/remove_gallery', [\App\Http\Controllers\ProductController::class,'removeGallery']);
        Route::post('/default_gallery', [\App\Http\Controllers\ProductController::class,'defaultGallery']);
        Route::post('/get_ext', [\App\Http\Controllers\ProductController::class,'extend']);
        Route::post('/set_settings', [\App\Http\Controllers\ProductController::class,'setSetting']);
        Route::post('/search_engin/insert', [\App\Http\Controllers\ProductController::class,'searchEnginInsert']);
        Route::post('/search_engin/select', [\App\Http\Controllers\ProductController::class,'searchEnginSelect']);
        Route::post('/get_dynamic', [\App\Http\Controllers\ProductController::class,'getDynamic']);
    });

    Route::prefix('product_cat')->group(function () {
        Route::post('/select/{id?}', [\App\Http\Controllers\ProductCatController::class,'select']);
        Route::post('/select_detail/{id?}', [\App\Http\Controllers\ProductCatController::class,'selectByDetail']);
        Route::post('/insert', [\App\Http\Controllers\ProductCatController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\ProductCatController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\ProductCatController::class,'delete']);
        Route::post('/get_ext', [\App\Http\Controllers\ProductCatController::class,'CatExtend']);
        Route::post('/select_child/{unique}', [\App\Http\Controllers\ProductCatController::class,'selectChildByLang']);
    });

    Route::prefix('depo')->group(function () {
        Route::post('/depo_servisce', [\App\Http\Controllers\DepoController::class,'depoServisce']);
        Route::post('/insert/depomain', [\App\Http\Controllers\DepoController::class,'insert']);
        Route::post('/select', [\App\Http\Controllers\DepoController::class,'select']);
    });

    Route::prefix('brand')->group(function () {
        Route::post('/select/{status?}', [\App\Http\Controllers\Brand::class,'select']);
        Route::post('/insert', [\App\Http\Controllers\Brand::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\Brand::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\Brand::class,'delete']);
        Route::post('/get_ext', [\App\Http\Controllers\Brand::class,'extend']);
        Route::post('/delete_lang', [\App\Http\Controllers\Brand::class,'deleteLang']);
        Route::post('/select_detail', [\App\Http\Controllers\Brand::class,'selectByDetail']);

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
        Route::post('/select/gp/cat', [\App\Http\Controllers\AttrController::class,'selectGpByCat']);
        Route::post('/insert', [\App\Http\Controllers\AttrController::class,'insert']);
        Route::post('/update', [\App\Http\Controllers\AttrController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\AttrController::class,'delete']);
        Route::post('/Attr_gp/get_ext', [\App\Http\Controllers\AttrController::class,'CatExtend']);
        Route::post('/get_ext', [\App\Http\Controllers\AttrController::class,'attrExtend']);
        Route::post('/delete_lang', [\App\Http\Controllers\AttrController::class,'deleteLang']);
    });

    Route::prefix('user')->group(function () {
        Route::post('/select/{id?}', [\App\Http\Controllers\UserController::class,'select']);
        Route::post('/trash_list', [\App\Http\Controllers\UserController::class,'trashList']);
        Route::post('/insert', [\App\Http\Controllers\UserController::class,'insert']);
        Route::post('/update/{id}', [\App\Http\Controllers\UserController::class,'update']);
        Route::post('/delete', [\App\Http\Controllers\UserController::class,'delete']);
        Route::get('/search', [\App\Http\Controllers\UserController::class,'search']);
    });

});
Route::get('/powered/{PIN}',function ($PIN) {
    if ($PIN == 1317){
        return 'powered by omid nami';
    }
    return 'bad request';
});

