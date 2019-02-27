<?php

namespace App\Http\Controllers\Cart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
use App\Model\CartModel;
use Illuminate\Support\Facades\Auth;
class IndexController extends Controller
{

    public function  __construct()
    {
        $this->middleware("auth");
    }

    //购物车展示
    public function index(Request $request)
    {
        //查询购物车中的信息
        $uid=Auth::id();
        $cart_goods=CartModel::where(['uid'=>$uid])->get()->toArray();
        if(empty($cart_goods)){
            echo "购物车为空，快去选购商品吧";
            header("refresh:1,url=/goodslist");
            exit;
        }
        if($cart_goods){
            //获取商品的最新信息
            foreach ($cart_goods as $k=>$v){
                $goods_info=GoodsModel::where(['goods_id'=>$v['goods_id']])->first();
                $goods_info['num']=$v['num'];
                $list[]=$goods_info;
            }
        }
        $data=[
            'list'=>$list
        ];
        return view("cart.index",$data);

    }
    ///购物车添加
    public  function  add2(Request $request){
          $goods_id=$request->input("goods_id");
          $num=$request->input("num");
          //判断库存
         $stock=GoodsModel::where(['goods_id'=>$goods_id])->value("goods_num");
         if($stock<=0 || $num>$stock){
             $response=[
                 'errno'=>5001,
                 'msg'=>"库存不足"
             ];
             return $response;
         }
         //判断购物车中是否已经存在该商品 存在商品做累计 否则入库
         $where=[
             'goods_id'=>$goods_id,
             'uid'=>Auth::id()
         ];
         $cartInfo=CartModel::where($where)->first();
         $buy_num=$cartInfo['num'];
         if($cartInfo){
             $data2=[
                 'num'=>$num+$buy_num,
                 'session_token'=>session()->get("u_token"),
                 'add_time'=>time()
             ];
             $res=CartModel::where($where)->update($data2);
             if(!$res){
                 $response=[
                     'errno'=>5002,
                     'msg'=>"添加购车失败"
                 ];
                 return  $response;
             }
             $response=[
                 'errno'=>0,
                 'msg'=>"添加购物车成功"
             ];
             //修改库存
             /*$goods_num=GoodsModel::where(['goods_id'=>$goods_id])->decrement("goods_num");
             if($goods_num<=0){
                 header("refresh:1,url=/");
                 echo "库存不足";
                 exit;
             }*/
             return  $response;
         }else{
             //添加入库
             $data=[
                 'goods_id'=>$goods_id,
                 'num'=>$num,
                 'uid'=>Auth::id(),
                 'session_token'=>session()->get("u_token"),
                 'add_time'=>time()
             ];
             $cid=CartModel::insertGetId($data);
             if(!$cid){
                 $response=[
                     'errno'=>5002,
                     'msg'=>"添加购车失败"
                 ];
                 return  $response;
             }
             $response=[
                 'errno'=>0,
                 'msg'=>"添加购物车成功"
             ];
             return $response;
             //修改库存
             /*$goods_num=GoodsModel::where(['goods_id'=>$goods_id])->decrement("goods_num");
             if($goods_num<=0){
                 header("refresh:1,url=/");
                 echo "库存不足";
                 exit;
             }*/
         }

    }
     public function  del1($goods_id){
        if(empty($goods_id)){
            header("refresh:1,url=/cart");
            echo "该商品不存在";
            exit;
        }
         $where=[
             'goods_id'=>$goods_id,
             'uid'=>auth::id()
         ];
         $res=CartModel::where($where)->delete();
         if($res){
             echo "删除成功";
             header("refresh:1,url=/cart");
         }else{
             echo  "删除成功";
         }
     }








}