<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
class CheckApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private  $_data_info=[];
    private  $_blank_list='blank_list';
    public function handle($request, Closure $next)
    {
        //对数据进行解密
        $this->_aecDecrypt($request);
        //接口防刷
        $data=$this->_apiAccess($request);
        if($data['error']!=0){
            return response($data);
        }
        //验签
        $data=$this->_checkSign($request);
        if($data['error']==0){
            $request->request->replace($this->_data_info);
            return $next($request);
        }else{
            return response($data);
        }


    }
    /**
     * 对称解密
     */
    private  function _aecDecrypt($request){
        $data=$request->input("data");
        if(!empty($data)) {
            $data_api = openssl_decrypt($data, "AES-128-CBC", "xushihao", false, "1234567890123456");
          $this->_data_info = json_decode($data_api, true);
        }else{
            return [
                'error' => 1,
                'mag'   => '解密失败'
            ];

        }

    }
    /**
     * 验签
     */
    private  function  _checkSign($request){
          $data=$request->input("sign");
         //var_dump($data);echo "<pre/>";
          if(!empty($data)){
              ksort($this->_data_info);
              $app_secret=$this->_getAppkey()['app_secret'];
              $str=http_build_query($this->_data_info);
              $str.="app_secret".$app_secret;
              $sign=md5($str);
              if($sign==$data){
                  return [
                      'error'=>0,
                      'msg'=>"验签成功"
                  ];
              }else{
                  return [
                      'error'=>2,
                      'msg'=>"验签失败"
                  ];
              }

          }else{
              return [
                  'error'=>3,
                  'msg'=>"数据传输错误"
              ];
          }
    }
    /**
     * 获取app_key和app_secret
     */
    private  function  _getAppkey(){
        return [
            'app_key'=>md5("xushihao123"),
            'app_secret'=>md5("xushihao456")
        ];
    }
    /**
     * 获取app_key
     */
    private function _getKey(){
        return $this->_data_info['app_key'];
    }
    /**
     * 接口防刷
     */
    private  function  _apiAccess(){
        $app_key=$this->_getKey();
        //var_dump($app_key);exit;
        $time=Redis::zScore($this->_blank_list,$app_key);
        if(!empty($time)){
            if(time()-$time>60){
                Redis::zRem($this->_blank_list,$app_key);
                //调用接口的次数
                $data=$this->_apiAccessCount();
                return $data;
            }else{
                return [
                    'error'=>4,
                    'msg'=>"接口调用已经上线，请稍后重试"
                ];
            }


        }else{
            $data=$this->_apiAccessCount();
            return $data;
        }


    }
    /**
     * 调用接口的次数
     */
    private  function  _apiAccessCount(){
        $app_key=$this->_getKey();
        $count=Redis::incr($app_key);
        if($count==1){
            Redis::expire($app_key,60);
        }
        if($count>10){
           Redis::zAdd($this->_blank_list,time(),$app_key);
           Redis::del($app_key);
           return [
               'error'=>'5',
               'msg'=>"接口调用次数过多"
           ];
        }else{
           return [
               'error'=>0,
               'msg'=>"成功"
           ];
        }





    }






}
