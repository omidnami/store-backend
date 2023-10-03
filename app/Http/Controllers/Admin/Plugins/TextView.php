<?php

namespace App\Http\Controllers\Admin\Plugins;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Deraft;
use App\Models\Slider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class TextView extends Controller
{
    //
    public function select(Request $request){
        if (isset($request->unique)) {
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
            $draft = Deraft::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$draft){
                return null;
            }
            $art = Article::where('pid', $draft->id)->where('type', 'draft')->first();

            return (object)['status'=>true, 'data'=>$draft, 'art' => $art, 'msg' => 'selected'];
        }

        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = Deraft::where('status', 1)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }
    public function detail(Request $request){
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $slide = Deraft::select('id','title')->where('status', true)->where('lang', $request->lang)->get();
        return (object)['status'=>true,'plugin'=>$slide,'msg'=>'plugin_selected'];
    }
    public function insert(Request $request){
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request->uniqueId = uniqid();
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ]);


            //update
            try {
                Deraft::create([
                    'title' => $request->title,
                    'link' => $request->link??null,
                    'linkText' => $request->linkText??null,
                    'target' => $request->target??'blank',
                    'lang' => $request->lang,
                    'uniqueId' => $request->uniqueId,
                    'user' => 1
                ]);
            }catch (Exception $e){

                return ResponseHelper::error($e->getMessage(), false);
            }



        $draft = Deraft::where('uniqueId',$request->uniqueId)->where('lang', $request->lang)->first();

        //if isset request file => set file(s) to addGallery

        Article::create([
            'pid' => $draft->id,
            'type' => 'draft',
            'text' => $request->text
        ]);




        return json_encode((object)['status'=>true,'msg'=>'text_inserted']);
    }

    public function update(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;

        $draft = Deraft::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
        ]);

        if ($draft) {
            error_log('update');

            //update brand article serchengin
            Deraft::find($draft->id)->update([
                'title' => $request->title,
                'link' => $request->link??null,
                'linkText' => $request->linkText??null,
                'target' => $request->target??'blank',
            ]);

            Article::where('pid',$draft->id)->where('type','draft')->update([
                'text' => $request->text
            ]);


        }

        else {
            error_log('insert');


            //insert brand article searchengin
            Deraft::create([
                'title' => $request->title,
                'link' => $request->link??null,
                'linkText' => $request->linkText??null,
                'target' => $request->target??'blank',
                'lang' => $request->lang,
                'uniqueId' => $request->unique,
                'user' => 1
            ]);
            $draft = Deraft::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

            //if text !== null or '' or isset text request set text to article
            Article::create([
                'pid' => $draft->id,
                'type' => 'draft',
                'text' => $request->text
            ]);

        }

        return json_encode((object)['status'=>true,'msg'=>'success']);

    }

    public function extend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = Deraft::select('uniqueId')->where('id',$request->id)->first();
        $lang = Deraft::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        return json_encode((object)['lang'=>$lang]);
    }



}
