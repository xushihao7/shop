<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\UserModel;

class UserController extends Controller
{
    //

	public function user($uid)
	{
		echo $uid;
	}

	public function test()
    {
        echo '<pre>';print_r($_GET);echo '</pre>';
    }

	public function add()
	{
		$data = [
			'name'      => str_random(5),
			'age'       => mt_rand(20,99),
			'email'     => str_random(6) . '@gmail.com',
			'reg_time'  => time()
		];

		$id = UserModel::insertGetId($data);
		var_dump($id);
	}
	//用户注册
    public function register(){
	    return view("user.reg");
    }
    public function doreg(Request $request){
	    //echo __METHOD__;
	    //echo "<pre/>";print_r($_POST);echo "<pre/>";
        $name=$request->input("u_name");
        if(empty($name)){
            header("refresh:1,url=/user/reg");
            echo "用户名不能为空";
            exit;
        }
        $where=[
            'name'=>$name
        ];
        $count=UserModel::where($where)->count();
        if($count>0){
            header("refresh:1,url=/user/reg");
            echo "用户名已经存在";
            exit;
        }
        $pwd1=$request->input("u_pwd");
        if(empty($pwd1)){
            header("refresh:1,url=/user/reg");
            echo "密码不能为空";
            exit;
        }
        $pwd2=$request->input("u_pwd2");
        if(empty($pwd2)){
            header("refresh:1,url=/user/reg");
            echo "确认密码不能为空";
            exit;
        }
        if($pwd1!==$pwd2){
            header("refresh:1,url=/user/reg");
            echo "确认密码必须和密码一致";
            exit;
        }
        $pwd=password_hash($pwd1,PASSWORD_BCRYPT);
        $age=$request->input('u_age');
        if(empty($age)){
            header("refresh:1,url=/user/reg");
            echo "年龄不能为空";
            exit;
        }
        $email=$request->input('u_email');
        if(empty($email)){
            header("refresh:1,url=/user/reg");
            echo "邮箱不能为空";
            exit;
        }
	    $data=[
	        'name'=>$name,
            'pwd'=>$pwd,
            'age'=>$age,
            'email'=>$email,
            'reg_time'=>time()
        ];
	    $uid=UserModel::insertGetId($data);
	    if($uid){
	        echo "注册成功";
            $token=substr(md5(time().mt_rand(1,99999)),10,10);
            setcookie("uid",$uid,time()+86400,"/",'',false,true);
            setcookie("uname",$name,time()+86400,"/",'',false,true);
            setcookie("token",$token,time()+86400,"/user","",false,true);
	        $request->session()->put("u_token",$token);
	        $request->session()->put("uid",$uid);
            header("refresh:1,url=/user/center");
        }else{
	        echo "注册失败";
        }
    }
    //用户登录
    public function  login(){
        return view("user.login");
    }
    public  function dologin(Request $request){
         $uname=$request->input("u_name");
         $pwd=$request->input("u_pwd");
         $where=[
             'name'=>$uname,
         ];
         $res=UserModel::where($where)->first();
         if($res){
             if(password_verify($pwd,$res->pwd)){
                 $token=substr(md5(time().mt_rand(1,99999)),10,10);
                 setcookie("uid",$res->uid,time()+86400,"/",'',false,true);
                 setcookie("uname",$res->name,time()+86400,"/",'',false,true);
                 setcookie("token",$token,time()+86400,"/","",false,true);
                 $request->session()->put('u_token',$token);
                 $request->session()->put("uid",$res->uid);
                 header("refresh:1,url=/user/center");
                 echo "登录成功";
             }else{
                 echo "账号或者密码错误,请重新登录";
                 header("refresh:1,url=/user/login");
                 exit;
             }
         }else{
             echo "账号或者密码错误,请重新登录";
             header("refresh:1,url=/user/login");
             exit;
         }
    }
    public function center(Request $request){
	    if($_COOKIE['token']!=$request->session()->get("u_token")){
	        die("非法请求");
        }else{
            if(empty($_COOKIE['uid'])){
                header("refresh:1,url=/user/login");
                echo "请先登录";
                exit;
            }else{
                echo "欢迎".$_COOKIE['uname']."登录";
            }
        }
       /* echo "u_toke:".$request->session()->get("u_token");
        echo '<pre>';print_r($_COOKIE);echo '</pre>';die;*/

    }
    //退出
    public function  quit(){
	    session()->forget("u_token");
        session()->forget("uid");
        cookie()->forget("uname");
        cookie()->forget("uid");
        cookie()->forget("token");
        echo "退出成功";
        header("refresh:1,url=/");
    }

}
