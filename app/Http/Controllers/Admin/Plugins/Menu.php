<?php

namespace App\Http\Controllers\Admin\Plugins;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class Menu extends Controller
{
    //

    public function select(Request $request){
        if (isset($request->unique)) {
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
            $menu = \App\Models\Menu::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$menu){
                return null;
            }

            return $menu;
        }

        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = \App\Models\Menu::where('status', 1)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function insert(Request $request){
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request->uniqueId = uniqid();
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);


        try {
            \App\Models\Menu::create([
                'title' => $request->title,
                'position' => $request->position,
                'link' => $request->link??null,
                'lang' => $request->lang,
                'uniqueId' => $request->uniqueId,
                'user' => 1
            ]);
        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)['status'=>true,'msg'=>'menu_inserted']);
    }

    public function update(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;

        $menu = \App\Models\Menu::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        if ($menu) {
            error_log('update');

            //update brand article serchengin
            \App\Models\Menu::find($menu->id)->update([
                'title' => $request->title,
                'position' => $request->position??null,
            ]);
        }

        else {
            error_log('insert');


            //insert brand article searchengin
            \App\Models\Menu::create([
                'title' => $request->title,
                'position' => $request->position,
                'link' => $request->link??null,
                'lang' => $request->lang,
                'uniqueId' => $request->unique,
                'user' => 1
            ]);

        }

        return json_encode((object)['status'=>true,'msg'=>'menu_updated']);

    }
    public function extend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = \App\Models\Menu::select('uniqueId')->where('id',$request->id)->first();
        $lang = \App\Models\Menu::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        return json_encode((object)['lang'=>$lang]);
    }

}
