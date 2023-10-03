<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\SearchEngin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class ArticleCat extends Controller
{
    //
    public function select($status=1,Request $request){

        if (isset($request->unique)) {
            error_log($request->unique);
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
            $cat = \App\Models\ArticleCat::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$cat){
                return null;
            }
            $seo = SearchEngin::where('pid', $cat->id)->where('type', 'article_cat')->first();
            $file = \App\Models\File::where('pid', $cat->id)->where('type', 'article_cat')->first();
            unset($seo->id);
            unset($file->id);
            unset($file->title);

            $res = array_merge((array)json_decode($cat),(array)json_decode($seo),(array)json_decode($file));
            return json_encode((object)$res);
        }
        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = \App\Models\ArticleCat::where('status', $status)->where('cid', $request->cid)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function selectByDetail(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $parent = \App\Models\ArticleCat::where('status',true)->where('cid', 0)->where('lang', $request->lang)->get();
        $res = [];
        foreach ($parent as $list) {
            $child = \App\Models\ArticleCat::where('status',true)->where('cid', $list->uniqueId)->where('lang', $request->lang)->get();
            $list->child = $child;
            $res[] = $list;
        }

        return (object)['status' => true, 'msg' => 'cat_selected', 'data' => $res];
    }

    public function trash(Request $request) {
        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $res = \App\Models\ArticleCat::where('status', 0)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function insert(Request $request) {
        $request['slug'] = str_replace(' ','_',$request['title']);
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request->uniqueId = uniqid();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255','unique:article_cat'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);

        try {
            \App\Models\ArticleCat::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'cid' => $request->cid,
                'uniqueId' => uniqid(),
                'uid' => 1
            ]);

            $cat = \App\Models\ArticleCat::where('slug',$request->slug)->first();

            //if isset request file => set file(s) to addGallery

            if ($request->file) {
                error_log($request->file);
                    File::serverSide([
                    'file' => $request->file,
                    'slug' => $request->slug,
                    'pid' => $cat->id,
                    'type' => 'article_cat'
                ]);
            }

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $cat->id,
                'type' => 'article_cat',
                'text' => $request->text
            ]);

            //add search engin options
            SearchEngin::create([
                'pid' => $cat->id,
                'type' => 'article_cat',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['meta_description'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)['status'=>true,'msg'=>'cat_inserted']);
    }


    public function update(Request $request)
    {
        $request->lang = $request->header('Lang') ?? \App\Models\Setting::first()->lang;

        $cat = \App\Models\ArticleCat::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);

        if ($cat) {
            \App\Models\ArticleCat::where('id', $cat->id)
                ->update([
                    'title' => $request->title,
                ]);
            if ($request->file) {
                //first delete file as public/uploads/cat_product and database
                File::serverSide([
                    'file' => $request->file,
                    'slug' => $request->slug,
                    'pid' => $cat->id,
                    'type' => 'article_cat'
                ]);
            }

            SearchEngin::where('type', 'article_cat')->where('pid', $cat->id)->update([
                'meta_key' => $request['meta_key'],
                'meta_description' => $request['meta_description'],
                'canonical' => $request['canonical']
            ]);
        } else {

            $request['slug'] = str_replace(' ', '_', $request['title']);
            $request->cid = 0;
            $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'slug' => ['required', 'string', 'max:255', 'unique:article_cat'],
                'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
            ]);
            \App\Models\ArticleCat::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'cid' => $request->cid,
                'uniqueId' => $request->unique,
                'uid' => 1
            ]);
            $cat = \App\Models\ArticleCat::where('slug', $request->slug)->first();

            if ($request->file) {
                error_log($request->file);
                \App\Models\ArticleCat::serverSide([
                    'file' => $request->file,
                    'slug' => $request->slug,
                    'pid' => $cat->id,
                    'type' => 'article_cat'
                ]);
            }
            SearchEngin::create([
                'pid' => $cat->id,
                'type' => 'article_cat',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['meta_description'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);
        }


        return json_encode((object)['status' => true, 'msg' => 'cat_updated']);
    }

    public function delete(Request $request) {
        $del = \App\Models\ArticleCat::where('uniqueId',$request->unique)->get();

        foreach ($del as $item) {
            \App\Models\ArticleCat::find($item->id)->update([
                'status' => !$item->status
            ]);
        }

        return json_encode((object)['status'=>true,'msg'=>'cat_deleted']);
    }

    public function CatExtend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = \App\Models\ArticleCat::select('uniqueId')->where('id',$request->id)->first();
        $cid = \App\Models\ArticleCat::select('id')->where('cid',$cat->uniqueId)->get()->count();
        $lang = \App\Models\ArticleCat::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        $img = \App\Models\File::select('url')->where('pid',$request->id)->where('type','article_cat')->first();
        return json_encode((object)['img'=>$img,'lang'=>$lang,'cid'=>$cid]);
    }

    public function deleteLang(Request $request) {
        if ($request->id) {
            $b=\App\Models\ArticleCat::find($request->id);
            $b->delete(); //returns true/false
            $s=SearchEngin::where('pid',$request->id)->where('type','article_cat');
            $s->delete();
            $a=Article::where('pid',$request->id)->where('type','article_cat');
            $a->delete();
            $f=\App\Models\File::where('pid',$request->id)->where('type','article_cat')->get();
            //del imge in upload folder
            if ($f){
                foreach ($f as $i){
                    \App\Models\File::find($i->id)->delete();
                    File::fileDelete($i->url);
                }
            }
            //del imge in upload folder
        }
        return json_encode((object)['status'=>true,'msg'=>'cat_deleted']);

    }

}
