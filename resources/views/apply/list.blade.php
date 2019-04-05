<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
     <form action="/apply/success" method="post" enctype="multipart/form-data" >
         {{csrf_field()}}
         <table>
             <tr>
                 <td>姓名</td>
                 <td><input type="text" name="username"></td>
             </tr>
             <tr>
                 <td>身份证号</td>
                 <td><input type="text" name="number"> </td>
             </tr>
             <tr>
                 <td>上传身份证照片</td>
                 <td><input type="file" name="photo"> </td>
             </tr>
             <tr>
                 <td>接口用途</td>
                 <td><textarea name="yongtu" cols="22x" rows="3px"></textarea> </td>
             </tr>
             <tr>
                 <td><input type="submit" value="申请"></td>
             </tr>
         </table>
     </form>
</body>
</html>