<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    public  $table='p_cart';
    public  $timestamps=false;
    public function  goodsInfo($goods_id){
       return GoodsModel::where(['goods_id'=>$goods_id])->get();
   }
}
