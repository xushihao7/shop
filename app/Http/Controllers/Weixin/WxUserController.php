<?php

namespace App\Http\Controllers\Weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
class WxUserController extends Controller

{
    protected  $redis_weixin_access_token='str:weixin_access_token';
    //获取accetoken
    public function  getAccesstoken(){
        $token=Redis::get($this->redis_weixin_access_token);
        if(!$token){
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data=json_decode(file_get_contents($url),true);
            //print_r($data);die;
            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);

        }
        return $token;

    }

    //获取用户信息
    public  function  userInfo(){
        $url='https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token='.$this->getAccesstoken();

    }










}
