<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Page;
use App\Models\SearchEngin;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PageController extends Controller
{
    //

    public function select(Request $request){
        if (isset($request->unique)) {
            error_log($request->unique);
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
            $blog = Page::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$blog){
                return null;
            }
            $setting = Setting::first();
            if ($setting->home === $request->unique){
                $blog->home = 1;
            }else{
                $blog->home = 0;
            }
            $seo = SearchEngin::where('pid', $blog->id)->where('type', 'page')->first();
            $art = Article::where('pid', $blog->id)->where('type', 'page')->first();
            unset($seo->id);
            unset($seo->cat);
            unset($art->id);


            $res = array_merge((array)json_decode($blog),(array)json_decode($seo),(array)json_decode($art));
            return (object)$res;
        }

        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = Page::where('status', 1)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function insert(Request $request){
        $request['slug'] = str_replace(' ','_',$request['link']);
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request->uniqueId = uniqid();


        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255','unique:pages'],
        ]);


        error_log(json_decode($request->options)->home);
        if (json_decode($request->options)->home){
            Setting::find(1)->update([
                'home' => $request->uniqueId
            ]);
        }

        try {
            Page::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'data' => json_encode($request->data),
                'options' => $request->options,
                'uniqueId' => $request->uniqueId,
                'user' => 1,
                'home' => (boolean)json_decode($request->options)->home,
                'javascript' => json_encode($request->javascript),
                'css' => json_encode($request->css),
                'meta' => json_encode($request->meta)
            ]);

            $page = Page::where('slug',$request->slug)->first();

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $page->id,
                'type' => 'page',
                'text' => $request->html
            ]);

            //add search engin options
            SearchEngin::create([
                'pid' => $page->id,
                'type' => 'page',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)['status'=>true,'msg'=>'page_inserted']);
    }

    public function update(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request['slug'] = str_replace(' ','_',$request['link']);

        $blog = Page::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();


        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);



            if (json_decode($request->options)->home){
                Setting::find(1)->update([
                    'home' => $request->unique
                ]);
        }

        if ($blog) {
            if ($request['slug'] !== $blog->slug) {
                $request->validate([
                    'slug' => ['required', 'string', 'max:255','unique:pages'],
                ]);
            }
            error_log('update'.$blog->id);

            //update blog article serchengin
            Page::find($blog->id)->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'data' => json_encode($request->data),
                'options' => $request->options,
                'home' => (boolean)json_decode($request->options)->home,
                'javascript' => json_encode($request->javascript),
                'css' => json_encode($request->css),
                'meta' => json_encode($request->meta)
            ]);

            Article::where('pid',$blog->id)->where('type','page')->update([
                'text' => $request->html
            ]);
            SearchEngin::where('pid',$blog->id)->where('type','page')->update([
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);

        }

        else {
            error_log('insert');

            $request['slug'] = str_replace(' ','_',$request['link']);
            $request->validate([
                'slug' => ['required', 'string', 'max:255','unique:pages'],
            ]);
            //insert blog article searchengin
            Page::create([
                'lang' => $request->lang,
                'title' => $request->title,
                'slug' => $request->slug,
                'data' => json_encode($request->data),
                'options' => $request->options,
                'uniqueId' => $request->unique,
                'user' => 1,
                'home' => (boolean)json_decode($request->options)->home,
                'javascript' => json_encode($request->javascript),
                'css' => json_encode($request->css),
                'meta' => json_encode($request->meta)
            ]);
            $blog = page::where('slug',$request->slug)->first();

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $blog->id,
                'type' => 'page',
                'text' => $request->html
            ]);

            //add search engin options
            SearchEngin::create([
                'pid' => $blog->id,
                'type' => 'page',
                'meta_key' => $request['meta_key'] ?? null,
                'meta_description' => $request['desc'] ?? null,
                'canonical' => $request['canonical'] ?? null
            ]);
        }


        return json_encode((object)['status'=>true,'msg'=>'page_inserted']);

    }

    public function delete(Request $request){}
    public function deleteLang(Request $request){}

    public function extend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = Page::select('uniqueId')->where('id',$request->id)->first();

        $lang = Page::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        return json_encode((object)['lang'=>$lang]);
    }
}
