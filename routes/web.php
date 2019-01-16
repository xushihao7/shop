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
//用户注册
Route::get("/user/reg",'User\UserController@register');
Route::post("/register",'User\UserController@doReg');
//用户登录
Route::get("/user/login",'User\UserController@login');
Route::post("/login",'User\UserController@doLogin');
//用户页面
Route::get("/user/center",'User\UserController@center');
//中间架
Route::get('/test/check_cookie','Test\TestController@checkCookie')->middleware('check.cookie');

//购物车
Route::get('/cart','Cart\IndexController@index')->middleware('check.login'); //购物车展示
Route::get('/goods/{goods_id}','Goods\IndexController@index');//购物车添加页面
Route::post('/cart/add2','Cart\IndexController@add2')->middleware("check.login");//购物车添加
Route::get("/cart/del1/{goods_id}",'Cart\IndexController@del1')->middleware("check.login");//购物车删除
//订单
Route::get("/order/add",'Order\IndexController@add')->middleware("check.login");//提交订单号
Route::get("/order/list",'Order\IndexController@list')->middleware("check.login");//订单展示
//Route::get("/pay/order/{order_id}",'Pay\IndexController@order')->middleware("check.login");//订单支付
//退出
Route::get("/user/quit",'User\UserController@quit');
//支付
Route::get("/pay/alipay/test",'Pay\AlipayController@test');//
Route::get("/pay/alipay/return",'Pay\AlipayController@aliReturn');//支付宝同步
Route::post("/pay/alipay/notify",'Pay\AlipayController@aliNotify');//支付宝异步
Route::get("/pay/order/{order_id}",'Pay\AlipayController@pay')->middleware("check.login");//订单支付