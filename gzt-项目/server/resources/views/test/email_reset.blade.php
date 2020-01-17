<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>密码重置页面</title>
    <style >
        p.error{
            color: #880000;
            display: none;
        }
        div#show_captcha{
            display: none;
        }
    </style>
</head>
<script  src="http://apps.bdimg.com/libs/jquery/1.11.1/jquery.js"></script>
<body>
@if($state)
    <input type="hidden" id="email_token" value="{{$token}}">
    <label>密码:</label> <input type="password" id="password" name="password" value='' /><br/>
    <label>确认密码:</label> <input type="password" id="password_confirmation" name="password_confirmation" value='' /><br/>
    <input type="submit"  id="reset_button" value='重置' /><br/>
@else
    链接已失效
@endif
<script type="text/javascript">
    /**
     * jQuery的Ajax传参方法
     */
    $(document).ready(function () {
        $('#tel').blur(function () {
            var data={
                'tel':$('#tel').val(),
                '_token':$('input[name="_token"]').val(),
            };
            $.ajax({
                type:'Post',
                url:'/checkTelExsit',
                dataType:'json',
                data:data,
                success:function (data) {
                    if(data.status===true){
                        alert('手机号存在');
                    }else{
                        alert('手机号不存在');
                    }
                },
                error:function (jqXHR) {
                    $('#status').val('发生错误:'+jqXHR.status);
                }
            });
        })
        $('#reset_button').click(function () {
            var data={
                'password':$('#password').val(),
                'password_confirmation':$('#password_confirmation').val(),
                '_token':$('input[name="_token"]').val(),
                'token':$('#email_token').val(),
            };
            $.ajax({
                type:'Post',
                url:'/resetPwdByEmail',
                dataType:'json',
                data:data,
                success:function (data) {
                    if(data.status==='success'){
                        alert('成功');
                    }else{
                        alert('no');
                    }
                },
                error:function (jqXHR) {
                    $('#status').val('发生错误:'+jqXHR.status);
                }
            });
        })
    });
</script>
</body>
</html>