<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Product;
use App\Models\ProductAttrType;
use App\Models\ProductCat;
use App\Models\SearchEngin;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Pagination\Paginator;

class AttrController extends Controller
{

    public function select($id = 1, Request $request){
        if (isset($request->unique)) {
            error_log($request->unique);
            // category image and video gallery or filses -> files_table
            // category description -> article_table
            // category seo -> searchEngin_table
            $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
            $attr = ProductAttrType::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();
            if (!$attr){
                return null;
            }

            return json_encode((object)$attr);
        }
        //paginate
        //"total":20
        //"per_page":15,
        //"to":15,
        //"current_page":1,
        // data: {}
        $currentPage = $request->page; // You can set this to any page you want to paginate to
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $res = ProductAttrType::where('gp',$request->gp)->where('status',$id)->groupBy('uniqueId')->orderBy('id','DESC')->paginate(10);
        //error_log($res);
        return collect($res);
    }

    public function select_cat($cid = 0) {
        if ($cid == 0){
            return ResponseHelper::error('not set category id', false);
        }
        return ProductAttrType::where('cid', $cid)->get();
    }

    public function selectGpByCat(Request $request) {
        error_log($request->cid);
        $cid = [
            'c1' => json_decode($request->cid)->cat1,
            'c2' => json_decode($request->cid)->cat2===''?null:json_decode($request->cid)->cat2,
            'c3' => $request->cid->cat3??null,
            'c4' => $request->cid->cat4??null,
            'c5' => null,
        ];
        $all = [
            'c1' => null,
            'c2' => null,
            'c3' => null,
            'c4' => null,
            'c5' => null,
        ];
        error_log(json_encode((object)$cid));
        $request->cid = json_encode((object)$cid);
        $request->all = json_encode((object)$all);
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $res = ProductAttrType::whereIn('cid' , [$request->cid,$request->all])->where('lang',$request->lang)->where('status',1)->get();
        $result = [];
        foreach ($res as $item) {
            $child = ProductAttrType::where('gp',$item->uniqueId)->where('lang',$request->lang)->where('status',1)->get();
            if ($child->count()){
                $item['child'] = $child;
                array_push($result, $item);
            }
        }
        return $result;
    }

    public function insert(Request $request) {
        $request->uniqueId = uniqid();
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            ]);

        try {
            error_log($request->gp);
            ProductAttrType::create([
                'title' => $request->title,
                'link' => isset($request->link)?$request->link??null:null,
                'lang' => $request->lang,
                'gp' => $request->gp,
                'type' => isset($request->type)?$request->type:null,
                'dataType' => isset($request->dataType)?$request->dataType:null,
                'data' => isset($request->data)?$request->data:null,
                'uniqueId' => $request->uniqueId,
                'cid' => json_encode($request->cid)

            ]);
        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }
        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function update(Request $request) {
        error_log('u');
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $attr = ProductAttrType::where('uniqueId', $request->unique)->where('lang', $request->lang)->first();

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        if ($attr){
            error_log('up');
            ProductAttrType::where('id',$attr->id)
                ->update([
                    'title' => $request->title,
                    'link' => $request->link,
                    'type' => isset($request->type)?$request->type:null,
                    'data' => isset($request->data)?$request->data:null,
                    'dataType' => isset($request->dataType)?$request->dataType:null,
                    'cid' => json_encode($request->cid)
                ]);
        }else {
            error_log('in');
            ProductAttrType::create([
                'title' => $request->title,
                'link' => isset($request->link)?$request->link??null:null,
                'lang' => $request->lang,
                'gp' => $request->gp,
                'type' => isset($request->type)?$request->type:null,
                'data' => isset($request->data)?$request->data:null,
                'dataType' => isset($request->dataType)?$request->dataType:null,
                'uniqueId' => $request->unique,
                'cid' => json_encode($request->cid)
            ]);
        }
        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function delete(Request $request) {
        $del = ProductAttrType::where('uniqueId',$request->unique)->get();
        error_log($del);

        foreach ($del as $item) {
            error_log($item->id);
            ProductAttrType::find($item->id)->update([
                'status' => $item->status?0:1
            ]);
        }

        return json_encode((object)['status'=>true,'msg'=>'success']);
    }

    public function CatExtend(Request $request) {
        if (!isset($request->id))
            return false;

        $attr = ProductAttrType::where('id',$request->id)->first();
        $cid = ProductAttrType::select('id')->where('cid',$request->id)->get()->count();
        $lang = ProductAttrType::select('lang')->where('uniqueId',$attr->uniqueId)->get();
        $cat = (bool)json_decode($attr->cid)->c1;
        return json_encode((object)['lang'=>$lang,'cid'=>$cid,'cat'=>$cat]);
    }

    public function attrExtend(Request $request) {
        if (!isset($request->id))
            return false;

        $attr = ProductAttrType::where('id',$request->id)->first();
        $lang = ProductAttrType::select('lang')->where('uniqueId',$attr->uniqueId)->get();
        return json_encode((object)['lang'=>$lang]);
    }

    public function deleteLang(Request $request) {
        if ($request->id) {
            $b=ProductAttrType::find($request->id);
            $b->delete(); //returns true/false
            //del childe
        }
        return json_encode((object)['status'=>true,'msg'=>'success']);

    }

}
