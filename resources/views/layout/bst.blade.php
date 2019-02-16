<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>微商城</title>
    <link rel="stylesheet" href="{{URL::asset('/bootstrap/css/bootstrap.min.css')}}">
</head>
<body>

<div class="container">
    <!-- Static navbar -->
    <nav class="navbar navbar-default">
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button class="navbar-toggle collapsed" type="button" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand hidden-sm" href="/">首页</a>
                </div>
                <div class="navbar-collapse collapse" role="navigation">
                    <ul class="nav navbar-nav">
                        <li class="hidden-sm hidden-md"><a href="/cart/">购物车列表</a></li>
                        {{--<li><a href="#">Bootstrap3中文文档</a></li>
                        <li><a href="#">Bootstrap4中文文档</a></li>
                        <li><a href="#">Less 教程</a></li>
                        <li><a href="#">jQuery API</a></li>
                        <li><a class="reddot" href="#" target="_blank" >网站实例</a></li>--}}
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">个人中心 <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="/order/list">我的订单</a></li>
                                <li><a href="#">待收货</a></li>
                            </ul>
                        </li>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="/user/reg"><span class="glyphicon glyphicon-user"></span> 注册</a></li>
                            <li><a href="/user/login"><span class="glyphicon glyphicon-log-in"></span> 登录</a></li>
                            <li><a href="/user/quit"><span class="glyphicon glyphicon-log-in"></span> 退出</a></li>
                        </ul>
                    </ul>
                </div>
            </div>
        </div>

    </nav>
    @yield('content')
</div>

@section('footer')

    <script src="{{URL::asset('/js/jquery-1.12.4.min.js')}}"></script>
    <script src="{{URL::asset('/bootstrap/js/bootstrap.min.js')}}"></script>
@show
</body>
</html>