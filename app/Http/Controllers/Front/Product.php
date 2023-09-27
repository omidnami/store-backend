<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class Product extends Controller
{
    //

    function cat(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $pro = \App\Models\Product::where('lang', $request->lang)
            ->where('slug', '!=', '')
            ->orderBy('id','DESC')
            ->paginate(12);

        $res = [];
        foreach ($pro as $item){
            $ex = $this->exProduct($item->id);
            $ex = json_decode($ex);
            //error_log($ex);
            $item->cat = $ex->cat?$ex->cat->title:'';
            $item->brand = $ex->brand?$ex->brand->title:'';
            $item->price = $ex->price??0;
            $item->vahed = $ex->vahed??'';

            if ($ex->img){
                $item->img = $ex->img->url;
            }else{
                $item->img = '';
            }
            $res[] = $item;
        }
        if (count($res))
            $pro->data = $res;

        return collect($pro);
    }

    function single(Request $request) {

    }

    function search(Request $request) {

    }

}
