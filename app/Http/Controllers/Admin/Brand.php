<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\SearchEngin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class Brand extends Controller
{
    //
    public function select($status = 1,Request $request) {
        if (isset($request->unique)) {
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
            error_log($request->lang);
            $brand = \App\Models\Brand::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$brand){
                return null;
            }
            $seo = SearchEngin::where('pid', $brand->id)->where('type', 'brand')->first();
            $file = \App\Models\File::where('pid', $brand->id)->where('type', 'brand')->first();
            $art = Article::where('pid', $brand->id)->where('type', 'brand')->first();
            unset($seo->id);
            if ($file) {
                $file->alt = $file->title;
                unset($file->id);
                unset($file->title);
//              unset($file->title);
                unset($file->data);
            }

            $res = array_merge((array)json_decode($brand),(array)json_decode($seo),(array)json_decode($file),(array)json_decode($art));
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

        $res = \App\Models\Brand::where('status', $status)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        error_log($res);
        return collect($res);
    }
    public function selectByDetail(Request $request) {
        $lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        error_log($lang);
        return \App\Models\Brand::where('status', 1)->where('lang', $lang)->get();
    }

    public function insert(Request $request) {
        $request['slug'] = str_replace(' ','_',$request['title']);
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $request->uniqueId = uniqid();

        $request->validate([
            "title" => ['required', 'string', 'max:255'],
            "slug" => ['required', 'string', 'max:255', 'unique:brands'],
            "country" => ['required', 'string', 'max:255']
        ]);

        try {
            \App\Models\Brand::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'uniqueId' => $request->uniqueId,
                'lang' => $request->lang,
                'data' => json_encode((object)['country'=>$request->country])
            ]);

            $brand = \App\Models\Brand::where('slug',$request->slug)->first();

            //if isset request file => set file(s) to addGallery

            if ($request->file) {
                error_log($request->file);
                Admin\File::serverSide([
                    'file' => $request->file,
                    'slug' => $request->slug,
                    'pid' => $brand->id,
                    'type' => 'brand'
                ]);
            }

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $brand->id,
                'type' => 'brand',
                'text' => $request->text
            ]);

            //add search engin options
            SearchEngin::create([
                'pid' => $brand->id,
                'type' => 'brand',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function update(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;

        $brand = \App\Models\Brand::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);

        if ($brand) {
            error_log('update');

            //update brand article serchengin
            \App\Models\Brand::find($brand->id)->update([
                'title' => $request->title,
                'data' => json_encode((object)['country'=>$request->country])
            ]);

            Article::where('pid',$brand->id)->where('type','brand')->update([
                'text' => $request->text
            ]);
            SearchEngin::where('pid',$brand->id)->where('type','brand')->update([
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }

        else {
            error_log('insert');

            $request['slug'] = str_replace(' ','_',$request['title']);

            //insert brand article searchengin
            \App\Models\Brand::create([
                'title' => $request->title,
                'slug' => $request['slug'],
                'uniqueId' => $request->unique,
                'lang' => $request->lang,
                'data' => json_encode((object)['country'=>$request->country])
            ]);
            $brand = \App\Models\Brand::where('slug',$request->slug)->first();

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $brand->id,
                'type' => 'brand',
                'text' => $request->text
            ]);

            //add search engin options
            SearchEngin::create([
                'pid' => $brand->id,
                'type' => 'brand',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);
        }

        // file
        if ($request->file) {
            //remove old file
            $f = \App\Models\File::where('pid',$brand->id)->where('type','brand')->get();
            if ($f){
                foreach ($f as $i){
                    \App\Models\File::find($i->id)->delete();
                    File::fileDelete($i->url);
                }
            }
            Admin\File::serverSide([
                'file' => $request->file,
                'slug' => $brand->slug,
                'pid' => $brand->id,
                'type' => 'brand'
            ]);
        }
        return json_encode((object)['status'=>true,'msg'=>'success']);

    }

    public function delete(Request $request) {
        $del = \App\Models\Brand::where('uniqueId',$request->unique)->get();

        foreach ($del as $item) {
            \App\Models\Brand::find($item->id)->update([
                'status' => $item->status?0:1
            ]);
        }

        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function extend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = \App\Models\Brand::select('uniqueId')->where('id',$request->id)->first();

        $lang = \App\Models\Brand::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        $img = \App\Models\File::select('url')->where('pid',$request->id)->where('type','brand')->first();
        return json_encode((object)['img'=>$img,'lang'=>$lang]);
    }

    public function deleteLang(Request $request) {
        if ($request->id) {
            $b=\App\Models\Brand::find($request->id);
            $b->delete(); //returns true/false
            $s=SearchEngin::where('pid',$request->id)->where('type','brand');
            $s->delete();
            $a=Article::where('pid',$request->id)->where('type','brand');
            $a->delete();
            $f=\App\Models\File::where('pid',$request->id)->where('type','brand')->get();
            //del imge in upload folder
            if ($f){
                foreach ($f as $i){
                    \App\Models\File::find($i->id)->delete();
                    File::fileDelete($i->url);
                }
            }
            //del imge in upload folder
        }
        return json_encode((object)['status'=>true,'msg'=>'success']);

    }

}
