<?php

namespace App\Http\Controllers;


use App\Helpers\ResponseHelper;
use App\Models\ProductDynamic;
use Illuminate\Http\Request;

class ProductDynamicController extends Controller
{
    public function select($id = 0) {
        if ($id) {
            ProductDynamic::find($id);
        }
        ProductDynamic::all();
    }

    public function insert(Request $request) {
        ProductDynamic::create($request->all());
        return ResponseHelper::success(__('response.item_added'));
    }

    public function update(Request $request) {
        if (!isset($request['id'])){
            return ResponseHelper::error(__('response.id_ex',false));
        }

        ProductDynamic::where('id',$request->id)
            ->update($request->all());

        return ResponseHelper::success(__('response.item_updated'));
    }

    public function delete(Request $request) {
        $del = $this->select($request->id);
        $del->delete();
        return ResponseHelper::success(__('response.item_deleted'));
    }
}
