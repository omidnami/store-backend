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
Route::prefix('wsd/v1/')->middleware(\App\Http\Middleware\DataUserSave::class)->group(function () {
    Route::post('settings/select',[\App\Http\Controllers\Front\Settings::class,'select'])
        ->middleware(\App\Http\Middleware\CheckDomain::class);

    Route::post('seo/select',[\App\Http\Controllers\Front\Settings::class,'select']);
    Route::post('page/select',[\App\Http\Controllers\Front\Page::class,'select']);
    Route::post('page/home',[\App\Http\Controllers\Front\Page::class,'home']);
    //plugins
    Route::post('plugins/slider',[\App\Http\Controllers\Front\Plugin::class,'slider']);
    Route::post('plugins/text',[\App\Http\Controllers\Front\Plugin::class,'text']);
    Route::post('plugins/service',[\App\Http\Controllers\Front\Plugin::class,'service']);
    Route::post('plugins/paralax',[\App\Http\Controllers\Front\Plugin::class,'paralax']);
    Route::post('plugins/product',[\App\Http\Controllers\Front\Plugin::class,'product']);
    Route::post('plugins/project',[\App\Http\Controllers\Front\Plugin::class,'project']);
    Route::post('plugins/menu',[\App\Http\Controllers\Front\Plugin::class,'menu']);
    Route::post('plugins/service_items',[\App\Http\Controllers\Front\Plugin::class,'serviceItems']);
    //product
    Route::post('product/cat',[\App\Http\Controllers\Front\Product::class,'cat']);
    Route::post('product/single',[\App\Http\Controllers\Front\Product::class,'single']);
    Route::post('product/search',[\App\Http\Controllers\Front\Product::class,'search']);
});
Route::get('/powered/{PIN}',function ($PIN) {
    if ($PIN == 1317){
        return 'powered by omid nami';
    }
    return 'bad request';
});

