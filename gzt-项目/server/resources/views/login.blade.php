<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>登陆页面</title>
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
<label>缓存Json:</label> <input id="json" type="text" disabled="" name="json" value='{"title":"\u660e\u65e5\u4e4b\u661f","price":15,"num":99}' /><br/>
<label>文件名称:</label> <input id="name" type="text" name="name" value='' /><br/>
<label>操作缓存:</label>
<select class="aaa" id="type" >
    <option  value="alive">动态缓存</option>
    <option  value="save">保存缓存</option>
    <option  value="get">拿到缓存</option>
    <option  value="delete">清空缓存</option>
</select><br/>
<p class="error" ></p>
<label>请求状态显示:</label> <input id="status" type="text" disabled name="status" value='' /><br/>
<label>设置缓存过期时长(分钟):</label> <input id="cache_time" type="text"  name="cache_time" value='' /><br/>
<input type="button" value="提交Post请求" id="send_post_ajax" />
<input type="button" value="提交Get请求" id="send_get_ajax" /><br>
<label>图片验证码测试:</label> <img src="/captcha"><br>
<label>请输入图片验证码:</label> <input id="captcha" type="text"  name="captcha" value='' /><input type="button" id="check_captcha" value="验证"/><br/>
<div id="show_captcha">
    <label>验证码状态</label><p > </p>
</div>
<script type="text/javascript">
    /**
     * jQuery的Ajax传参方法
     */
    $(document).ready(function () {
        $('#send_get_ajax').click(function () {
            $.ajax({
                type:'GET',
                url:'/cache?value='+$('#json').val()
                                   +'&'+'name='+$('#name').val()
                                   +'&'+'type='+$('select.aaa').val()
                                   +'&'+'cache='+$('#cache_time').val(),
                dataType:'json',
                jsonp:'callback',
                success:function (data) {
                    if(data.status==='success'){
                        cacheCallback(data.type,data.message,data.status);
                    }else{
                        cacheCallback(data.type,data.message,data.status);
                    }
                },
                error:function (jqXHR) {
                    alert('发生错误:'+jqXHR.status);
                }
            });
        })
        $('#send_post_ajax').click(function () {
            var data={
                'value':$('#json').val(),
                'name':$('#name').val(),
                'type':$('select.aaa').val(),
                'cache_time':$('#cache_time').val(),
            };
            $.ajax({
                type:'Post',
                url:'/cache',
                dataType:'json',
                data:data,
                success:function (data) {
                    if(data.status==='success'){
                        cacheCallback(data.type,data.message,data.status);
                    }else{
                        cacheCallback(data.type,data.message,data.status);
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