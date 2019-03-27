<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\UserModel;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use App\Model\GoodsModel;

class ApiController extends Controller
{
    public  $app_id;
    public  $gate_way;
    public  $notify_url;
    public  $return_url;
    public  $rsaPrivateKeyFilePath="./key/priv.key";
    public  $aliPubKey='./key/ali_pub.key';
    public  function  __construct()
    {
        $this->app_id=env('APP_APPID');
        $this->gate_way=env('APP_GATEWAY');
        $this->notify_url=env("APP_NOTIFY");
        $this->return_url=env("APP_RETURN");
    }


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
    //连接app测试
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
        $name=$request->input("name");
        $pwd=$request->input("pwd");
        $where=[
            'name'=>$name
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
    //接口注册
    public  function  register(Request $request){
        $username=$request->input("name");
        $pwd=$request->input("pwd");
        $email=$request->input("email");
        $age=$request->input("age");
        $where=[
            'name'=>$username
        ];
        $count=UserModel::where($where)->count();
        if($count>0){
            $response=[
                'error'=>'5001',
                'msg'=>'账号已经存在'
            ];
        }else{
            $data=[
                'name'=>$username,
                'pwd'=>$pwd,
                'age'=>$age,
                'email'=>$email,
                'reg_time'=>time()
            ];
            $uid=UserModel::insertGetId($data);
            if($uid){
                $response=[
                    'error'=>'0',
                    'msg'=>'注册成功'
                ];
                $token=substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie("uid",$uid,time()+86400,"/",'',false,true);
                setcookie("token",$token,time()+86400,"/","",false,true);

            }else{
                $response=[
                    'error'=>'5001',
                    'msg'=>'注册失败'
                ];
            }
        }
        return $response;

    }
    //passport登录
    public  function  apiLogin(Request $request){
        $name=$request->input("name");
        $pwd=$request->input("pwd");
        $data=[
            'name'=>$name,
            'pwd'=>$pwd
        ];
        $url="http://passport.xsh.wangby.cn/apiLogin";
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        $rs=curl_exec($ch);
        //echo $rs;
        $response=json_decode($rs,true);
        return $response;
        //print_r($response);die;

    }
    //app退出
    public  function  center(Request $request){
        $uid=$request->input("uid");
        $token=$request->input("token");
        if(empty($uid) || empty($token)){
            $response=[
                'error'=>50001,
                'msg'=>'请重新登录'
            ];
            return $response;
        }else{
            $key="h:token:".$uid;
            $redis_token=Redis::hGet($key,"android");
            //var_dump($redis_token);die;
            if($token!=$redis_token){
                $response=[
                    'error'=>50002,
                    'msg'=>'token错误,非法登录'
                ];
            }else{
                $response=[
                    'error'=>0,
                    'msg'=>'token正确'
                ];
            }
            return $response;
        }


    }
    //app支付
    public  function  pay(){
        $order_id=mt_rand(10000,99999);

        $bizcont = [
            'subject'           => 'lening_shop: '.$order_id,
            'out_trade_no'      => $order_id,
            'total_amount'      => 10,
            'product_code'      => 'QUICK_WAP_WAY',

        ];
        //公共参数
        $data = [
            'app_id'   => $this->app_id,
            'method'   => 'alipay.trade.wap.pay',
            'format'   => 'JSON',
            'charset'   => 'utf-8',
            'sign_type'   => 'RSA2',
            'timestamp'   => date('Y-m-d H:i:s'),
            'version'   => '1.0',
            'notify_url'   => $this->notify_url,        //异步通知地址
            'return_url'   => $this->return_url,        // 同步通知地址
            'biz_content'   => json_encode($bizcont),
        ];

    }
    public  function  appReturn(){
        echo "<pre>";print_r($_GET);echo '</pre>';
    }



    public  function  goodsList(){
        $goodsInfo=GoodsModel::get();
        return $goodsInfo;
    }

}
