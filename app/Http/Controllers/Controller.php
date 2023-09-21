<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function construct(){
        App::setLocale('en');
        session()->put('locale', 'fa');
        if (!App\Models\conf::all()->count()){
            App\Models\conf::create([
                'title' => 'title your site',
                'domain' => 'example.com',
                'logo' => '',
                'icon' => '',
                'lang' => 'EN'
            ]);
        }
    }

    public function profile(Request $request)
    {
        $user = Auth::user();

        return response()->json(['user' => $user]);
    }
}
