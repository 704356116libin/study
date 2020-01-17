<!doctype html> 
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>邮箱重置密码</title>
        <link href="/css/app.css" rel="stylesheet">
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="re-top">
                <div class="container">
                    <div class="re-wrapper">
                        <div class="panel panel-default reset-panel" style="padding: 0 25px 10px 25px;">
                            <div class="panel-body">
                                <div class="text-center" style="color:#00a0e0;">
                                    <div class="re-propaganda">邮箱重置密码</div>
                                </div>
                                <div>
                                    <form class="form-horizontal col-sm-12 col-md-12 login-form" role="form">
                                        {{ csrf_field() }}
                                        <input type="hidden" id="email_token" value="{{$token}}">
                                        <div class="form-group">
                                            <input id="password" type="password" class="re-input login-input pwd" maxlength="16" name="password" placeholder="密码(6-16位,字母、数字、符号任意两种以上组合)" value="">
                                            <span class="resetPwdMess"></span>
                                        </div>
                                        <div class="form-group">
                                            <input id="password_confirmation" type="password" class="re-input login-input old_pwd" maxlength="16" name="password_confirmation" placeholder="确认密码" value="">
                                            <span class="resetAgainPwdMess"></span>
                                        </div>
                                        <div class="form-group" style="margin-top:25px;">
                                            <a class="btn btn-block re-btn" id="email-reset" style="color:#fff;">
                                                重置密码
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/js/app.js"></script>
    </body>
</html>