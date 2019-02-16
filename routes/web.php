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

Route::get('/', function () {
    return view('welcome');
});
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
Route::get("/order/list",'Order\IndexController@list');//订单展示
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
