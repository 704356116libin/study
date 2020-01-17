<!doctype html> 
<html>
    <head>
          <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>找回密码</title>
        <link href="/css/app.css" rel="stylesheet">
    </head>
    <body id="resetWrapper">
        <div class="flex-center position-ref full-height">
            <div class="re-top">
                <div class="container">
                    <div class="reset-wrapper">
                        <div class="panel panel-default reset-panel">
                            <div class="panel-body">
                                <div class="text-center" style="color:#00a0e0;">
                                    <div class="reset-title">密码重置</div>
                                </div>
                                <div class="resetPwdInfo"></div>
                                <div class="clearfix">
                                    <ul class="nav m-nav">
                                        <li><a href="#pwdByTel" class="active" data-toggle="tab" aria-expanded="true">手机号找回</a></li>
                                        <li><a href="#pwdByEmail" data-toggle="tab" aria-expanded="false">邮箱找回</a></li>
                                    </ul>
                                    <div id="myTabContent" class="tab-content reset-mess">
                                        <div class="tab-pane active" id="pwdByTel">
                                            <div class="form-group">
                                                <input id="reset-pwd-tel" type="text" class="re-input login-input my_phone" name="tel" value=""placeholder="请输入手机号" maxlength="11">
                                                <span class="telMess"></span> 
                                            </div>
                                            <div class="form-group">
                                                <div>
                                                    <input id="password" type="password" class="re-input login-input pwd" maxlength="16" name="password" placeholder="密码(6-16位,字母、数字、符号任意两种以上组合)" value="">
                                                    <span class="pwdMess"></span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <input id="password_confirmation" type="password" class="re-input login-input old_pwd" maxlength="16" name="password_confirmation" placeholder="确认密码" value="">
                                                <span class="againPwdMess"></span>
                                            </div>
                                            <div class="form-group">
                                                <div class="re-item col-xs-8 col-sm-8 col-md-8">
                                                    <input type="text" id="captcha_code" name="captcha_code" placeholder="输入验证码" maxlength="4" class="re-input login-input"  value='' />
                                                    <span class="captchaMess"></span>
                                                </div>
                                                <div class="re-item col-sm-3 col-md-3" style="position:relative;top:6px;float: right;">
                                                    <img id="captcha" src="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="re-item col-sm-8 col-md-8">
                                                    <input id="tel_code" type="text" name="tel_code" class="re-input login-input" value="" maxlength="4" placeholder="请输入短信验证码">
                                                    <span class="verifyCodeMess"></span>
                                                </div>
                                                <div class="re-item col-sm-3 col-md-3" style="position:relative;top:6px;float: right;">
                                                    <button type="button" class="reset_get_tel_code re-codemess">获取验证码</button>
                                                </div>
                                            </div>
                                            <div class="form-group" style="margin-top:25px;">
                                                <a  class="btn btn-block re-btn" id="resetPwdByTel" style="color:#fff;">
                                                    确认重置
                                                </a>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pwdByEmail">
                                            <div class="form-group">
                                                <input id="email" type="email" class="re-input login-input" name="email" value=""placeholder="请输入邮箱">
                                                <span class="emailMess"></span> 
                                            </div>
                                            <div class="form-group" style="margin-top:25px;">
                                                <a  class="btn btn-block re-btn" id="resetPwdByEmail" style="color:#fff;">
                                                    确认重置
                                                </a>
                                            </div>
                                        </div>
                                    </div>
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