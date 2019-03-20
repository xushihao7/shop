<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use GuzzleHttp\Client;
class ApiController extends Controller
{
    public  function  contact(){
        $url='http://vm.api.com/test.php?type=1';
        $client=new Client();
        $r=$client->request('GET',$url);
        $response_arr=$r->getBody();
        //var_dump($response_arr);
        $data=json_decode($response_arr,true);
         echo "<pre>";print_r($data);echo "<pre/>";
    }
    //对称加密
    public  function  encryption(){
         $now=$_POST['t'];
         $key="pass";
         $salt="aaaaaa";
         $method="AES-128-CBC";
         $iv=substr(md5($salt.$now),5,16);
         $post_data=base64_decode($_POST['post_data']);
         $de_data=openssl_decrypt($post_data,$method,$key,OPENSSL_RAW_DATA,$iv);
         $data=json_decode($de_data,true);
         //print_r($data);die;
         $data2=[
            'name'=>'dongzhiheng',
            'hobby'=>'sleep'
        ];
        $now=time();
        $iv=substr(md5($salt.$now),5,16);
        $method="AES-128-CBC";
        $json_str=json_encode($data2);
        $enc_str=openssl_encrypt($json_str,$method,$key,OPENSSL_RAW_DATA,$iv);
        $post_data=base64_encode($enc_str);
        $data3=[
            't'=>time(),
            'post_data'=>$post_data
        ];
        echo json_encode($data3);
    }
    //非对称加密
    public  function asymmetric(){
        $sign = $_POST['sign'];
        $data=$_POST['data'];
        //var_dump($data);die;
        //读取公钥文件
        $pubKey = file_get_contents("./key/pub.pem");
        $res = openssl_get_publickey($pubKey);
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        //调用openssl内置方法验签，返回bool值
        $result = (openssl_verify($data,base64_decode($sign), $res, OPENSSL_ALGO_SHA256));
        openssl_free_key($res);
        var_dump($result);
    }
    //连接app
    public  function  application(Request $request){
          $username=$request->input("username");
          if($username){
              $response=[
                  'error'=>0,
                  'msg'=>"数据已经收到:".$username
              ];
          }else{
              $response=[
                  'error'=>5001,
                  'msg'=>"数据未收到".$username
              ];
          }
          return $response;
    }
    //接口登录
    public  function  login(Request $request){
        $email=$request->input("email");
        $pwd=$request->input("pwd");
        $where=[
            'email'=>$email
        ];
        $res=UserModel::where($where)->first();
        if($res){
            if(password_verify($pwd,$res->pwd)){
                $token=substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie("uid",$res->uid,time()+86400,"/",'',false,true);
                setcookie("uname",$res->name,time()+86400,"/",'',false,true);
                setcookie("token",$token,time()+86400,"/user","",false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put("uid",$res->uid);
                $response=[
                    'error'=>0,
                    'msg'=>'登录成功'
                ];
            }else{
                $response=[
                    'error'=>50001,
                    'msg'=>'登录失败,密码错误'
                ];
            }
        }else{
           $response=[
               'error'=>50001,
               'msg'=>'登录失败,账号错误'
           ];
        }
       return $response;
    }

}
