<?php

namespace App\Http\Controllers\Admin\Plugins;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Admin\File;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class SlidersController extends Controller
{
    //

    public function select(Request $request){
        if (isset($request->unique)) {
            error_log($request->unique);
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
            $slider = Slider::where('parent', $request->unique)->where('lang', $request->lang)->first();
            if (!$slider){
                return null;
            }
            $file = \App\Models\File::where('pid', $slider->id)->where('type', 'slider')->get();


            $res = (object)['status'=>true, 'data'=>$slider, 'files' => $file, 'msg' => 'selected'];
            return (object)$res;
        }

        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = Slider::where('status', 1)->where('parent', $request->parent)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function detail(Request $request){
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $slide = Slider::select('id','title')->where('parent', 0)->where('status', true)->where('lang', $request->lang)->get();
        return (object)['status'=>true,'plugin'=>$slide,'msg'=>'plugin_selected'];
    }

    public function insert(Request $request){
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->defaultLang;
        $request->uniqueId = uniqid();
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file' => 'mimes:jpeg,jpg,png,gif,svg,webp|max:2048'
        ]);

        $slid = Slider::where('parent', $request->unique)->where('lang',$request->lang)->first();
        if ($slid){
            //update
            try {
                Slider::find($slid->id)->update([
                    'title' => $request->title,
                    'sub_title' => $request->sub_title??null,
                    'link' => $request->link??null,
                    'linkText' => $request->linkText??null,
                    'target' => $request->target??'blank',
                    'parent' => $request->unique??0,
                    'bg' => $request->bg,
                    'dynamictext' => $request->dynamictext
                ]);
                $unique = $slid->uniqueId;
            }catch (Exception $e){

                return ResponseHelper::error($e->getMessage(), false);
            }
        }else{
            //insert
            $unique = uniqid();

            try {
                Slider::create([
                    'lang' => $request->lang,
                    'title' => $request->title,
                    'uniqueId' => $unique,
                    'user' => 1,
                    'sub_title' => $request->sub_title??null,
                    'link' => $request->link??null,
                    'linkText' => $request->linkText??null,
                    'target' => $request->target??'blank',
                    'parent' => $request->unique??0,
                    'bg' => $request->bg,
                    'dynamictext' => json_encode($request->dynamictext)
                ]);
            }catch (Exception $e){

                return ResponseHelper::error($e->getMessage(), false);
            }
        }


            $slider = Slider::where('uniqueId',$unique)->where('lang', $request->lang)->first();

            //if isset request file => set file(s) to addGallery

            if ($request->file) {
                //delete last file
                File::serverSide([
                    'file' => $request->file,
                    'slug' => $request->title,
                    'pid' => $slider->id,
                    'type' => 'slider',
                    'def' => true
                ]);
            }

            if ($request->mobile) {
                //delete last file

                File::serverSide([
                    'file' => $request->mobile,
                    'slug' => $request->title,
                    'pid' => $slider->id,
                    'type' => 'slider'
                ]);
            }




        return json_encode((object)['status'=>true,'msg'=>'slider_inserted']);
    }


    public function extend(Request $request) {
        if (!isset($request->id))
            return false;
        $cat = \App\Models\Slider::select('uniqueId')->where('id',$request->id)->first();
        $lang = \App\Models\Slider::select('lang')->where('uniqueId',$cat->uniqueId)->get();
        $img = \App\Models\File::select('url')->where('pid',$request->id)->where('type','article_cat')->first();
        return json_encode((object)['img'=>$img,'lang'=>$lang]);
    }

}
