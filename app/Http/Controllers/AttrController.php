<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Product;
use App\Models\ProductAttrType;
use Illuminate\Http\Request;
use Exception;

class AttrController extends Controller
{

    public function select($id = 0) {
        if ($id) {
            return ProductAttrType::find($id);
        }
        return ProductAttrType::all();
    }
    public function select_cat($cid = 0) {
        if ($cid == 0){
            return ResponseHelper::error('not set category id', false);
        }
        return ProductAttrType::where('cid', $cid)->get();
    }

    public function insert(Request $request) {
        try {
            ProductAttrType::create($request->all());
        }catch (Exception $e){

            return ResponseHelper::error($e->getMessage(), false);
        }
        return ResponseHelper::success(__('response.attr_added'));
    }

    public function update(Request $request) {
        if (!isset($request['id'])){
            return ResponseHelper::error(__('response.id_ex',false));
        }

        ProductAttrType::where('id',$request->id)
            ->update($request->all());

        return ResponseHelper::success(__('response.attr_updated'));
    }

    public function delete(Request $request) {
        $del = $this->select($request->id);
        $del->delete();
        return ResponseHelper::success(__('response.attr_deleted'));
    }
}
