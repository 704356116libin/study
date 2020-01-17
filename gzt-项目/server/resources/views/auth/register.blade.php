<!doctype html>
<html>
<style>
    p.error {
        color: #880000;
        display: none;
    }

    div#show_captcha {
        display: none;
    }

    .message {
        visibility: hidden;
        position: fixed;
        top: 0;
        left: calc(50% - 50px);
        padding: 8px 24px;
        transition: .2s ease-out;
        background: #fff;
        border-radius: 3px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .message i {
        color: #52c41a;
    }

    .message.show {
        visibility: visible;
        top: 30px;
    }
</style>

<head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>注册</title>
    <!-- Fonts -->
    <link href="/css/app.css" rel="stylesheet">
</head>

<body id="registerWrapper">
    <div class="message">
        <i aria-label="icon: check-circle" class="anticon anticon-check-circle">
            <svg viewBox="64 64 896 896" class="" data-icon="check-circle" width="1em" height="1em" fill="currentColor"
                aria-hidden="true" focusable="false">
                <path
                    d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z">
                </path>
            </svg>
        </i>
        注册成功
    </div>
    <div class="flex-center position-ref full-height clearfix">
        <div class="re-top">
            <div class="container">
                <div class="re-wrapper">
                    <div class="panel panel-default re-panel" style="padding:0 35px 10px 35px;">
                        <div class="panel-body">
                            <div class="text-center" style="color:#00a0e0;">
                                <div class="re-propaganda">注册工作通&nbsp;&nbsp;工作更轻松</div>
                            </div>
                            <div>
                                <form class="form-horizontal col-xs-12 col-sm-12 col-md-12" role="form">
                                    {{-- {{csrf_field()}} --}}
                                    <div class="form-group">
                                        <input id="tel" type="text" class="re-input login-input my_phone" name="tel"
                                            value="" placeholder="请输入手机号" maxlength="11">
                                        <span class="telMess"></span>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            <input id="password" type="password" class="re-input login-input pwd"
                                                maxlength="16" name="password" placeholder="密码(6-16位,字母、数字、符号任意两种以上组合)"
                                                value="">
                                            <span class="pwdMess"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input id="password_confirmation" type="password"
                                            class="re-input login-input old_pwd" maxlength="16"
                                            name="password_confirmation" placeholder="确认密码" value="">
                                        <span class="againPwdMess"></span>
                                    </div>
                                    <div class="form-group">
                                        <div class="re-item col-xs-8 col-sm-8 col-md-8">
                                            <input type="text" id="captcha_code" name="captcha_code" placeholder="输入验证码"
                                                maxlength="4" class="re-input login-input" value='' />
                                            <span class="captchaMess"></span>
                                        </div>
                                        <div class="re-item col-sm-3 col-md-3"
                                            style="position:relative;top:6px;float: right;">
                                            <img id="captcha" src="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="re-item col-sm-8 col-md-8">
                                            <input id="tel_code" type="text" name="tel_code"
                                                class="re-input login-input" value="" maxlength="4"
                                                placeholder="请输入短信验证码">
                                            <span class="verifyCodeMess"></span>
                                        </div>
                                        <div class="re-item col-sm-3 col-md-3"
                                            style="position:relative;top:6px;float: right;">
                                            <button type="button" class="get_tel_code re-codemess">获取验证码</button>
                                        </div>
                                    </div>
                                    <div class="form-group" style="margin-top:25px;">
                                        <a class="btn btn-block re-btn" id="register" style="color:#fff;">
                                            立即注册
                                        </a>
                                    </div>
                                    <div class="form-group">
                                        <div>
                                            注册即代表同意 <a class="agrees" data-toggle="modal" data-target="#user-protocol"
                                                style="cursor: pointer; color:#F66264;">《会员协议》</a>
                                        </div>
                                    </div>
                                    <div class="form-group clearfix re-bottom">
                                        已有账号 ? <a href="/login">登录</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('auth.user-protocol')
    <script src="/js/app.js"></script>
    <script>
        // $(".qqq").click(function(){
        //     var token =localStorage.getItem('token');
        //     axios.post("/api_test").then(function (res) {
        //         var status = res.data.status,
        //         message = res.data.message;
        //         console.log(1);
        //     })
        //     .catch(function (error) {
        //         console.log(error);
        //         console.log(3);
        //     });
        // })
    </script>
</body>

</html>