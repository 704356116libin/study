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
    {{csrf_field()}}
    <label>手机号:</label> <input type="text" id="tel" name="tel" value='' /><br/>
    <label>图片验证:</label> <input type="captcha" id="captcha_code" name="captcha_code" value='' />
    <img id="captcha" src="/captcha"><br>
    <label>手机验证码:</label> <input type="text" id="tel_code" name="tel_code" value='' />
    <input type="hidden" id="tel_type" value="register">
    <input type="button" id="get_tel_code"  value='获取短信验证' /><br/>
    <label>密码:</label> <input type="password" id="password" name="password" value='' /><br/>
    <label>确认密码:</label> <input type="password" id="password_confirmation" name="password_confirmation" value='' /><br/>
    <input type="submit"  id="reset_button" value='重置' /><br/>
    <label>邮箱:</label>  <input id="email" type="email" />
    <input type="submit"  id="send_email_button" value='发送重置密码邮件' /><br/>
    <script type="text/javascript">
    /**
     * jQuery的Ajax传参方法
     */
    $(document).ready(function () {
        $('#email').blur(function () {
            var data={
                'email':$('#email').val(),
                '_token':$('input[name="_token"]').val(),
            };
            $.ajax({
                type:'Post',
                url:'/checkEmailExsit',
                dataType:'json',
                data:data,
                success:function (data) {
                    if(data.status===true){
                        alert('邮箱已存在');
                    }else{
                        alert('邮箱不存在');
                    }
                },
                error:function (jqXHR) {
                    $('#status').val('发生错误:'+jqXHR.status);
                }
            });
        })
        $('#send_email_button').click(function () {
            var data={
                'email':$('#email').val(),
                'type':'reset',
                '_token':$('input[name="_token"]').val(),
            };
            $.ajax({
                type:'Post',
                url:'/send_email',
                dataType:'json',
                data:data,
                success:function (data) {
                    if(data.status===true){
                      alert('邮箱已存在');
                    }else{
                        alert('邮箱不存在');
                    }
                },
                error:function (jqXHR) {
                    $('#status').val('发生错误:'+jqXHR.status);
                }
            });
        })
        /**
         *图片验证码重置
         */
        $('#captcha').click(function () {
            $('#captcha').attr('src','/captcha?'+Math.floor(Math.random()*10000));
        })
        /**
         * 获取短信验证码(图片验证码验证)
         */
        $('#get_tel_code').click(function () {
            var data={
                'captcha':$('#captcha_code').val(),
                'tel':$('#tel').val(),
                'type':$('#tel_type').val(),
            };
            $.ajax({
                type:'Post',
                url:'/getTelCode',
                dataType:'json',
                data:data,
                success:function (data) {
                    if(data.status==='success'){
                        alert('短信发送成功'+data.code);
                    }else{
                        alert('短信发送失败');
                        $('#captcha').attr('src','/captcha?'+Math.floor(Math.random()*10000));
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