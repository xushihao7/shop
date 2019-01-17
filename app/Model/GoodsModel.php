<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class GoodsModel extends Model
{
    public $timestamps=true;
    public $table="p_goods";
    public $updated_at=false;
    public $primaryKey='goods_id';

    //获取某字段时 格式化 该字段的值
    public function  getGoodsPriceAttribute($price){
        return $price /100;
    }
    public function  setGoodsPriceAttribute($price){
        $this->attributes['goods_price']=$price*100;
    }
}
