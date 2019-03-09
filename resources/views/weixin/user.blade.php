@extends('layout.bst')
@section('content')
    <h2>商品列表</h2>
    <div class="container">
        <table  class="table table-bordered">
            <tbody>
            <tr>
                <td>复选框</td>
                <td>OPENID</td>
                <td>用户名称</td>
                <td>性别</td>
                 <td>操作</td>
            </tr>
            </tbody>
            <tbody>
            @foreach($list as $k=>$v)
                <tr class="a">
                    <td><input type="checkbox" id="flag" openid="{{$v['openid']}}"></td>
                    <td id="openid" >{{$v['openid']}}} </td>
                    <td>{{$v['nickname']}}</td>
                    <td>
                        @if($v['sex']==1)
                            男
                        @else
                           女
                        @endif
                    </td>

                    <td><input type="submit" value="拉黑" class="block"  openid="{{$v['openid']}}" ></td>
                </tr>

            @endforeach
            <tr>
                <td><input type="submit" value="设置标签" id="label"></td>
            </tr>
            </tbody>
        </table>
        {{$list->links()}}
    </div>
@endsection
@section('footer')
    <script src="{{URL::asset('/js/jquery-1.12.4.min.js')}}"></script>
    <script>
        $(".block").click(function(e){
            e.preventDefault();
            var _this=$(this);
            var openid=_this.attr("openid")
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url     :   '/weixin/pull',
                type    :   'post',
                data    :   {openid:openid},
                dataType:   'json',
                success :   function(d){
                   if(d==1){
                       alert("拉黑成功")
                   }
                }
            });


        })
    </script>
    <script>
        $("#label").click(function(e){
            e.preventDefault();
            var _this=$(this);
            $("#flag").each(function(){
                var _check=$(this)
                if(_this.prop("checked")==true){
                    var openid=_check.attr("openid")
                }

            })
            console.log(openid)
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url     :   '/weixin/setlabel',
                type    :   'post',
                data    :   {openid:openid},
                dataType:   'json',
                success :   function(d){
                    if(d==1){
                        alert("设置标签")
                    }
                }
            });





        })
    </script>
    @parent
@endsection
