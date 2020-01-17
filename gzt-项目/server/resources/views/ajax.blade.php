<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>注册页面</title>
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
<script src="http://apps.bdimg.com/libs/jquery/1.11.1/jquery.js"></script>
<body>
    <label>手机号:</label> <input type="text" id="tel" name="tel" value='' /><br/>
    @if ($errors->has('tel'))
        <span class="help-block">
    <strong>{{ $errors->first('tel') }}</strong>
</span><br>
    @endif
    <label>图片验证:</label> <input type="captcha" id="captcha_code" name="captcha_code" value='' /><br/>
    <label>图片key:</label> <input type="" id="captcha_key" name="captcha_key" value=''  /><br/>
    <img id="captcha" src="/captcha"><br><label>手机验证码:</label> <input type="text" id="tel_code" name="tel_code" value='' />
    <input type="hidden" id="tel_type" value="register">
    <input type="button" id="get_tel_code"  value='获取短信验证' /><br/>
    <label>密码:</label> <input type="password" id="password" name="password" value='' /><br/>
    @if ($errors->has('password'))
        <span class="help-block">
    <strong>{{ $errors->first('password') }}</strong>
</span><br>
    @endif
    <label>确认密码:</label> <input type="password" id="password_confirmation" name="password_confirmation" value='' /><br/>
    <input id="register_button" type="button"   value='注册' /><br/>
    @if ($errors->has('password_confirmation'))
        <span class="help-block">
    <strong>{{ $errors->first('password_confirmation') }}</strong>
</span><br>
    @endif
<input id="email" type="text" value="704356116@qq.com" disabled>
<input id="email_test" type="button" value="发送测试邮件"><br/>
<input id="tel_test" type="text" value="15237358570" disabled>
<input id="tel_test_code" type="text" value="" >
<input id="tel_test_button" type="button" value="发送短信验证">
<input id="tel_test_button2" type="button" value="短信验证校验"><br/>
<input id="auth_middleware" type="button"   value='验证中间件测试' /><br/>
<script type="text/javascript">
    /**
     * jQuery的Ajax传参方法
     */
    $(document).ready(function () {
        $('#email_test').click(function () {
            var data = {
                'email': $('#email').val(),
                'type': 'reset',
            };
            $.ajax({
                type: 'Post',
                url: '/send_email',
                dataType: 'json',
                data: data,
                success: function (data) {
                    if (data.status === 'success') {
                        alert('邮件发送成功');
                    } else {
                        alert('邮件发送不成功:' + data.message);
                    }
                },
                error: function (jqXHR) {
                    $('#status').val('发生错误:' + jqXHR.status);
                }
            });
        })
        $('#tel_test_button').click(function () {
            var data = {
                'tel': $('#tel_test').val(),
                'tel_code': $('#tel_test_code').val(),
                'type': 'verify',
            };
            $.ajax({
                type: 'Post',
                url: '/api/getTelCode',
                dataType: 'json',
                data: data,
                success: function (data) {
                    if (data.status === 'success') {
                        alert('短信发送成功' + data.code);
                    } else {
                        alert('邮件发送不成功:' + data.message);
                    }
                },
                error: function (jqXHR) {
                    $('#status').val('发生错误:' + jqXHR.status);
                }
            });
        })
        $('#tel_test_button2').click(function () {
            var data = {
                'tel': $('#tel_test').val(),
                'tel_code': $('#tel_test_code').val(),
                '_token': $('input[name="_token"]').val(),
            };
            $.ajax({
                type: 'Post',
                url: '/tel_verify',
                dataType: 'json',
                data: data,
                success: function (data) {
                    if (data.status === 'success') {
                        alert('短信验证通过');
                    } else {
                        alert('短信验证不通过:' + data.message);
                    }
                },
                error: function (jqXHR) {
                    $('#status').val('发生错误:' + jqXHR.status);
                }
            });
        })
        $('#send_get_ajax').click(function () {
            $.ajax({
                type: 'GET',
                url: '/cache?value=' + $('#json').val()
                + '&' + 'name=' + $('#name').val()
                + '&' + 'type=' + $('select.aaa').val()
                + '&' + 'cache=' + $('#cache_time').val(),
                dataType: 'json',
                jsonp: 'callback',
                success: function (data) {
                    if (data.status === 'success') {
                        cacheCallback(data.type, data.message, data.status);
                    } else {
                        cacheCallback(data.type, data.message, data.status);
                    }
                },
                error: function (jqXHR) {
                    alert('发生错误:' + jqXHR.status);
                }
            });
        })
        $('#register_button').click(function () {
            var data = {
                'tel': $('#tel').val(),
                'tel_code': $('#tel_code').val(),
                'password': $('#password').val(),
                'password_confirmation': $('#password_confirmation').val(),
            };

            $.ajax({
                type: 'Post',
                url: '/register',
                dataType: 'json',
                data: data,
                success: function (data) {
                    if (data.status === 'success') {
                        alert('注册成功');
                    } else {
                        alert('asdasdasd');
                    }
                },
                error: function (jqXHR) {
                    $('#status').val('发生错误:' + jqXHR.status);
                }
            });
        });
        /**
         *图片验证码重置
         */
        $('#captcha').click(function () {
            $.ajax({
                type: 'Post',
                url: '/api/captchas',
                dataType: 'json',
                data: [],
                success: function (data) {
                    $('#captcha').attr('src',data.captcha_image_content);
                    $('#captcha_key').val(data.captcha_key);
                },
                error: function (jqXHR) {
                    alert('获取图片验证码失败');
                }
            });
        });
        /**
         * 获取短信验证码(图片验证码验证)
         */
        $('#get_tel_code').click(function () {
            var data = {
                'captcha_code': $('#captcha_code').val(),
                'tel': $('#tel').val(),
                'captcha_key': $('#captcha_key').val(),
            };
            $.ajax({
                type: 'Post',
                url: '/api/getTelCode',
                dataType: 'json',
                headers: {
                    Accept : 'application/json',
                    Authorization : 'Bearer '+'12356',
                },
                data: data,
                success: function (data) {
                    if (data.status === 'success') {
                        alert('短信发送成功' + data.code);
                    } else {
                        alert('短信发送失败');
                    }
                },
                error: function (jqXHR) {
                    $('#status').val('发生错误:' + jqXHR.status);
                }
            });
        });
        $('#auth_middleware').click(function () {
            $data={
                tel:'15237358570',
                email:'704356116@qq.com',
                password:'123456..',
            };
            axios.post('/api/aaaa',$data)
                .then(function (res) {
                    alert(res);
                })
                .catch(function (err) {
                    alert(err);
                });
        });
    });
</script>
</body>
</html>