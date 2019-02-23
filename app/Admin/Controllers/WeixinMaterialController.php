<?php

namespace App\Admin\Controllers;

use App\Model\WeixinMaterial;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use GuzzleHttp;
use Illuminate\Support\Facades\Redis;
use App\Model\WeixinUser;

class WeixinMaterialController extends Controller
{
    use HasResourceActions;
    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token
    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinMaterial);

        $grid->id('Id');
        $grid->media_id('Media id');
        $grid->file_name('File name');
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinMaterial::findOrFail($id));

        $show->id('Id');
        $show->media_id('Media id');
        $show->file_name('File name');
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinMaterial);
        $form->file("media","上传");
        return $form;
    }
    //群发页面
    public function sendShow(Content $content)
    {
        $f = new \Encore\Admin\Widgets\Form();
        $f->action('admin/sendAll');
        $f->textarea('name', '信息');
        return $content
            ->header('Create')
            ->description('description')
            ->body($f);
    }
    //群发信息
    public function sendAll(Request $request){
        $name=$request->all();
       //var_dump($name) ;die;
        $content=$name['name'];
        //echo $content;die;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->getWXAccessToken();
        //echo $url;exit;
        //openid
        $wxUserInfo = WeixinUser::get()->toArray();
        //var_dump($wxUserInfo);
        foreach($wxUserInfo as $v){
            $openid[]=$v['openid'];
        }
        //print_r($openid);
        //文本群发消息
        $data = [
            "touser"    =>  $openid,
            "msgtype"   =>  "text",
            "text"      =>  [
                "content"   =>  $content
            ]
        ];
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $r = $client->request('POST', $url, [
            'body' => json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        $respone_arr = json_decode($r->getBody(),true);
        //echo '<pre>';print_r($respone_arr);echo '</pre>';
        if($request['errcode']==0){
            echo "群发成功";
        }
    }





    //永久素材重命名
    public  function  formTest(Request $request){
        //保存文件
        $img_file=$request->file("media");
        $img_origin_name=$img_file->getClientOriginalName();
        //echo 'originName:'.$img_origin_name;echo"<hr/>";
        $file_ext=$img_file->getClientOriginalExtension();//后缀名
        //echo "ext:".$file_ext;echo"<hr/>";
        //重命名
        $new_file_name=str_random(15).'.'.$file_ext;
        echo 'new_file_name:'.$new_file_name;echo "<hr/>";
        //保存文件
        $save_file_path=$request->media->storeAs("form_test",$new_file_name);//返回保存成功之后的路径
        //echo 'save_file_path:'.$save_file_path;echo "<pre>";
        //上传至微信永久素材
        $this->upMaterialTest($save_file_path,$new_file_name);

    }
    //上传永久素材
    public  function  upMaterialTest($save_file_path,$new_file_name){
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
        //echo $body;echo "<hr>";
        $d=json_decode($body,true);
        echo "<pre>";print_r($d);echo"</pre>";
        $data=[
            'media_id'=>$d['media_id'],
            'file_name'=>$new_file_name
        ];
        $res=WeixinMaterial::insertGetId($data);
        if($res){
            echo "上传成功";
        }else{
            echo  "上传失败";
        }
    }
    //获取AccessToken
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



}
