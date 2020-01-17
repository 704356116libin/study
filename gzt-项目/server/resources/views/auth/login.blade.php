<!doctype html> 
<html>
    <head>
          <!-- CSRF Token -->
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>登录</title>
        <link href="/css/app.css" rel="stylesheet">
       
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="re-top">
                <div class="container">
                    <div class="re-wrapper">
                        <div class="panel panel-default login-panel" style="padding: 0 25px 10px 25px;">
                            <div class="panel-body">
                                <div class="text-center" style="color:#00a0e0;">
                                    <div class="re-propaganda">登录工作通&nbsp;&nbsp;工作更轻松</div>
                                </div>
                                <div>
                                    <form class="form-horizontal col-sm-12 col-md-12 login-form" role="form">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <input id="myTel" type="text" class="re-input login-input" maxlength="11" name="tel" value="" placeholder="请输入手机号">
                                            <span class="myTelMess"></span>
                                        </div>
                                        <div class="form-group">
                                            <div>
                                                <input id="password" type="password" class="re-input login-input" maxlength="16" name="password" placeholder="请输入密码" value="">
                                                <span href="#" id="passwordeye" class="login-invisible bgImg"></span>
                                                <span class="loginPwd"></span>
                                            </div>
                                        </div>
                                        {{-- <div class="form-group clearfix">
                                            <label class="pull-left" style="font-weight: 400;">
                                                <input type="checkbox" id="remember" name="remember" class="remember vertical-way"><span class="vertical-way login-text">记住我</span>
                                            </label>
                                        </div> --}}
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1">
                                            <label class="custom-control-label remember login-text" for="customCheck1" id="remember" name="remember">记住我</label>
                                            <a class="forgot-pwd" href="/reset">忘记密码?</a>
                                        </div>

                                        <div class="form-group" style="margin-top:25px;">
                                            <a class="btn btn-block re-btn" id="login" style="color:#fff;">
                                                立即登录
                                            </a>
                                        </div>
                                        <div class="form-group" style="padding-top: 5%;">
                                            <div class="row" style="margin:0;">
                                                <div class="col login-line"></div>
                                                <div class="col text-center login-text">其他方式登录</div>
                                                <div class="col login-line"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row justify-content-md-center">
                                                <div class="col-md-auto">
                                                    <a href="https://www.dulifei.com/login_qq">
                                                        <b  class="login_qw">
                                                            <svg class="icons" aria-hidden="true">
                                                                <use xlink:href="#icon-qq"></use>
                                                            </svg>
                                                        </b> 
                                                    </a>
                                                </div>
                                                <div class="col-md-auto">
                                                    <b class="login_qw">
                                                        <svg class="icons" aria-hidden="true">
                                                            <use xlink:href="#icon-weixin"></use>
                                                        </svg>
                                                    </b>
                                                </div>
                                                <div class="col col-4  text-right" style="line-height: 50px;">
                                                    <a href="/register" class="login-link">免费注册账户</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
        <script>
            // if ($('#remember').hasClass("remember")) { //全选
            //     $('.remember').prop('indeterminate', true);
            // }else{
            //     $('#remember').removeClass("remember")
            //     $('.remember').prop('indeterminate', false);
            // }
            
        </script>
    </body>
</html>