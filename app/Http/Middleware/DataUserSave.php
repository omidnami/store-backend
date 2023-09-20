<?php

namespace App\Http\Middleware;

use App\Models\View;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class DataUserSave
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $Token = $request['_token_'];
        //check Auth login
        $user = \App\Models\User::where('token',$Token)->first();

        //if Auh ? user_id = id : 0
            if ($user){
                $user_id = $user->id;
            }else{
                $user_id = 0;
            }
        //get view table where created_at == data.now and ip == any client ip
        $view = View::where('ip',$_SERVER["REMOTE_ADDR"])->where('created_at', '>=', Carbon::today())->first();

        //if view == null ? set record : view ++
            if (!$view){
                $data = [
                    'browser' => $request->header('Sec-Ch-Ua'),
                    'os' => $request->header('Sec-Ch-Ua-Platform'),
                    'ip' => $_SERVER["REMOTE_ADDR"],
                    'session' => md5(time().$_SERVER["REMOTE_ADDR"]),
                    'view' => 1,
                    'user_id' => $user_id
                ];
                //error_log();
                View::create($data);
            }else {
                $data = [
                    'view' => $view->view+1
                ];
                View::find($view->id)->update($data);
            }

        return $next($request);
    }
}
