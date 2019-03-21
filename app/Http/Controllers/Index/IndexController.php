<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    //
    public  function  index(Request $request){
        $is_login=$request->get("is_login");
        //var_dump($is_login);die;
        $url="http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        $info=[
            'url'=>urlencode($url),
            'login'=>$is_login
        ];
        return view("welcome",$info);
    }
}
