<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\ProductCat;
use App\Models\SearchEngin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ProductCatController extends Controller
{

    public function select($id = 0, Request $request){
        if (isset($request->unique)) {
            error_log($request->unique);
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
            $cat = ProductCat::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$cat){
                return null;
            }
            $seo = SearchEngin::where('pid', $cat->id)->where('type', 'cat_product')->first();
            $file = File::where('pid', $cat->id)->where('type', 'cat_product')->first();
            unset($seo->id);
            unset($file->id);
            unset($file->title);

            $res = array_merge((array)json_decode($cat),(array)json_decode($seo),(array)json_decode($file));
            return json_encode((object)$res);
        }
        //paginate
        //"total":20
        //"per_page":15,
        //"to":15,
        //"current_page":1,
        // data: {}
        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = ProductCat::where('cid', $id)->where('status',$request->status)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        //error_log($res);
        return collect($res);
    }
    public function selectByDetail($cid = 0, Request $request) {
        $lang =$request->header('Lang')??\App\Models\Setting::first()->lang;

        return ProductCat::where('cid', $cid)->where('status', 1)->where('lang', $lang)->get();
    }
    public function selectChildByLang($unique,Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
            error_log('lang '.$request->lang.' cid '.json_decode($unique));
        $res = ProductCat::where('cid', $unique)
            ->where('lang', $request->lang)
            ->where('status',1)
            ->orderBy('title','DESC')
            ->get();
        return $res;
    }
    public function insert(Request $request) {
        //App::setLocale('fa');

        $request['slug'] = str_replace(' ','_',$request['title']);
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $request->cid = 0;
        $request->uniqueId = uniqid();
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255','unique:product_cats'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);

        try {
            ProductCat::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'cid' => $request->cid,
                'uniqueId' => uniqid()
            ]);

            $cat = ProductCat::where('slug',$request->slug)->first();

            //if isset request file => set file(s) to addGallery

                if ($request->file) {
                    error_log($request->file);
                    Admin\File::serverSide([
                        'file' => $request->file,
                        'slug' => $request->slug,
                        'pid' => $cat->id,
                        'type' => 'cat_product'
                    ]);
                }

            //if text !== null or '' or isset text request set text to article
//            if (isset($request['text']) and !is_null($request['text'])) {
//                $this->article([
//                    'slug' => $request['slug'],
//                    'text' => $request['text']
//                ]);
//            }

            //add search engin options
            SearchEngin::create([
                'pid' => $cat->id,
                'type' => 'cat_product',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['meta_description'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);
        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }
        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function update(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;

        $cat = ProductCat::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);

        if ($cat){
            ProductCat::where('id',$cat->id)
                ->update([
                    'title' => $request->title,
                ]);


            if ($request->file) {
                //first delete file as public/uploads/cat_product and database


                error_log($request->file);
                Admin\File::serverSide([
                    'file' => $request->file,
                    'slug' => $request->slug,
                    'pid' => $cat->id,
                    'type' => 'cat_product'
                ]);
            }

            SearchEngin::where('type','cat_product')->where('pid',$cat->id)->update([
                'meta_key' => $request['meta_key'] ,
                'meta_description' => $request['meta_description'],
                'canonical' => $request['canonical']
            ]);
        }else {

            $request['slug'] = str_replace(' ','_',$request['title']);
            $request->cid = 0;
            $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'slug' => ['required', 'string', 'max:255','unique:product_cats'],
                'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
            ]);
            ProductCat::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'cid' => $request->cid,
                'uniqueId' => $request->unique
            ]);
            $cat = ProductCat::where('slug',$request->slug)->first();

            if ($request->file) {
                error_log($request->file);
                Admin\File::serverSide([
                    'file' => $request->file,
                    'slug' => $request->slug,
                    'pid' => $cat->id,
                    'type' => 'cat_product'
                ]);
            }
            SearchEngin::create([
                'pid' => $cat->id,
                'type' => 'cat_product',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['meta_description'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);
        }


        return json_encode((object)['status'=>true,'msg'=>'success']);


    }

    public function delete(Request $request) {
        $del = ProductCat::where('uniqueId',$request->unique)->get();

        foreach ($del as $item) {
            ProductCat::find($item->id)->update([
                'status' => 0
            ]);
        }

        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function CatExtend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = ProductCat::select('uniqueId')->where('id',$request->id)->first();
        $cid = ProductCat::select('id')->where('cid',$request->id)->get()->count();
        $lang = ProductCat::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        $img = File::select('url')->where('pid',$request->id)->where('type','cat_product')->first();
        return json_encode((object)['img'=>$img,'lang'=>$lang,'cid'=>$cid]);
    }
}
