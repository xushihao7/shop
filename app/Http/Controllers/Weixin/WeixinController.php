<?php

namespace App\Http\Controllers\Weixin;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Model\WeixinUser;
use GuzzleHttp;
use Illuminate\Support\Facades\Storage;
use App\Model\WeixinMedia;
use App\Model\WeixinChatModel;
class WeixinController extends Controller
{
    //
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    protected $redis_weixin_jsapi_ticket = 'str:weixin_jsapi_ticket';     //微信 jsapi_ticket
    public function test()
    {
        //echo __METHOD__;
        //$this->getWXAccessToken();
        echo 'Token: '. $this->getWXAccessToken();
    }

    /**
     * 首次接入
     */
    public function validToken1()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        echo $_GET['echostr'];
    }

    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");


        //解析XML
        $xml = simplexml_load_string($data);  //将 xml字符串 转换成对象
        //记录日志
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);

        $event = $xml->Event;                 //事件类型
        //var_dump($xml);echo '<hr>';
        $openid = $xml->FromUserName;               //用户openid
        //处理用户发送消息
        if(isset($xml->MsgType)){
            if($xml->MsgType=='text'){
                $msg=$xml->Content;
                $data=[
                    'msg'=>$xml->Content,
                    'msgid'=>$xml->MsgId,
                    'openid'=>$openid,
                    'msg_type'=>1,
                ];
                $res=WeixinChatModel::InsertGetId($data);
                //var_dump($res);
                $xml_response= '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['.$msg. date('Y-m-d H:i:s') .']]></Content></xml>';
                //echo $xml_response;

            }elseif($xml->MsgType=='image'){
                //视业务需求是否需要下载保存图片
                if(1){//下载图片素材
                    $file_name=$this->dlWxImg($xml->MediaId);
                    $xml_response='<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                    echo $xml_response;
                    //写入数据库
                    $data=[
                        'openid'=>$openid,
                        'add_time'=>time(),
                        'msg_type'=>'image',
                        'media_id'=>$xml->MediaId,
                        'format'    => $xml->Format,
                        'msg_id'    => $xml->MsgId,
                        'local_file_name'   =>$file_name
                    ];
                    $m_id = WeixinMedia::insertGetId($data);
                    var_dump($m_id);
                }
            }elseif($xml->MsgType=="voice"){ //处理语音消息
                $this->dlVoice($xml->MediaId);
                $xml_response='<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;
            }elseif($xml->MsgType=='video'){
                $this->dlVideo($xml->MediaId);
                $xml_response='<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. str_random(10) . ' >>> ' . date('Y-m-d H:i:s') .']]></Content></xml>';
                echo $xml_response;
            }
        }
          //判断事件类型
        if($event=='subscribe'){
            //echo 'openid: '.$openid;echo '</br>';
            $sub_time = $xml->CreateTime;               //扫码关注时间
            echo '$sub_time: ' . $sub_time;

            //获取用户信息
            $user_info = $this->getUserInfo($openid);
            echo '<pre>';print_r($user_info);echo '</pre>';

            //保存用户信息
            $u = WeixinUser::where(['openid'=>$openid])->first();
            //var_dump($u);die;
            if($u){       //用户不存在
                echo '用户已存在';
            }else{
                $user_data = [
                    'openid'            => $openid,
                    'add_time'          => time(),
                    'nickname'          => $user_info['nickname'],
                    'sex'               => $user_info['sex'],
                    'headimgurl'        => $user_info['headimgurl'],
                    'subscribe_time'    => $sub_time,
                ];

                $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                var_dump($id);
            }
        }elseif($event=="CLICK"){
            if($xml->EventKey=='kefu01'){
                $this->kefu01($openid,$xml->ToUserName);
            }
        }


    }
    /**
     * 客服处理
     * @param $openid   用户openid
     * @param $from     开发者公众号id 非 APPID
     */
    public function kefu01($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. 'Hello World, 现在时间'. date('Y-m-d H:i:s') .']]></Content></xml>';
        echo $xml_response;
    }
    //下载图片素材
    public  function  dlWxImg($media_id){
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
         //保存图片
        $client=new GuzzleHttp\Client();
        $response=$client->get($url);
        //获取文件名
        $file_info=$response->getHeader('Content-disposition');
        $file_name=substr(rtrim($file_info[0],'"'),-20);
        $wx_image_path='wx/image/'.$file_name;
        $r=Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){
            //保存成功
        }else{
            //保存失败
        }
        return $file_name;
    }
    //下载语音消息
    public  function  dlVoice($media_id){
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //保存图片
        $client=new GuzzleHttp\Client();
        $response=$client->get($url);
        //获取文件名
        $file_info=$response->getHeader('Content-disposition');
        $file_name=substr(rtrim($file_info[0],'"'),-20);
        $wx_image_path='wx/voice/'.$file_name;
        $r=Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){
            //保存成功
        }else{
            //保存失败
        }
    }
    //下载视频消息
    public  function  dlVideo($media_id){
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //保存图片
        $client=new GuzzleHttp\Client();
        $response=$client->get($url);
        //获取文件名
        $file_info=$response->getHeader('Content-disposition');
        $file_name=substr(rtrim($file_info[0],'"'),-20);
        $wx_image_path='wx/video/'.$file_name;
        $r=Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){
            //保存成功
        }else{
            //保存失败
        }

    }

    public  function formShow(){
        return view("test.form");
    }
    public  function  formTest(Request $request){
        //保存文件
        $img_file=$request->file("media");
        $img_origin_name=$img_file->getClientOriginalName();
        echo 'originName:'.$img_origin_name;echo"<hr/>";
        $file_ext=$img_file->getClientOriginalExtension();//后缀名
        echo "ext:".$file_ext;echo"<hr/>";
        //重命名
        $new_file_name=str_random(15).'.'.$file_ext;
        echo 'new_file_name:'.$new_file_name;echo "<hr/>";
        //保存文件
        $save_file_path=$request->media->storeAs("form_test",$new_file_name);//返回保存成功之后的路径
        echo 'save_file_path:'.$save_file_path;echo "<pre>";
        //上传至微信永久素材
        $this->upMaterialTest($save_file_path);

    }
    //上传永久素材
    public  function  upMaterialTest($save_file_path){
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client=new GuzzleHttp\Client();
        $response=$client->request('POST',$url,[
           'multipart'=>[
               [
                   'name'=>'media',
                   'contents'=>fopen($save_file_path,'r')
               ],
           ]
        ]);
        $body=$response->getBody();
        echo $body;echo "<hr>";
        $d=json_decode($body,true);
        echo "<pre>";print_r($d);echo"</pre>";
    }
    //获取永久素材列表
    public function mediaList(){
         $url="https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$this->getWXAccessToken();
         $data=[
             "type"=>'image',
             "offset"=>"0",
              "count"=>"1"
         ];
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $r = $client->request('POST', $url, [
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        $respone_arr = json_decode($r->getBody(),true);
        //echo '<pre>';print_r($respone_arr);echo '</pre>';

    }
    //客服消息展示
    public  function  messageShow(){
        $data=[
            'openid'=>"oNAoM6O4HQo3jNtBZ7FnG24nOBIo",
        ];
        return view("test.show",$data);
    }
    public  function  message(){
        $openid=$_GET['openid'];
        $pos=$_GET['pos'];
        $msg=WeixinChatModel::where(['openid'=>$openid])->where('id','>',$pos)->first();
        if($msg){
            $response = [
                'errno' => 0,
                'data'  => $msg->toArray()
            ];

        }else{
            $response = [
                'errno' => 50001,
                'msg'   => '服务器异常，请联系管理员'
            ];
        }

        die( json_encode($response));

    }
    //客服消息处理
   public  function  weixinChat(Request $request){
       $openid=$request->input("openid");
       $msg=$request->input("send_msg");
       $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();
       $data = [
           'touser'       =>$openid,
           'msgtype'      =>'text',
           'text'         =>[
               'content'  =>$msg,
           ]
       ];
       $client = new GuzzleHttp\Client();
       $response = $client->request('POST', $url, [
           'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
       ]);
       $body = $response->getBody();
       $arr = json_decode($body,true);
       //加入数据库
       if($arr['errcode']==0){
           $info = [
               'msg_type'      =>  2,
               'msg'   =>  $msg,
               'msgid'     =>  0,
               'openid'   =>  $openid,
           ];
         $id= WeixinChatModel::insertGetId($info);
          //var_dump($id);die;
       }
       return $arr;
   }
    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
        //$openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
        return $data;
        //echo '<pre>';print_r($data);echo '</pre>';
    }
    //创建菜单
    public function  createMenu(){
        //1获取access_token 拼接请求接口
         $access_token=$this->getWXAccessToken();
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
        //2请求微信接口
        $client=new GuzzleHttp\Client(['base_uri'=>$url]);
        $data = [
            "button"    => [
                [
                    "name"=>"nba球队",
                    "sub_button"=>[
                        [
                          'type'=>"view",
                            'name'=>"湖人队",
                            'url'=>"https://china.nba.com/lakers/"
                        ],
                        [
                            'type'=>"view",
                            'name'=>"勇士队",
                            'url'=>"https://china.nba.com/warriors/"
                        ],
                        [
                            'type'=>"view",
                            'name'=>"火箭队",
                            'url'=>"https://china.nba.com/rockets/"
                        ],

                    ]
                ],
                [
                    'name'=>'nba排名',
                    'sub_button'=>[
                        [
                            'type'=>"view",
                            'name'=>'联盟排名',
                            'url'=>'https://china.nba.com/standings/'
                        ]
                    ]
                ],
                [
                    "type"=>"click",
                    "name"=>"客服01",
                    "key"=>"kefu01"
                ]


            ]
        ];
        $r=$client->request("POST",$url,[
            "body"=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        //3 解析微信接口返回信息
        $response_arr=json_decode($r->getBody(),true);
        //print_r($response_arr);die;
        if($response_arr['errcode'] == 0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败，请重试";echo '</br>';
            echo $response_arr['errmsg'];

        }
    }
    //微信登录
    public  function  wxLogin(){
        return view("weixin.wxlogin");
    }
    //微信登录成功
    public  function  getCode(Request $request){
        //echo '<pre>';print_r($_GET);echo '</pre>';echo '<hr>';

        $code = $_GET['code'];

        //2 用code换取access_token 请求接口
        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe24f70961302b5a5&secret=0f121743ff20a3a454e4a12aeecef4be&code='.$code.'&grant_type=authorization_code';
        $token_json = file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
        //echo '<hr>';
        //echo '<pre>';print_r($token_arr);echo '</pre>';

        $access_token = $token_arr['access_token'];
        $openid = $token_arr['openid'];

        // 3 携带token  获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);

        $user_arr = json_decode($user_json,true);
        //echo '<hr>';
        //echo '<pre>';print_r($user_arr);echo '</pre>';
        $unionid=$user_arr['unionid'];
        $where=[
            'unionid'=>$unionid
        ];
        $wx_userInfo=WeixinUser::where($where)->first();
        if($wx_userInfo){
           $user_info=UserModel::where(['wx_id'=>$wx_userInfo->id])->first();
        }
        if(empty($wx_userInfo)){
            $data=[
                'openid'=>$user_arr['openid'],
                'nickname'=>$user_arr['nickname'],
                'sex'=>$user_arr['sex'],
                'headimgurl'=>$user_arr['headimgurl'],
                'unionid'=>$unionid,
                'add_time'=>time()
            ];
            $wx_id = WeixinUser::insertGetId($data);
            $rs = UserModel::insertGetId(['wx_id'=>$wx_id]);
            if($rs){
                $token=substr(md5(time().mt_rand(1,99999)),10,10);
                setcookie('uid',$rs,time()+86400,'/','',false,true);
                setcookie('token',$token,time()+86400,'/user','',false,true);
                $request->session()->put('u_token',$token);
                $request->session()->put('uid',$rs);
                echo '注册成功';
                header("refresh:1,url=/goodslist");

            }else{
                echo '注册失败';
            }
            exit;




        }
        $token=substr(md5(time().mt_rand(1,99999)),10,10);
        setcookie('uid',$user_info->uid,time()+86400,'/','',false,true);
        setcookie('token',$token,time()+86400,'/user','',false,true);
        $request->session()->put('u_token',$token);
        $request->session()->put('uid',$user_info->uid);
        echo "登录成功";
        header("refresh:1,url=/goodslist");


    }
    //微信JSSDK
    public function  wxJssdk(){

        $jsconfig=[
            'appid'=>env("WEIXIN_APPID"),
            'timestamp'=>time(),
            'noncestr'=>str_random(10)
        ];

        $sign=$this->wxJsSign($jsconfig);
        $jsconfig['sign']=$sign;
        $data=[
            'jsconfig'=>$jsconfig
        ];
        return view("weixin.jssdk",$data);
    }
    //签名算法
    public  function  wxJsSign($param){
        $current_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];     //当前调用 jsapi的 url
        $ticket = $this->getJsapiTicket();
        $str =  'jsapi_ticket='.$ticket.'&noncestr='.$param['noncestr']. '&timestamp='. $param['timestamp']. '&url='.$current_url;
        $signature=sha1($str);
        return $signature;
    }

    //获取jsapi_ticket
    public  function  getJsapiTicket(){
        //是否有缓存
        $ticket = Redis::get($this->redis_weixin_jsapi_ticket);
        if(!$ticket){           // 无缓存 请求接口
            $access_token = $this->getWXAccessToken();
            $ticket_url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
            $ticket_info = file_get_contents($ticket_url);
            $ticket_arr = json_decode($ticket_info,true);

            if(isset($ticket_arr['ticket'])){
                $ticket = $ticket_arr['ticket'];
                Redis::set($this->redis_weixin_jsapi_ticket,$ticket);
                Redis::setTimeout($this->redis_weixin_jsapi_ticket,3600);       //设置过期时间 3600s
            }
        }
        return $ticket;



    }
    //用户列表展示
    public  function  userList(){
        $list=WeixinUser::paginate(2);
        $data=[
            'list'=>$list
        ];
        Redis::set("listlist",$data);
        Redis::setTimeout("listlistaaa",3600);
        return view('weixin.user',$data);

    }
    //获取用户列表信息
    public  function  userInfo(){
        $url='https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->getWXAccessToken().'&next_openid=oNAoM6OXj2EDGss-t8GQ_rd3zn50';
        $data=json_decode(file_get_contents($url),true);
        echo "<pre>";print_r($data);echo "<pre/>";
    }
    //黑名单
    public  function  userBlack(Request $request){
        $openid= $request->input('openid');
        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchblacklist?access_token='.$this->getWXAccessToken();
        $data=[
            'openid_list'=>[$openid]
        ];
        $client=new GuzzleHttp\Client(['base_uri'=>$url]);
        $r=$client->request('POST',$url,[
            'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        $response_arr=json_decode($r->getBody(),true);
        echo "<pre>";print_r($response_arr);echo "<pre/>";die;
        if($response_arr){
            return 1;
        }else{
            return 0;
        }

    }
   //设置标签
    public  function  setlabel(Request $request){
        $openid=$request->input("openid");
        //echo $openid;die;
        $url='https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$this->getWXAccessToken();
        $data=[
           'openid_list'=>[
               $openid
           ],
            'tagid'=>123,
           
        ];
        $client=new GuzzleHttp\Client(['base_uri'=>$url]);
        $r=$client->request('POST',$url,[
            'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
         $response_arr=json_decode($r->getBody(),true);
        echo "<pre>";print_r($response_arr);echo "<pre/>";



    }



}
