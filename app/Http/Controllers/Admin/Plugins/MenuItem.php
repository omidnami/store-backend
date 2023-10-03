<?php

namespace App\Http\Controllers\Admin\Plugins;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class MenuItem extends Controller
{
    //
    public function select(Request $request){
        if (isset($request->unique)) {
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
            $menu = \App\Models\MenuItem::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$menu){
                return null;
            }

            return $menu;
        }

        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = \App\Models\MenuItem::where('status', 1)
            ->where('menu', $request->menu)
            ->where('parent', $request->parent)
            ->groupBy('uniqueId')
            ->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function insert(Request $request){
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request->uniqueId = uniqid();
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);


        try {
            \App\Models\MenuItem::create([
                'title' => $request->title,
                'icone' => $request->icon??null,
                'link' => $request->link??null,
                'lang' => $request->lang,
                'uniqueId' => $request->uniqueId,
                'parent' => $request->parent,
                'menu' => $request->menu,
                'user' => 1
            ]);
        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)['status'=>true,'msg'=>'menu_inserted']);
    }

    public function update(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;

        $menu = \App\Models\MenuItem::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        if ($menu) {
            error_log('update');

            //update brand article serchengin
            \App\Models\MenuItem::find($menu->id)->update([
                'title' => $request->title,
                'icone' => $request->icon??null,
                'link' => $request->link??null,
            ]);
        }

        else {
            error_log('insert');


            //insert brand article searchengin
            \App\Models\MenuItem::create([
                'title' => $request->title,
                'icone' => $request->icon??null,
                'link' => $request->link??null,
                'lang' => $request->lang,
                'uniqueId' => $request->unique,
                'parent' => $request->parent,
                'menu' => $request->menu,
                'user' => 1
            ]);

        }

        return json_encode((object)['status'=>true,'msg'=>'menu_updated']);

    }
    public function extend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = \App\Models\MenuItem::select('uniqueId')->where('id',$request->id)->first();
        $lang = \App\Models\MenuItem::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        return json_encode((object)['lang'=>$lang]);
    }
}
