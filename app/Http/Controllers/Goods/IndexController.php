<?php

namespace App\Http\Controllers\goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\GoodsModel;
class IndexController extends Controller
{
       public function  index($goods_id){
           $goods=GoodsModel::where(['goods_id'=>$goods_id])->first();
           if(!$goods){
               header("refresh:1,url=/");
               echo "该商品不存在";
               exit;
           }
           $data=[
               'goods'=>$goods
           ];
           return view("goods.index",$data);
       }

}
