var openid = $("#openid").val();

setInterval(function(){
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/weixin/chat/get_msg?openid=' + openid + '&pos=' + $("#msg_pos").val(),
        type    :   'get',
        dataType:   'json',
        success :   function(d){
            if(d.errno==0){     //服务器响应正常
                //数据填充
                if(d.data.msg_type==1){
                    var msg_str = '<blockquote>' + d.data.created_at+
                        '<p>' + d.data.msg + '</p>' +
                        '</blockquote>'
                }else{
                    var msg_str = "<blockquote style='height:70px;'><font style='float: right;clear:both;'>" + d.data.create_at +
                        '</font><p style="float: right;clear:both;">' + d.data.msg + '</p>' +
                        '</blockquote>';
                }

                $("#chat_div").append(msg_str);
                $("#msg_pos").val(d.data.id)
            }else{

            }
        }
    });
},5000);

// 客服发送消息 begin
$("#send_msg_btn").click(function(e){
    e.preventDefault();
    var send_msg = $("#send_msg").val().trim();
    var msg_str = '<p style="color: mediumorchid"> >>>>> '+send_msg+'</p>';
    $("#chat_div").append(msg_str);
    $("#send_msg").val("");
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url     :   '/weixin/chat',
        type    :   'post',
        data    :   {openid:openid,send_msg:send_msg},
        dataType:   'json',
        success :   function(d){
                if(d.errcode==0){
                    alert("发送成功");
                }else{
                    alert("发送失败");
                }
        }
    });






});
// 客服发送消息 end
