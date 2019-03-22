<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class CheckPassport
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(isset($_COOKIE['uid']) && isset($_COOKIE['token'])){
            //验证token
            $key = 'h:token:'.$_COOKIE['uid'];
            $token = Redis::hGetAll($key);
            foreach($token as $k=>$v){
                $token=$v;
            }
            if($token == $_COOKIE['token']){
                $request->attributes->add(['is_login'=>1]);
            }else{
                $request->attributes->add(['is_login'=>0]);
            }
        }else{
            $request->attributes->add(['is_login'=>0]);
        };
        return $next($request);


    }
}
