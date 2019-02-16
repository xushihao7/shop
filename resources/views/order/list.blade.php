@extends("layout.bst")
@section("content")
    <div class="container">
        <h1>订单详情</h1>
        <table  class="table table-bordered ">
            <tr class="active">
                <td>订单号</td>
                <td>订单金额</td>
                <td>订单状态</td>

                <td>操作</td>
            </tr>
            @foreach($list as $k=>$v)
                <tr class="active">
                    <td>{{$v['order_sn']}}</td>
                    <td>￥{{$v['order_amount']}}</td>
                    <td>
                        @if($v['is_pay']==1)
                            已支付
                        @else
                            未支付
                        @endif
                    </td>
                    <td>
                        @if($v['is_pay']==2)
                        <a href="/pay/order/{{$v['order_id']}}" class="btn btn-info">去支付</a>
                        <a href="#" class="btn btn-info">取消订单</a>
                        @elseif($v['is_pay']==1)
                            <a href="#" class="btn btn-info">退款</a>
                        @elseif($v['order_status']==2)
                            订单已经取消
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection

@section("footer")
    @parent
@endsection