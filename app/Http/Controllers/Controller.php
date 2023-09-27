<?php

namespace App\Http\Controllers;

use App;
use App\Models\File;
use App\Models\Product;
use App\Models\ProductCat;
use App\Models\ProductDynamic;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

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


    public function exProduct($id) {
        $pro = Product::find($id);
        $lang = Product::select('lang')->where('uniqueId',$pro->uniqueId)->get();
        $img = File::select('url')->where('pid',$pro->id)->where('type','products')->where('def',true)->first();
        $cat = ProductCat::select('title')->where('lang',$pro->lang)->where('uniqueId',$pro->mainCat)->first();
        $brand =\App\Models\Brand::select('title')->where('lang',$pro->lang)->where('uniqueId',$pro->brand)->first();
        $price = 0;
        $vahed = '';
        $dynamic = ProductDynamic::select('price')->where('pid',$pro->id)->where('status',true)->get();
        if ($dynamic->count()){
            $price = 999999999999999999999999999;
            foreach ($dynamic as $item){
                $prc = json_decode($item->price);
                if ((int)$prc->discount){
                    if ((int)$prc->discount < $price){
                        $price = (int)$prc->discount;
                        $vahed = $prc->vahed;
                    }
                }else{
                    if ((int)$prc->prc < $price){
                        $price = (int)$prc->prc;
                        $vahed = $prc->vahed;
                    }
                }
            }
        }

        return json_encode((object)['img'=>$img,'lang'=>$lang,'cat'=>$cat,'brand'=>$brand,'defaultLang'=>$pro->lang,'price'=>$price,'vahed'=>$vahed]);
    }
}
