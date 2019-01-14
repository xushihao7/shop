@extends("layout.bst")
@section("content")
<form class="form-horizontal" action="/user/login" method="post" style="margin-top: 30px">
    {{csrf_field()}}
    <div class="form-group" >
        <label for="inputEmail3" class="col-sm-2 control-label">账号</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="u_name" >
        </div>
    </div>
    <div class="form-group" >
        <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" name="u_pwd" >
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-info">登陆</button>
        </div>
    </div>
</form>
 @endsection
