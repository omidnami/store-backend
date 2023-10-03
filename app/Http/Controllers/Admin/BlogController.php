<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Blog;
use App\Models\SearchEngin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class BlogController extends Controller
{
    //

    public function select(Request $request){
        if (isset($request->unique)) {
            error_log($request->unique);
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
            $blog = Blog::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$blog){
                return null;
            }
            $seo = SearchEngin::where('pid', $blog->id)->where('type', 'blog')->first();
            $file = \App\Models\File::where('pid', $blog->id)->where('type', 'blog')->first();
            $art = Article::where('pid', $blog->id)->where('type', 'blog')->first();
            unset($seo->id);
            unset($seo->cat);
            unset($art->id);
            if ($file) {
                unset($file->title);
                unset($file->id);
            }

            $res = array_merge((array)json_decode($blog),(array)json_decode($seo),(array)json_decode($file),(array)json_decode($art));
            return (object)$res;
        }

        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = Blog::where('status', 1)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function insert(Request $request){
        $request['slug'] = str_replace(' ','_',$request['title']);
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request->uniqueId = uniqid();
        error_log($request->cat[count($request->cat) - 1]);
            $request['mainCat'] = '';
        if (count($request->cat)){
            $request['mainCat'] = $request->cat[count($request->cat) - 1];
        }
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'mainCat' => ['required', 'max:255'],
            'slug' => ['required', 'string', 'max:255','unique:blogs'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);
        error_log($request->mainCat);

        try {
            Blog::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'cat' => json_encode($request->cat),
                'mainCat' => $request->mainCat,
                'uniqueId' => uniqid(),
                'user' => 1
            ]);

            $cat = Blog::where('slug',$request->slug)->first();

            //if isset request file => set file(s) to addGallery

            if ($request->file) {
                error_log($request->file);
                File::serverSide([
                    'file' => $request->file,
                    'slug' => $request->slug,
                    'pid' => $cat->id,
                    'type' => 'blog'
                ]);
            }

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $cat->id,
                'type' => 'blog',
                'text' => $request->text
            ]);

            //add search engin options
            SearchEngin::create([
                'pid' => $cat->id,
                'type' => 'blog',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)['status'=>true,'msg'=>'blog_inserted']);
    }

    public function update(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;

        $blog = Blog::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request['mainCat'] = '';
        if (count($request->cat)){
            $request['mainCat'] = $request->cat[count($request->cat) - 1];
        }
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'mainCat' => ['required', 'max:255'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);

        if ($blog) {
            error_log('update'.$blog->id);

            //update blog article serchengin
            Blog::find($blog->id)->update([
                'title' => $request->title,
                'cat' => json_encode($request->cat),
                'mainCat' => $request->mainCat,
            ]);

            Article::where('pid',$blog->id)->where('type','blog')->update([
                'text' => $request->text
            ]);
            SearchEngin::where('pid',$blog->id)->where('type','blog')->update([
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }

        else {
            error_log('insert');

            $request['slug'] = str_replace(' ','_',$request['title']);
            $request->validate([
                'slug' => ['required', 'string', 'max:255','unique:blogs'],
            ]);
            //insert blog article searchengin
            Blog::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'cat' => json_encode($request->cat),
                'mainCat' => $request->mainCat,
                'uniqueId' => $request->unique,
                'user' => 1
            ]);
            $blog = Blog::where('slug',$request->slug)->first();

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $blog->id,
                'type' => 'blog',
                'text' => $request->text
            ]);

            //add search engin options
            SearchEngin::create([
                'pid' => $blog->id,
                'type' => 'blog',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);
        }

        // file
        if ($request->file) {
            //remove old file
            $f = \App\Models\File::where('pid',$blog->id)->where('type','blog')->get();
            if ($f){
                foreach ($f as $i){
                    \App\Models\File::find($i->id)->delete();
                    File::fileDelete($i->url);
                }
            }
            File::serverSide([
                'file' => $request->file,
                'slug' => $blog->slug,
                'pid' => $blog->id,
                'type' => 'blog'
            ]);
        }
        return json_encode((object)['status'=>true,'msg'=>'blog_inserted']);

    }

    public function delete(Request $request){}
    public function deleteLang(Request $request){}

    public function extend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = Blog::select('uniqueId')->where('id',$request->id)->first();

        $lang = Blog::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        $img = \App\Models\File::select('url')->where('pid',$request->id)->where('type','blog')->first();
        return json_encode((object)['img'=>$img,'lang'=>$lang]);
    }
}
