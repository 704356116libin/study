<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>文件上传重置页面</title>
</head>
<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="https://cdn.bootcss.com/twitter-bootstrap/4.1.3/css/bootstrap.min.css">
<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="http://cdn.bootcss.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<body>


{{--<input id='location' class="form-control"  disabled>--}}
{{--<input type="button" id="i-check" value="浏览" class="btn" onclick="$('#i-file').click();">--}}
    <form id="demo" action="/redis" method="post" enctype="multipart/form-data">
        {{--<input type="file" id='i-file' name="file"  onchange="$('#location').val($('#i-file').val());"--}}
               {{--style="display: none" >--}}
        <input name="file" type="file" >
        <input type="submit" id="up" value="上传" class="btn" >
    </form>
<input type="submit" id="btn_excel" value="Excel测试" class="btn" >
<script type="text/javascript">
    /**
     * jQuery的Ajax传参方法
     */
    $(document).ready(function () {
        $('#btn_excel').click(function () {
            window.location.href='/excel';
            $.ajax({
                type:'Post',
                url:'/excel',

                success:function (data) {
                    if(data){
                        alert('提交成功！');
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