<div class="banner">
    
        <a class="logo" href="#">
                <div class="photograph"></div>
            </a>
        <ul class="left">
            <li><a class="header-nav">首页</a></li>
            <li><a class="header-nav">帮助中心</a></li>
        </ul>
        <ul class="right">
            <li>
                <a href="{{explode('//', Request::url())[0]}}//pst.{{preg_split('/\/\/(www.)?/', Request::url())[1]}}/login" class="btn btn-primary enrol" style="background:transparent; color:white; border-color:white">登录</a>
            </li>
            <li>
                <a href="{{explode('//', Request::url())[0]}}//pst.{{preg_split('/\/\/(www.)?/', Request::url())[1]}}/register" class="btn btn-secondary enrol" style="background: transparent; color:white; border-color:white ">免费注册</a>
            </li>
        </ul>
    
</div>