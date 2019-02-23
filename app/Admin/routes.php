<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource("/goods",GoodsController::class);
    $router->resource("/user",UserController::class);
    $router->resource("/weixinUser",WeixinController::class);
    $router->resource("/weixinMedia",WeixinMediaController::class);
    //上传永久素材
    $router->resource("/weixinMaterial",WeixinMaterialController::class);
    $router->post("/weixinMaterial",'WeixinMaterialController@formTest');
    //群发
    $router->get("/weixinSend",'WeixinMaterialController@sendShow');
    $router->post("admin/sendAll",'WeixinMaterialController@sendAll');
});
