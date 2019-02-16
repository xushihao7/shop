<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lening-@yield('title')</title>
</head>
<body>
@section("header")
       <p style="color:blue">This is mama header</p>
@show
<div class="container">
     @yield('content')
</div>
@section('footer')
    <p style="color: blue">This is the mama footer.</p>
@show
</body>
</html>