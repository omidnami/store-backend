<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function select($id = 0) {
        if ($id) {
            return Product::find($id);
        }
        return  Product::all();
    }

    public function insert(Request $request) {
        $request['sk'] = rand(10000,99999);
        $request['slug'] = str_replace(' ','_',$request['title']);
        try {
            Product::create($request->all());
        }catch (Exception $e){

           return ResponseHelper::error($e->getMessage(), false);
        }


        return ResponseHelper::success(__('response.product_inserted'));
    }

    public function update(Request $request) {
        if (!isset($request['id'])){
            return ResponseHelper::error(__('response.id_ex',false));
        }

        Product::where('id',$request->id)
            ->update($request->all());

        return ResponseHelper::success(__('response.product_updated'));
    }

    public function delete(Request $request) {
        $del = $this->select($request->id);
        $del->delete();
        return ResponseHelper::success(__('response.product_deleted'));
    }
}
