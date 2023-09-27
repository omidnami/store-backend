<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function select($id = 0,Request $request) {
        if ($id){

            $user = User::find($id);
            $userData = $user->userData;
            unset($userData->id);
            $res = array_merge((array)json_decode($user),(array)json_decode($userData));
            return json_encode((object)$res);
        }

        //paginate
        //"total":20
        //"per_page":15,
        //"to":15,
        //"current_page":1,
        // data: {}
        $currentPage = $request->page; // You can set this to any page you want to paginate to

        // Make sure that you call the static method currentPageResolver()
        // before querying users
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $u = User::where('rol','>',0)->paginate(10);
        return collect($u);
    }

    public function trashList(Request $request) {
        //paginate
        //"total":20
        //"per_page":15,
        //"to":15,
        //"current_page":1,
        // data: {}
        $currentPage = $request->page; // You can set this to any page you want to paginate to

        // Make sure that you call the static method currentPageResolver()
        // before querying users
        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });
        $u = User::where('rol',0)->paginate(
            $perPage = 10
        );
        return collect($u);
    }

    public function insert(Request $request) {
//        if (!isset($request['password']) or isEmpty($request['password'])){
//            $request['password'] = rand(999,99999999).time();
//        }
        if (!isset($request->userName) or is_null($request->userName)){
            $request->userName = $request->email;
        }
        if (!isset($request['phone']) or is_null($request['phone'])){
            $request['phone'] = '0980'.rand(100000000,9999999999);
        }
        //error_log(json_encode($request->all()));
        $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'userName' => ['required', 'max:255', 'unique:users'],
            'phone' => ['required', 'max:255', 'unique:users'],
            'password' => 'confirmed',
            'file' => 'mimes:jpeg,jpg,png,gif|max:2048'
        ]);


        $token = Hash::make(rand(100000,999999).time().$_SERVER['REMOTE_ADDR']);
        $user = [
            'userName' => $request->userName,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'phone' => Utils::npe($request->phone),
            'token' => $token,
            'password' => Hash::make($request->password)
        ];
        User::create($user);
        $u = User::where('token',$token)->first();

        $userData = [
            'address' => $request->address??'',
            'tel' => $request->tel??'',
            'posport' => $request->posport??'',
            'zip' => $request->zip,
            'father' => $request->father??'',
            'breat' => $request->breat??'',
            'meli' => $request->meli??'',
        ];

        UserData::create([
            'user_id' => $u->id,
            'person_data' => json_encode($userData)
        ]);

        if ($request->file) {
            error_log($request->file);
            File::serverSide([
                'file' => $request->file,
                'slug' => $request->fname.'-'.$request->lname,
                'pid' => $u->id,
                'type' => 'profile'
            ]);
        }
        return json_encode((object)['status'=>true, 'msg'=>'success']);
    }

    public function update(Request $request,$id) {

        $userSelect = User::find($id);

        if (!isset($request['phone']) or is_null($request['phone'])){
            $request['phone'] = '0980'.rand(100000000,9999999999);
        }

        //error_log(json_encode($request->all()));
        $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'password' => 'confirmed',
            'file' => ['mimes:jpeg,jpg,png,gif|max:2048']
        ]);


        if ($request->email !== $userSelect->email){
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
        }
        if ($request->phone !== $userSelect->phone){
            $request->validate([
                'phone' => ['required', 'max:255', 'unique:users'],
            ]);
        }

        $user = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'phone' => Utils::npe($request->phone),
            'password' => Hash::make($request->password)
        ];
        $userSelect->update($user);

        $userData = [
            'address' => $request->address??'',
            'tel' => $request->tel??'',
            'posport' => $request->posport??'',
            'zip' => $request->zip,
            'father' => $request->father??'',
            'breat' => $request->breat??'',
            'meli' => $request->meli??'',
        ];

        UserData::where('id', $id)->update([
            'person_data' => json_encode($userData)
        ]);

        if ($request->file) {
            error_log('entery file');
            File::serverSide([
                'file' => $request->file,
                'slug' => $request->fname.'-'.$request->lname,
                'pid' => $id,
                'type' => 'profile'
            ]);
        }
        return json_encode((object)['status'=>true, 'msg'=>'success']);
    }

    public function delete(Request $request){
            $userSelect = User::find($request->id);
           $res = $userSelect->update([
                'rol' => 0
           ]);
        return json_encode((object)['status'=>true, 'msg'=>'success']);


    }

    public function search(Request $request) {
        return User::where('id',1)->get();
    }
}
