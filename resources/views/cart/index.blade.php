@extends("layout.bst")
@section("content")
    <div class="container">
       <table  class="table table-bordered ">
           <tr>
               <td>商品名称</td>
               <td>商品价格</td>
               <td>购买数量</td>
               <td>添加时间</td>
               <td>操作</td>
           </tr>
           @foreach($list as $k=>$v)
               <tr>
                   <td>{{$v->goods_name}}</td>
                   <td>￥{{$v->goods_price}}</td>
                   <td>{{$v->num}}</td>
                   <td>{{date("Y-m-d H:i:s",$v->add_time)}}</td>
                   <td><a href="/cart/del1/{{$v->goods_id}}" class="btn btn-info">删除</a></td>
               </tr>
           @endforeach
       </table>
    </div>
    <div class="container" style="text-align: right">
        <a href="/order/add"  id="submit_order" class="btn btn-primary">提交订单</a>
    </div>
 @endsection

 @section("footer")
   @parent
 @endsection