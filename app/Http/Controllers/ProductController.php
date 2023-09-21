<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Models\DepoLocation;
use App\Models\File;
use App\Models\Product;
use App\Models\ProductAttr;
use App\Models\ProductCat;
use App\Models\ProductDynamic;
use App\Models\SearchEngin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ProductController extends Controller
{

    public function select($status = 1, Request $request) {
        if (isset($request->unique)) {
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
            //error_log($request->lang);
            $product = Product::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$product){
                return json_encode((object)['status' => false, 'msg' => 'New_Lang', 'lang' => $request->lang]);
            }
            $seo = SearchEngin::where('pid', $product->id)->where('type', 'products')->first();
            $file = File::where('pid', $product->id)->where('type', 'products')->get();
            $art = Article::where('pid', $product->id)->where('type', 'products')->first();
            $attr = ProductAttr::where('pid', $product->id)->first();
            unset($seo->id);
            unset($seo->type);
            unset($art->id);
            unset($seo->cat);
            unset($product->text);
            unset($art->type);
            if ($file->count()) {
                $file->alt = '';
                unset($file->id);
                unset($file->title);
//              unset($file->title);
                unset($file->data);
                unset($file->type);
            }

            $res = array_merge((array)json_decode($product),(array)json_decode($seo),['img'=>json_decode($file)],(array)json_decode($art),['attr'=>json_decode($attr)]);
            error_log(json_encode($res));
            return $res;
        }


        //paginate
        //"total":20
        //"per_page":15,
        //"to":15,
        //"current_page":1,
        // data: {}
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;

        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = Product::where('status', $status)->where('slug','!=',null)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        error_log($res);
        return collect($res);
    }

    public function store(Request $request) {
        $perProduct = Product::select('id')->where('sk','>',100000)->where('slug',null)->where('user',1)->first();
        if ($perProduct){
            $perProduct->delete();
        }
        $getProduct = Product::select('sk')->orderBy('sk','DESC')->first();
        if (!$getProduct){
            $sk = 100001;
        }else{
            $sk = (int)$getProduct->sk+1;
        }

        $request['sk'] = $sk;
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $request->uniqueId = uniqid();
        $request->validate([
            'cat' => ['required', 'string'],
            'sk' => ['required', 'max:8','unique:products'],
        ]);

        try {
            Product::create([
                'cat' => $request->cat,
                'lang' => $request->lang,
                'uniqueId' => $request->uniqueId,
                'sk' => $request->sk,
                'user' => 1
            ]);

            return json_encode([
                'status'=>true,
                'msg'=>'stored',
                'data'=> Product::where('uniqueId', $request->uniqueId)->where('lang',$request->lang)->where('sk',$request->sk)->first()
            ]);

        }catch (Exception $e){

           return ResponseHelper::error($e->getMessage(), false);
        }


    }

    public function insert(Request $request) {
        //lang
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;

        //slug
        $request['slug'] = str_replace(' ','_',$request['title']);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255','unique:products'],
            'model' => ['required', 'string', 'max:255'],
        ]);
        // get product
        $id = $request->id;
        $box = (object)['wtype' => $request->wtype,'atype' => $request->atype,
        'l' => $request->box['l'], 'h' => $request->box['h'], 'w' => $request->box['w'], 'v' => $request->box['v']
        ];
        //$box =
//        $box = $request->box;
//        $box->wtype = $request->wtype;
//        $box->atype = $request->atype;

        error_log($request->unique);

        $product = [
            'title' => $request->title,
            'lang' => $request->lang,
            'uniqueId' => ($request->unique==='' or $request->unique === 'insert')?uniqid():$request->unique,
            'brand' => $request->brand,
            'model' => $request->model,
            'type' => $request->type,
            'noghat' => json_encode((object)['zaf' => $request->zaf, 'ghovat' => $request->ghovat]),
            'orginal' => $request->orginal,
            'box' => json_encode($box),
            'cat' => $request->cat
        ];

        $pro = Product::find($request->id);
        if ($pro->slug === null){
            $product['slug'] = $request['slug'];
        }
        $proSk = Product::where('uniqueId',$request->unique)->get();
        error_log($proSk->count());
        if ($proSk->count()){
            $product['sk'] = $proSk[0]->sk;
        }

        $article = [
            'pid' => $id,
            'type' => 'products',
            'text' => $request->text
        ];
        $seo = [
            'pid' => $id,
            'type' => 'products',
            'meta_key' => $request['meta_key'] ?? null,
            'meta_description' => $request['desc'] ?? null,
            'canonical' => $request['canonical'] ?? null
        ];
        $attr = [
            'pid' => $id,
            'data' => json_encode($request->attr)
        ];
        try {
            //edit pro
            Product::find($id)->update($product);

        }catch (Exception $e){
            return ResponseHelper::error($e->getMessage(), false);
        }
        error_log(json_encode($request->attr));
        try {
            $a = Article::where('pid',$id)->where('type','products')->get();
            $s = SearchEngin::where('pid',$id)->where('type','products')->get();
            $at = ProductAttr::where('pid',$id)->get();
            error_log($s);
            if (!$a->count()){

                Article::create($article);
            }else {
                //edit article
                $a[0]->update([
                    'text' => $request->text
                ]);
            }
            if (!$s->count()){
                SearchEngin::create($seo);
            }
            if (!$at->count()){
                ProductAttr::create($attr);
            }else{
                //edit attr
                $at[0]->update([
                    'data' => json_encode($request->attr)
                ]);
            }

        }catch (Exception $e){
            error_log('op');

            return ResponseHelper::error($e->getMessage(), false);
        }


        return json_encode((object)['status'=>true,'msg'=>'inserted']);
    }

    public function update(Request $request) {
        if (!isset($request['id'])){
            return ResponseHelper::error(__('response.id_ex',false));
        }

        Product::where('id',$request->id)
            ->update($request->all());

        return ResponseHelper::success(__('response.product_updated'));
    }

    public function delete(Request $request) {
        $del = Product::where('uniqueId',$request->unique)->get();

        foreach ($del as $item) {
            Product::find($item->id)->update([
                'status' => !$item->status
            ]);
        }

        error_log('delete: '.$del);

        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function deleteLang(Request $request) {
        if ($request->id) {
            $b=Product::find($request->id);
            $b->delete(); //returns true/false
            $s=SearchEngin::where('pid',$request->id)->where('type','products');
            $s->delete();
            $a=Article::where('pid',$request->id)->where('type','products');
            $a->delete();
            $f=File::where('pid',$request->id)->where('type','products')->get();
            //del imge in upload folder
            if ($f->count()){
                foreach ($f as $i){
                    File::find($i->id)->delete();
                    \App\Http\Controllers\File::fileDelete($i->url);
                }
            }
            //tanavo
            $d=ProductDynamic::where('pid',$request->id)->get();
            if ($d->count()){
                foreach ($d as $i) {
                    ProductDynamic::find($i->id)->delete();
                }
            }
            //attr
            $attr=ProductAttr::where('pid',$request->id)->first();
            $attr->delete();
        }
        return json_encode((object)['status'=>true,'msg'=>'success']);

    }
    public function addGallery(Request $request) {
//         add single file to gallery
//         file uploads and blob
//         after upload file return boolean status
        $request->validate([
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $product = Product::select('id')->where('uniqueId',$request->unique)->where('lang', $request->lang)->first();
        $selectFile = File::select('id')->where('pid',$product->id)->where('type','products')->where('def',1)->first();
        error_log($selectFile);
        if ($request->file) {
            //error_log($request->file);
           $res= \App\Http\Controllers\File::serverSide([
                'file' => $request->file,
                'slug' => 'product_img',
                'pid' => $product->id,
                'type' => 'products',
                'def' => !(bool)$selectFile
            ]);
           return json_encode((object)['status'=>true,'msg'=>'uploaded',
               'data' => File::where('pid', $product->id)->where('type','products')->orderBy('id','desc')->get()
               ,'pid'=>$product->id]);
        }
        return json_encode((object)['status'=>false,'msg'=>'image not fond']);
    }

    public function removeGallery(Request $request) {
        $file = File::where('url',$request->url)->where('type','products')->first();
        $def = $file->def;
        $file->delete();
        if ($def) {
            error_log('def');
            error_log($request->pid);

            //set other file to def true
            $default = File::where('pid',$request->pid)->where('type','products')->first();
            if ($default){
                error_log('hase a file');
                $default->update([
                   'def' => true
                ]);
            }
        }
        //remove file

        \App\Http\Controllers\File::fileDelete($request->url);

        return json_encode((object)[
            'status' => true,
            'msg' => 'removed',
            'name' => $request->name,
            'data' => File::where('pid', $request->pid)->where('type','products')->orderBy('id','desc')->get()
        ]);
    }

    public function defaultGallery(Request $request) {
        File::where('pid',$request->pid)->where('type','products')->update(['def' => false]);
        File::find($request->id)->update(['def' => true]);
        return json_encode((object)[
            'status' => true,
            'msg' => 'defaulted',
            'name' => $request->name,
            'data' => File::where('pid', $request->pid)->where('type','products')->orderBy('id','desc')->get()
        ]);
    }

    public function extend(Request $request) {
        if (!isset($request->id))
            return false;

        $pro = Product::select('uniqueId','mainCat','brand','lang')->where('id',$request->id)->first();

        $lang = Product::select('lang')->where('uniqueId',$pro->uniqueId)->get();
        $img = File::select('url')->where('pid',$request->id)->where('type','products')->where('def',true)->first();
        $cat = ProductCat::select('title')->where('lang',$pro->lang)->where('uniqueId',$pro->mainCat)->first();
        $brand =\App\Models\Brand::select('title')->where('lang',$pro->lang)->where('uniqueId',$pro->brand)->first();
        $price = 0;
        $vahed = '';
        $dynamic = ProductDynamic::select('price')->where('pid',$request->id)->where('status',true)->get();
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
    public function setSetting(Request $request) {

        $p = Product::where('uniqueId',$request->unique)->get();
        foreach ($p as $item){
            error_log($request->unique.' - omid');
            try {
                Product::find($item->id)->update([
                    'settings' => json_encode($request->settings)
                ]);
            }catch (Exception $e){
                return $e;
            }

        }

        return json_encode((object)[
            'status' => true,
            'msg' => 'setting_change'
        ]);
    }

    public function searchEnginInsert(Request $request) {
        $product = Product::find($request->pid);
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meta_key' => ['required', 'string', 'max:255'],
            'des' => ['required', 'string', 'max:255'],
            'canonical' => ['required', 'string', 'max:255'],
        ]);

        if ($product->slug !==
            $request->slug){
            $request->validate([
                'slug' => ['required', 'string', 'max:255','unique:products'],
            ]);
        }

        try {
            SearchEngin::where('pid',$request->pid)->where('type','products')->update([
                'meta_key' => $request['meta_key'],
                'meta_description' => $request['des'],
                'canonical' => $request['canonical']
            ]);
            Product::find($request->pid)->update([
                'title'=>$request->title,
                'slug' => $request->slug
            ]);
        }catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)[
            'status' => true,
            'msg' => 'success'
        ]);

    }
    public function searchEnginSelect(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        //error_log($request->lang);
        $product = Product::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
        if (!$product){
            return null;
        }
        $seo = SearchEngin::where('pid', $product->id)->where('type', 'products')->first();


        return (object)[
            'status' =>true,
            'title' => $product->title,
            'id' => $product->id,
            'slug' => $product->slug,
            'meta_key' => $seo->meta_key??json_encode([]),
            'des' => $seo->meta_description??'',
            'canonical' => $seo->canonical??''
        ];
    }

    public function getDynamic(Request $request) {
        $lang = $request->header('Lang')??\App\Models\Setting::first()->lang;
        $pro = Product::select('id')->where('uniqueId',$request->unique)->where('lang',$lang)->first();
        $dynamic = ProductDynamic::select('type','value','depo', 'id')
            ->where('status',true)
            ->where('pid', $pro->id)->get();
        $depos = DepoLocation::all();
        return json_encode((object)[
            'msg'=>'dynamic_set',
            'data'=> $dynamic,
            'depos'=> $depos
        ]);
    }

}
