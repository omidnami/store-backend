<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\DepoLocation;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class DepoController extends Controller
{
    //

    public function select(Request $request){
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

        $res = DepoLocation::where('did', $request->id)->orderBy('id','DESC')->paginate(10);
        return collect($res);
    }

    public function depoServisce(Request $request) {
        $depo = DepoLocation::find($request->id);
        $users = User::where('rol', 2)->get();
        return json_encode((object)[
            'status'=>true,
            'msg'=>'depo_services',
            'data' => json_encode((object)[
                'depo' => $depo,
                'users' => $users,
            ])
        ]);
    }

    public function insert(Request $request) {
        $request->user = 1;

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'maxQuty' => ['required']
        ]);

        try {
            DepoLocation::create([
                'title' => $request->title,
                'maxQuty' => $request->maxQuty,
                'address' => $request->address,
                'depoMan' => $request->depoMan,
                'user' => $request->user,
                'did' => 0
            ]);
            $depo = DepoLocation::where('title',$request->title)->first();
            DepoLocation::create([
                'user' => $request->user,
                'depo' => $request->depo,
                'row' => $request->row,
                'Shelf' => $request->shelf,
                'did' => $depo->id
            ]);

        }catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), false);
        }

        return json_encode((object)[
            'status' => true,
            'msg' => 'success',
        ]);
    }
}
