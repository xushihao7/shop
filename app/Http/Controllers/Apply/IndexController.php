<?php

namespace App\Http\Controllers\Apply;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\ApplyModel;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{
    //
    public  function  apply(){
        return view("apply.list");
    }
    public  function  success(Request $request){
        $file = $request->file('photo');
        $name=$request->input("username");
        $number=$request->input("number");
        $yongtu=$request->input("yongtu");
        $file_ext=$file->getClientOriginalExtension();//后缀名
        //echo $file_ext;echo"<hr/>";die;
        //重命名
        $new_file_name=str_random(15).'.'.$file_ext;
        //echo 'new_file_name:'.$new_file_name;echo "<hr/>";die;
        $save_file_path=$request->photo->storeAs("apply_test",$new_file_name);//返回保存成功之后的路径
        //echo 'save_file_path:'.$save_file_path;echo "<pre>";die;
        $data=[
            'name'=>$name,
            'number'=>$number,
            'yongtu'=>$yongtu,
            'file'=>$save_file_path
        ];
        $apply_id=ApplyModel::insertGetId($data);
        echo "提交成功，待审核,请记住您的id:".$apply_id;
        if(empty($name)){
            echo "审核不通过,请填写您的姓名";
        }

    }
    public  function shenhe(){
        $apply_info=ApplyModel::get()->toArray();
        $data=[
            'list'=>$apply_info
        ];
        return view("apply.success",$data);
    }
    public  function pass($uid){
        $userInfo=ApplyModel::where(['uid'=>$uid])->first();
        $number=$userInfo['number'];
        $uid=$userInfo['uid'];
        //存取app_key
        $app_key=$number.mt_rand(1000,9999);
        $a_key="h:app_key:".$uid;
        Redis::hSet($a_key,'app_key',$app_key);
        //存取app_secret
        $app_secret=$number.mt_rand(1000,9999).$uid;
        $s_key="h:app_secret:".$uid;
        Redis::hSet($s_key,"app_secret",$app_secret);
        echo "审核通过";echo "<pre/>";
        //审核通过 修改状态
        ApplyModel::where(['uid'=>$uid])->update(['status'=>1]);
        //展示app_key和app_secret
        $a_key="h:app_key:".$uid;
        $app_keys=Redis::hGetAll($a_key);
        foreach($app_keys as $v){
            echo "app_key:".$v;echo "<br/>";
        }
        $s_key="h:app_secret:".$uid;
        $app_secrets=Redis::hGetAll($s_key);
        foreach($app_secrets as $value){
            echo "app_secret:".$value;
        }


    }
    public  function  redis( )
    {
        //print_r($_POST);die;
        $uid=$_POST['uid'];
        $a_key="h:app_key:".$uid;
        $app_keys=Redis::hGetAll($a_key);
        foreach($app_keys as $v){
            $app_key=$v;
        }
        $s_key="h:app_secret:".$uid;
        $app_secrets=Redis::hGetAll($s_key);
        foreach($app_secrets as $value){
            $app_secret=$value;
        }
        $now=$_POST['t'];
        $key="pass";
        $salt="aaaaaa";
        $method="AES-128-CBC";
        $iv=substr(md5($salt.$now),5,16);
        $post_data=base64_decode($_POST['post_data']);
        $de_data=openssl_decrypt($post_data,$method,$key,OPENSSL_RAW_DATA,$iv);
        $data=json_decode($de_data,true);

                    $data2=[
                        'msg'=>'ok',
                        'error'=>'0'
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

    public  function  api(Request $request){
       $name=$request->input("name");
       $pwd=$request->input("pwd");
       $data=[
           'name'=>$name,
           'pwd'=>$pwd
       ];
       return json_encode($data);
    }
}
