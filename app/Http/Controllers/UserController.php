<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function select($id = 0) {
        if ($id){
            return User::find($id);
        }
        return User::all();
    }

    public function insert(Request $request) {
        if (!isset($request['password']) or is_null($request['password'])){
            $request['password'] = rand(999,99999999).time();
        }
        User::create($request->all());
        return ResponseHelper::success('user added successfully');
    }

    public function search(Request $request) {
        return User::where('id',1)->get();
    }
}
