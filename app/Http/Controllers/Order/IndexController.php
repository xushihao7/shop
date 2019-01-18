<?php

namespace App\Http\Controllers\order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;
use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Support\Facades\Auth;
class IndexController extends Controller
{
    public  function  __construct()
    {
        $this->middleware("auth");
    }

    public function  list(){
        $uid=Auth::id();
        $list=OrderModel::where(['uid'=>$uid])->get()->toArray();
        $data=[
            'list'=>$list
        ];
        return view("order.list",$data);
     }
    public  function  add(Request $request){
         //查询购物车中的商品
        $cartInfo=CartModel::where(['uid'=>Auth::id()])->orderby("cart_id","desc")->get()->toArray();
        if(empty($cartInfo)){
            header("refresh:1,url=/");
            echo "购物车为空";
            exit;
        }
        $order_amount=0;
        foreach ($cartInfo as $k=>$v){
             $goods_info=GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
             $goods_info['num']=$v['num'];
             $list[]=$goods_info;
             //计算价格
            $order_amount+=$goods_info['goods_price']*$v['num'];
        }
        //生成订单号
        $order_sn=OrderModel::generateOrderSN();
        //echo $order_sn;exit;
        $data=[
            'order_sn'=>$order_sn,
            'uid'=>Auth::id(),
            'order_amount'=>$order_amount,
            'add_time'=>time()
        ];
        $oid=OrderModel::insertGetId($data);
        if(!$oid){
            echo "下单失败";
        }
        echo "下单成功,您的订单号为".$order_sn."正在跳转支付";
        ///header("refresh:1,url=/pay/order/oid");
        //清除购物车
        CartModel::where(['uid'=>Auth::id()])->delete();

    }

}
