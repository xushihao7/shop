<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
     <table border="1">
         <tr>
             <td>姓名</td>
             <td>身份证号</td>
             <td>接口用途</td>
             <td>操作</td>
         </tr>
         @foreach($list as $v)
         <tr>
             <td>{{$v['name']}}</td>
             <td>{{$v['number']}}</td>
             <td>{{$v['yongtu']}}</td>
             <td>
                 @if($v['status']==0)
                 <a href="/apply/pass/{{$v['uid']}}">审核</a>
                 @elseif($v['status']==1)
                 审核成功
                 @endif
             </td>
         </tr>
          @endforeach
     </table>
</body>
</html>