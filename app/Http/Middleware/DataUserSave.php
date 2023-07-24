<?php

namespace App\Http\Middleware;

use App\Models\View;
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
        //check Auth login

        //get userID

        //if Auh ? user_id = id : 0

        //get view table where created_at == data.now and ip == any client ip

        //if view == null ? set record : view ++

        $data = [
            'browser' => $request->header('Sec-Ch-Ua'),
            'os' => $request->header('Sec-Ch-Ua-Platform'),
            'ip' => $_SERVER["REMOTE_ADDR"],
            'session' => md5(time().$_SERVER["REMOTE_ADDR"]),
            'view' => 1
        ];
        //error_log();
        View::create($data);
        return $next($request);
    }
}
