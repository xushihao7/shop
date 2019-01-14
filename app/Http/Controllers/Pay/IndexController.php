<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
class IndexController extends Controller
{

    public  function  order($order_id){
        //查询订单
        $orderInfo=OrderModel::where(['order_id'=>$order_id])->first();
        if(!$order_id){
            die("订单不存在");
        }
        //检查订单状态 是否已支付 已过期
        if($orderInfo->pay_time>0){
            die("此订单已经被支付，无法再次支付");
        }
        //支付宝支付

        //支付成功
        $data=[
            'pay_time'=>time(),
            'pay_amount'=>rand(1111,9999),
            'is_pay'=>1
        ];
        OrderModel::where(['order_id'=>$order_id])->update($data);
        header("refresh:1,url=/user/center");
        echo "支付成功";

    }

}
