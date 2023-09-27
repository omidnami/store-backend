<?php

namespace App\Http\Controllers\Admin;


use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ProductDynamic;
use Exception;
use Illuminate\Http\Request;

class ProductDynamicController extends Controller
{
    public function select(Request $request) {
        if ($request->pid) {
            error_log($request->pid);
            return json_encode((object)[
                'status'=>true,
                'msg'=>'success',
                'data'=> ProductDynamic::where('pid', $request->pid)->get()
            ]);
        }
        return true;
    }

    public function insert(Request $request) {
        $request->lang =$request->header('Lang')??\App\Models\Setting::first()->lang;
        $request->prc = json_decode($request->price)->prc;
        $request->transDay = json_decode($request->data)->tranDay;
        error_log($request->status);
        $request->validate([
            'type' => ['required', 'string'],
            'value' => ['required', 'max:255'],
//            'transDay' => ['required', 'max:255'],
//            'prc' => ['required', 'max:255', 'int'],
        ]);

        $date = json_decode($request->price)->date;
        $date = time()+((int)$date*86400);
        $request->price = json_decode($request->price);
        $request->price->date = $date;
        $request->price = json_encode($request->price);

        try {

            ProductDynamic::create([
                'type' => $request->type,
                'value' => $request->value,
                'status' => (boolean)$request->status,
                'depo' => $request->depo,
                'price' => $request->price,
                'img' => $request->img,
                'pid' => $request->pid,
                'data' => $request->data,
            ]);

        }catch (Exception $e){
            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)[
            'status'=>true,
            'msg'=>'success',
            'data'=> ProductDynamic::where('pid', $request->pid)->get()
        ]);
    }

    public function update(Request $request) {
        $request->validate([
            'type' => ['required', 'string'],
            'value' => ['required', 'max:255'],
//            'transDay' => ['required', 'max:255'],
//            'prc' => ['required', 'max:255', 'int'],
        ]);

        $date = json_decode($request->price)->date;
        $date = time()+((int)$date*86400);
        $request->price = json_decode($request->price);
        $request->price->date = $date;
        $request->price = json_encode($request->price);

        try {

            ProductDynamic::find($request->id)->update([
                'value' => $request->value,
                'status' => (boolean)$request->status,
                'depo' => $request->depo,
                'price' => $request->price,
                'img' => $request->img,
                'data' => $request->data,
            ]);

        }catch (Exception $e){
            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)[
            'status'=>true,
            'msg'=>'success',
            'data'=> ProductDynamic::where('pid', $request->pid)->get()
        ]);
    }

    public function delete(Request $request) {
        ProductDynamic::find($request->id)
        ->delete();
        return json_encode((object)[
            'status'=>true,
            'msg'=>'success',
            'data'=> ProductDynamic::where('pid', $request->pid)->get()
        ]);
    }


}
