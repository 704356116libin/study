<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登陆</title>
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
<label>手机号:</label> <input type="tel" id="tel" name="tel" value='' /><br/>
<label>密码:</label> <input type="password" id="password" name="password" value='' /><br/>
<input type="submit"  id="login_button" value='登陆' /><br/>
<script type="text/javascript">
    /**
     * jQuery的Ajax传参方法
     */
    $(document).ready(function () {
        $('#login_button').click(function () {
            var data={
                'tel':$('#tel').val(),
                'password':$('#password').val(),
            };
            $.ajax({
                type:'Post',
                url:'/getApiToken',
                dataType:'json',
                data:data,
                success:function (data) {
                    if(data.status==='success'){
                        alert('登陆成功');
                    }else{
                        alert('账号密码错误');
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