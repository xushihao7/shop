<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class CheckApply
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public  function  handle($request, Closure $next)
    {
        $uid = $request->input("uid");
        $a_key="h:app_key:".$uid;
        //echo $uid;die;
        $app_key=Redis::hgetAll($a_key);
        foreach($app_key as $v){
            $app_keys=$v;
        }
        $s_key = "s:str" . $app_keys;
        //echo $redis_key;echo "<br/>";
        $num = Redis::incr($s_key);
        //echo $num;die;
        if ($num > 5) {
            $response = [
                'error' => 4003,
                'msg' => 'many request'
            ];
            echo   json_encode($response);exit;
        }
        return $next($request);
    }
}