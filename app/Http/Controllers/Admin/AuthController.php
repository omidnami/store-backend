<?php

namespace App\Http\Controllers\Admin;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth:api', ['except' => ['login','register','checkAdmin']]);
    }

    public function login(Request $request)
    {
        $isEmail = ResponseHelper::is_Email($request['userName']);
//        $request->password = Hash::make($request->password);
        if ($isEmail){
            $request['email'] = $request['userName'];
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('email', 'password');

        }else {
            $request['phone'] = $request['userName'];
            $request->validate([
                'phone' => 'required',
                'password' => 'required|string',
            ]);
            $credentials = $request->only('phone', 'password');

        }

        unset($request['userName']);

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => false,
                'msg' => 'note_valide',
            ], 401);
        }


        $user = Auth::user();
        if ($user->rol !== 1){
            return response()->json([
                'status' => false,
                'msg' => 'note_access',
            ], 403);
        }
        $token_api = time().$_SERVER['REMOTE_ADDR'].$user->id.rand(100000,9999999);
        $token_api = Hash::make((string)$token_api);
        User::find($user->id)->update([
            'token' => $token_api
        ]);
        error_log($user->id);

        return response()->json([
            'status' => true,
            'user' => $user,
            'authorisation' => [
                'token' => $token_api,
                'type' => 'bearer',
            ],
            'msg' => 'success'
        ]);

    }

    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        User::find('token', $request->token)->update([
            'token' => ''
        ]);

        if (Auth::check()){
            Auth::logout();
        }

        return response()->json([
            'status' => false,
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function checkAdmin(Request $request) {
        $isEmail = ResponseHelper::is_Email($request['userName']);

        if ($isEmail){
            $request['email'] = $request['userName'];
        }else {
            $request['phone'] = $request['userName'];
        }
        unset($request['userName']);
        $res = User::where($request->all())->first();
        if ($res and $res->rol == 1){
            error_log($res->rol);

            return json_encode((object)['status'=>true, 'msg'=>'is admin']);
        }
        return json_encode((object)['status'=>false, 'msg'=>'user note access']);

    }

    public function check(Request $request) {
        error_log($request->token);
        $user = User::where('token', $request->token)->where('rol', 1)->first();
        if ($user){
            return json_encode((object)['status'=>true,'user'=>$user,'msg'=>'hase_Login']);
        }
        return json_encode((object)['status'=>false,'user'=>null,'msg'=>'dont_access']);
    }

}
