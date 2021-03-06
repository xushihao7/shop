<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/','Index\IndexController@index')->middleware("check.passport");
/*Route::get('/', function () {
    phpinfo();
});*/


Route::get('/adduser','User\UserController@add');

//路由跳转
Route::redirect('/hello1','/world1',301);
Route::get('/world1','Test\TestController@world1');

Route::get('hello2','Test\TestController@hello2');
Route::get('world2','Test\TestController@world2');


//路由参数
Route::get('/user/test','User\UserController@test');
//Route::get('/user/{uid}','User\UserController@user');
Route::get('/month/{m}/date/{d}','Test\TestController@md');
Route::get('/name/{str?}','Test\TestController@showName');



// View视图路由
Route::view('/mvc','mvc');
Route::view('/error','error',['code'=>40300]);
//模板继承
Route::get('/view/test2','Test\TestController@viewTest');

// Query Builder
Route::get('/query/get','Test\TestController@query1');
Route::get('/query/where','Test\TestController@query2');

//Route::match(['get','post'],'/test/abc','Test\TestController@abc');
//Route::any('/test/abc','Test\TestController@abc');
Route::any('/test/abc','Test\TestController@abc');

//用户页面
Route::get("/user/center",'User\UserController@center');
//商品列表
Route::get("/goodslist",'Goods\IndexController@list');
//中间架
Route::get('/test/check_cookie','Test\TestController@checkCookie');

//购物车
Route::get('/cart','Cart\IndexController@index'); //购物车展示
Route::get('/goods/{goods_id}','Goods\IndexController@index');//购物车添加页面
Route::post('/cart/add2','Cart\IndexController@add2');//购物车添加
Route::get("/cart/del1/{goods_id}",'Cart\IndexController@del1');//购物车删除
//订单
Route::get("/order/add",'Order\IndexController@add');//提交订单号
Route::get("/order/list",'Order\IndexController@orderList');//订单展示
Route::get("/order/cancel/{order_id}",'Order\IndexController@cancel');//取消订单
//Route::get("/pay/order/{order_id}",'Pay\IndexController@order')->middleware("check.login");//订单支付
//退出
Route::get("/user/quit",'User\UserController@quit');
//支付
Route::get("/pay/alipay/test",'Pay\AlipayController@test');//
Route::get("/pay/alipay/return",'Pay\AlipayController@aliReturn');//支付宝同步
Route::post("/pay/alipay/notify",'Pay\AlipayController@aliNotify');//支付宝异步
Route::get("/pay/order/{order_id}",'Pay\AlipayController@pay');//订单支付
Auth::routes();


Route::get('/home', 'HomeController@index')->name('home');
//在线订座
Route::get('/movie/seat','Movie\IndexController@index');
Route::get('/movie/buy/{pos}/{status}','Movie\IndexController@buy');
//微信
Route::get('/weixin/test','Weixin\WeixinController@test');
Route::get('/weixin/valid','Weixin\WeixinController@validToken');
Route::get('/weixin/valid1','Weixin\WeixinController@validToken1');
Route::post('/weixin/valid1','Weixin\WeixinController@wxEvent');        //接收微信服务器事件推送
Route::post('/weixin/valid','Weixin\WeixinController@validToken');

Route::get('/weixin/create_menu','Weixin\WeixinController@createMenu');
//Route::get('/weixin/sendAll','Weixin\WeixinController@sendAll');//群发消息

Route::get('/weixin/getmedia','Weixin\WeixinController@mediaList');
Route::get('/form/show','Weixin\WeixinController@formShow');//永久素材表单测试
Route::post('/form/test','Weixin\WeixinController@formTest');//永久素材表单测试

Route::get('/form/show','Weixin\WeixinController@messageShow');//微信聊天页面
Route::get('/weixin/chat/get_msg','Weixin\WeixinController@message');//微信聊天页面
Route::post('/weixin/chat','Weixin\WeixinController@weixinChat');



//微信支付
Route::get('/weixin/pay/test/{order_id}','Weixin\PayController@test');     //微信支付测试
Route::post('/weixin/pay/notice','Weixin\PayController@notice');     //微信支付通知回调
Route::get('/weixin/pay/wxsuccess','Weixin\PayController@WxSuccess');     //微信支付通知回调
//微信登录
Route::get('/weixin/login','Weixin\WeixinController@wxLogin');//微信登录
Route::get('/weixin/getCode','Weixin\WeixinController@getCode');
//微信jssdk
Route::get('/weixin/jssdk','Weixin\WeixinController@wxJssdk');
//Route::get('/weixin/access','Weixin\WxUserController@getAccesstoken');
Route::get('/weixin/user','Weixin\WeixinController@userInfo');//获取用户信息列表
//Route::get('/weixin/userinfo','Weixin\WxUserController@userInfo');
Route::get('/weixin/userlist','Weixin\WeixinController@userList');//用户列表展示
Route::post('/weixin/pull','Weixin\WeixinController@userBlack');//用户拉黑
Route::post('/weixin/setlabel','Weixin\WeixinController@setlabel');//设置标签
Route::get('/api/link','Api\ApiController@contact');


Route::any('/test/curl','Test\TestController@curl2');//curl测试
Route::any('/api/encry','Api\ApiController@encryption');//对称加密
Route::any('/api/asy','Api\ApiController@asymmetric');//非对称加密

Route::post('/api/app','Api\ApiController@application');//app接口调试
Route::post('/api/login','Api\ApiController@login');//app登录
Route::post('/api/register','Api\ApiController@register');//app注册
Route::post('/apiLogin','Api\ApiController@apiLogin');//app passport登录
Route::post('/api/center','Api\ApiController@center');//app退出

Route::get('/api/pay','Api\ApiController@pay');//app支付
Route::get('/api/app/return','Api\ApiController@appReturn');//app支付

Route::get('/api/goodlist','Api\ApiController@goodsList');//app商品列表
Route::post('/api/goodinfo','Api\ApiController@goodsDetail');//app商品详情


Route::get('/apply','Apply\IndexController@apply');//用户申请
Route::post('/apply/success','Apply\IndexController@success');//待审核
Route::get('/shenhe','Apply\IndexController@shenhe');//审核请求
Route::get('/apply/pass/{uid}','Apply\IndexController@pass');//审核通过
Route::post('/api/redis','Apply\IndexController@redis')->middleware("check.apply");//审核通过
Route::post('/api/interface','Apply\IndexController@api')->middleware("check.api");//审核通过