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

    public function gallery($id, $type) {
        return File::select('url')->where('pid',$id)->where('type', $type)->get();

    }

    public function tanavo($id) {
        $pro = Product::find($id);
        $depo = ProductDynamic::where('pid', $pro->uniqueId)
            ->where('lang', $pro->lang)
            ->where('status', true)->get();
        $res = [];
        foreach ($depo as $item) {
            $q = App\Models\Depo::where('productSk',$pro->sk)->where('dynamicId', $item->uniqueId)->sum('quty');
            $item->q = $q;
            $res[] = $item;
        }
        return $res;

    }

    public function attr($id) {}

    public function comments($id, $type) {}

    public function searchEngin($id, $type) {
        return App\Models\SearchEngin::where('pid',$id)->where('type', $type)->first();
    }

    public function price($id) {
        $pro = Product::find($id);
        $dynamic = $this->tanavo($id);
        // mojodi anbar
        $depo = [];
        // {dynamiqUnique:'575765', quty: 300, price: 2, discount: 0, date: 166587798544, vp:'' vd:''}
        foreach ($dynamic as $item) {
            $q = App\Models\Depo::where('productSk',$pro->sk)->where('dynamicId', $item->uniqueId)->sum('quty');
            if ($q){
                $depo[] = (object)[
                    'dynamic' => $item->uniqueId,
                    'quty' => $q,
                    'price' => json_decode($item->price)->prc,
                    'discount' => json_decode($item->price)->discount,
                    'date' => json_decode($item->price)->date,
                    'vp' => json_decode($item->price)->vahed,
                    'vd' => json_decode($item->depo)->vahed,
                ];
            }

        }
        $price = 99999999999999999999999;
        $discount = 99999999999999999999999;
        $dyna = '';
        $quty = 0;
        $date = 0;
        foreach ($depo as $item) {
            if ((float)$item->price > 0 AND (float)$item->price < (float)$price){
                $price = $item->price;
                $discount = (int)$item->discount === 0?99999999999999999999999:$item->discount;
                $dyna = $item->dynamic;
                $quty = $item->quty;
                $date = $item->date;
            }
            if ((float)$item->discount > 0 AND (float)$item->discount < (float)$discount AND $item->date > time()){
                $discount = $item->discount;
                $price = $item->price;
                $dyna = $item->dynamic;
                $quty = $item->quty;
                $date = $item->date;
            }
        }

        if ($discount === 99999999999999999999999)
            $discount = 0;

        return (object)['quty'=>$quty,'price'=>$price,'discount' => $discount, 'dynamic'=>$dyna,'date'=>$date];


    }

    public function depo($id) {
        $pro = Product::find($id);
        $dynamic = $this->tanavo($id);
        // mojodi anbar
        $depo = [];
        // {dynamiqUnique:'575765', quty: 300, price: 2, discount: 0, date: 166587798544, vp:'' vd:''}
        foreach ($dynamic as $item) {
            $q = App\Models\Depo::where('productSk',$pro->sk)->where('dynamicId', $item->uniqueId)->sum('quty');
            $depo[] = (object)[
                'dynamic' => $item->uniqueId,
                'quty' => $q,
                'price' => json_decode($item->price)->prc,
                'discount' => json_decode($item->price)->discount,
                'date' => json_decode($item->price)->date,
                'vp' => json_decode($item->price)->vahed,
                'vd' => json_decode($item->quty)->vahed,
            ];

        }

        return $depo;
    }

    public function article($id, $type) {
        return App\Models\Article::where('pid', $id)->where('type', $type)->first();
    }
}
