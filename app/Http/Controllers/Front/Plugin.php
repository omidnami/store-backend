<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Deraft;
use App\Models\File;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\Slider;
use Illuminate\Http\Request;

class Plugin extends Controller
{
    public function slider(Request $request) {
        $slider = Slider::find($request->id);
        $slide = Slider::where('parent', $slider->uniqueId)->get();
        $res = [];
        foreach ($slide as $item){
            $file = File::where('type', 'slider')->where('pid', $item->id)->first();
            if ($file){
                $item->img = $file->url;
            }else{
                $item->img = '';
            }
            $res[] = $item;
        }
        return $res;
    }

    public function text(Request $request) {
        $res = Deraft::where('id', $request->id)->first();
        $art = Article::where('pid',$request->id)->where('type','draft')->first();
        if ($art) {
            $res->text = $art->text;
        }else {
            $res->text = '';
        }


        return $res;
    }

    public function service(Request $request) {
        $service = Service::limit(4)->get();

        $res = [];
        foreach ($service as $item){
            $file = File::where('type', 'service')->where('pid', $item->id)->first();
            if ($file){
                $item->img = $file->url;
            }else{
                $item->img = '';
            }
            $res[] = $item;
        }
        return $res;
    }

    public function paralax(Request $request) {
        $service = Service::limit(4)->get();

        $res = [];
        foreach ($service as $item){
            $file = File::where('type', 'service')->where('pid', $item->id)->first();
            if ($file){
                $item->img = $file->url;
            }else{
                $item->img = '';
            }
            $res[] = $item;
        }
        return $res;
    }

    public function product(Request $request) {
        $pro = Product::limit(6)->orderBy('id','DESC')->get();

        $res = [];
        foreach ($pro as $item){
            $ex = $this->exProduct($item->id);
            $ex = json_decode($ex);
            //error_log($ex);
            $item->cat = $ex->cat?$ex->cat->title:'';
            $item->brand = $ex->brand?$ex->brand->title:'';
            $item->price = $ex->price??0;
            $item->vahed = $ex->vahed??'';

            if ($ex->img){
                $item->img = $ex->img->url;
            }else{
                $item->img = '';
            }
            $res[] = $item;
        }
        return $res;
    }

    public function project(Request $request) {
        $project  = Project::limit(8)->orderBy('id','DESC')->get();
        $res = [];
        foreach ($project as $item) {
            $file = File::where('type', 'project')->where('pid', $item->id)->first();
            if ($file){
                $item->img = $file->url;
            }else{
                $item->img = '';
            }
            $res[] = $item;
        }
        return $res;
    }

    public function blog(Request $request) {
        $blog  = Project::limit(8)->get();
        $res = [];
        foreach ($blog as $item) {
            $file = File::where('type', 'project')->where('pid', $item->id)->first();
            if ($file){
                $item->img = $file->url;
            }else{
                $item->img = '';
            }
            $res[] = $item;
        }
        return $res;
    }
}
