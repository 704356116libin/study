@extends('layouts.app') 
@section('styles')
<style>
.banner{
    position:absolute;
}
.banner:hover{
    background-color: #00a0ea;
    transition: 0.3s ease;
}
.banner:hover .header-nav{
    color: white;
}
.banner:hover .header-nav:hover{
  color: #63e5ab;
}
</style>
@endsection
@section('content')
<div id="home">

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" style="height:620px;">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
         
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="https://gzts.oss-cn-beijing.aliyuncs.com/banner/sx.jpg" alt="First slide" style="height:620px;">
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="https://gzts.oss-cn-beijing.aliyuncs.com/banner/gn.jpg" alt="Second slide" style="height:620px;">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="sr-only">上一张</span>
                        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="sr-only">下一张</span>
                        </a>
    </div>
</div>
<div class="value" style="background:#f4f4f4;">
    <div class="container desc">
        <div class="row">
            <p>评审造价过程中问题</p>
        </div>
        <div class="row justify-content-between">
            <div class="col-3 item sort">
                <div class="picture picture-1">
                </div>
                <div class="title">邮箱混乱</div>
                <p>邮箱中很多项目混在一起，单个项目管理逻辑不清晰</p>
            </div>
            <div class="col-3 item sort">
                <div class="picture picture-2">
                </div>
                <div class="title">联系混淆</div>
                <p>评审中心和咨询公司及建设单位对接联系很容易混淆</p>
            </div>
            <div class="col-3 item sort">
                <div class="picture picture-3">
                </div>
                <div class="title">管理分散</div>
                <p>每个人手头工作及项目比较分散，缺少统一管理支配</p>
            </div>
        </div>
        <div class="row justify-content-between">
            <div class="col-3 item sort">
                <div class="picture picture-4">
                </div>
                <div class="title">项目把控</div>
                <p>项目时长把控不便，不能实时查看项目整体进展情况</p>
            </div>
            <div class="col-3 item sort">
                <div class="picture picture-5">
                </div>
                <div class="title">台账效率</div>
                <p>台账管理容易分散模糊，评审效率缺少统一把控衡量</p>
            </div>
            <div class="col-3 item sort">
                <div class="picture picture-6">

                </div>
                <div class="title">归档数据</div>
                <p>电子资料缺乏统一整理归档，项目的数据缺少汇总统计</p>
            </div>
        </div>
    </div>
</div>
<div class="problem" style="background:white;">
    <div class="container desc">
        <div class="row">
            <p>评审通能带来的价值</p>
        </div>
        <div class="row justify-content-between">
            <div class="col-3 item item-a" style="background:#6bbaf3;">
                <div class="picture_a picture_a-1">
            
                </div>
                <div class="title" style="color:white;">形象</div>
                <p class="details">提升工作效率及管理水平，从而大幅度提升各使用方的整体形象</p>
                <i></i>
            </div>
            <div class="col-3 item item-a" style="background:#f8687c;">
                <div class="picture_a picture_a-2">
                </div>
                <div class="title" style="color:white;">跟踪</div>
                <p class="details">单个项目主线跟踪，不混乱；台账实时跟进，整体进程更加清晰</p>
                <i></i>
            </div>
            <div class="col-3 item item-a" style="background:#7f8d9f;">
                <div class="picture_a picture_a-3">
                </div>
                <div class="title" style="color:white;">数据</div>
                <p class="details">项目数据及评审效率报表实时查看，方便数据统计数据报表导出</p>
                <i></i>
            </div>
        </div>
        <div class="row justify-content-between">
            <div class="col-3 item item-a" style="background:#87cf52;">
                <div class="picture_a picture_a-4">
                </div>
                <div class="title" style="color:white;">存储</div>
                <p class="details">项目相关图纸文件等存储云端，方便保存及查看下载，不丢失</p>
                <i></i>
            </div>
            <div class="col-3 item item-a" style="background:#f8810b;">
                <div class="picture_a picture_a-5">
                </div>
                <div class="title" style="color:white;">多端</div>
                <p class="details">电脑端和手机可以同时使用，方便及时查看项目，处理跟进工作</p>
                <i></i>
            </div>
            <div class="col-3 item item-a" style="background:#70d4c7;">
                <div class="picture_a picture_a-6">
                </div>
                <div class="title" style="color:white;">查看</div>
                <p class="details">评审项目可以实时查看及抄送，方便领导可以随时查看项目进度</p>
                <i></i>

            </div>
        </div>
    </div>
</div>
<div class="function" style="background:#f7f7f7">
    <div class="container feat">
        <div class="row">
            <p>评审通的主要功能</p>
        </div>
        <div id="carouselIndicators" class="carousel slide" data-ride="carousel" style="height:620px;">
            <ol class="carousel-indicators" style="width:100%;margin-left:0; justify-content:space-around;">
                <li data-target="#carouselIndicators" data-slide-to="0" class="active" style="height:84px; width:auto; background-color:#f7f7f7;">
                    <div class="indicators indicators-1"></div>
                    <div class="contents" style="text-indent:0; text-align:center; margin-top:20px; font-size:20px;">主线功能</div>
                </li>
                <li data-target="#carouselIndicators" data-slide-to="1" style="height:84px; width:auto; background-color:#f7f7f7;">
                    <div class="indicators indicators-2"></div>
                    <div class="contents" style="text-indent:0; text-align:center; margin-top:20px; font-size:20px;">报表功能</div>
                </li>
                <li data-target="#carouselIndicators" data-slide-to="2" style="height:84px; width:auto; background-color:#f7f7f7;">
                    <div class="indicators indicators-5"></div>
                    <div class="contents" style="text-indent:0; text-align:center; margin-top:20px; font-size:20px;">跟踪通知</div>
                </li>
                <li data-target="#carouselIndicators" data-slide-to="3" style="height:84px; width:auto;  background-color:#f7f7f7;  ">
                    <div class="indicators indicators-6"></div>
                    <div class="contents" style="text-indent:0; text-align:center; margin-top:20px; font-size:20px;">附件功能</div>
                </li>
                <li data-target="#carouselIndicators" data-slide-to="4" style="height:84px; width:auto; background-color:#f7f7f7;">
                    <div class="indicators indicators-7"></div>
                    <div class="contents" style="text-indent:0; text-align:center; margin-top:20px; font-size:20px;">联系方式</div>
                </li>
                </li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row">
                        <div class="col">
                            <img class="d-block w-100" src="/images/avatar/zhuxian.png" alt="First slide" style="width:100%;height:358px;">
                        </div>
                        <div class="col" style="border:  background:#f7f7f7;">
                            <p style="margin-bottom:51px;">主线功能</p>
                            <div class="title" style="width:481px; height:153px; text-align:left;">单个项目，单个跟踪，很多资料比如图纸、初稿、定稿、报告等都可以围绕主线进行，需要整体流程清晰明了
                                自动生成。
                                因为每个项目都有单独的跟踪，所以可以自动生成报告，台账、审定单。</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row">
                        <div class="col">
                            <img class="d-block w-100" src="/images/avatar/baobiao.png" alt="Second slide" style="height:358px;">
                        </div>
                        <div class="col" style="border: background:#f7f7f7;">
                            <p style="margin-bottom:51px;">报表功能</p>
                            <div class="title" style="width:481px; height:153px; text-align:left;">评审项目需要经常提交报表，给市里面或者领导查看，我们可以设置直接导出报表的功能，分日期、进度等查询条件导出。</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row">
                        <div class="col">
                            <img class="d-block w-100" src="/images/avatar/zidong.png" alt="Third slide" style="height:358px;">
                        </div>
                        <div class="col" style="border: background:#f7f7f7;">
                            <p style="margin-bottom:51px;">跟踪通知</p>
                            <div class="title" style="width:481px; height:153px; text-align:left;">评审项目因时间比较紧，要求比较严格，所以需要非常的认真仔细。我们可以设置初步的自动检查，比如信息价日期，申请金额是否对应，审减金额是否正确，审减比例是否正确等，保证项目整体下来不犯关键数字等错误。</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row">
                        <div class="col">
                            <img class="d-block w-100" src="/images/avatar/fujian.png" alt="Four slide" style="height:358px;">
                        </div>
                        <div class="col" style="border:  background:#f7f7f7;">
                            <p style="margin-bottom:51px;">附件功能</p>
                            <div class="title" style="width:481px; height:153px; text-align:left;">纸质版资料存在查找不便，不易保存等缺点，我们可以围绕主线，保持项目的整体附件存入网盘，到达一定期限保管甚至一直保管，随时随地可以调用查看。</div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="row">
                        <div class="col">
                            <img class="d-block w-100" src="/images/avatar/lianxi.png" alt="Five slide" style="height:358px;">
                        </div>
                        <div class="col" style="border:  background:#f7f7f7;">
                            <p style="margin-bottom:51px;">联系方式</p>
                            <div class="title" style="width:481px; height:153px; text-align:left;">不同项目，不同单位，存在交叉联络，“桥梁”联络，容易出现忘记、联系错误混乱、联系不到等，我们可以设置强大的联系人功能，支持项目顺利开展。</div>
                        </div>

                    </div>
                </div>
            </div>
            <a class="carousel-control-prev bannerleft" href="#carouselIndicators" role="button" data-slide="prev" style="height:358px; left:-200px;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">上一幅</span>
            </a>
            
            <a class="carousel-control-next" href="#carouselIndicators" role="button" data-slide="next" style="height:358px; right:-200px;">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="sr-only">下一幅</span>
            </a>
        </div>
    </div>
</div>
<div class="find" style="background:white;">
    <div class="container understand">
        <div class="row">
            <p>快速了解评审通</p>
        </div>
        <div class="row">
            <div class="col">
                <div class="photo photo-1">
                    <div class="photo photo-a"></div>
                </div>
                <div class="step">发起评审</div>
                <div class="detail">填写评审项目相关信息资料</div>
            </div>
            <div class="col">
                <div class="photo photo-2">
                    <div class="photo photo-a"></div>
                </div>
                <div class="step">专家评审</div>
                <div class="detail">转交给专家或合作咨询公司</div>
            </div>
            <div class="col">
                <div class="step"></div>
                <div class="photo photo-3">
                    <div class="photo photo-a"></div>
                </div>
                <div class="step">问题沟通</div>
                <div class="detail">对评审存在的问题进行对接</div>
            </div>
            <div class="col">
                <div class="photo photo-4"></div>
                <div class="step">评审完成</div>
                <div class="detail">评审完成生成造价导出报表</div>
                <div></div>
            </div>
        </div>
    </div>
</div>
<div class="use">
    <div class="container apply">
        <div class="row caption">
            <p style="margin-top:81px;">专注评审造价  马上免费使用</p>
        </div>
        <div class="row login" style="margin-top:41px; padding-left:270px;">
            <input type="text" placeholder="请输入您的手机号" style="font-size:18px; width:480px; height:56px; border-radius:3px; padding-left:20px; border:1px solid #f6f6f6;">
            <a href="{{explode('//', Request::url())[0]}}//pst.{{explode('//', Request::url())[1]}}/register" class="btn btn-primary frame text-aqua" style="font-size:18px;background:#20d6bb; color:white;padding-top:12px; margin-left:21px; border-color:#20d6bb;"> 免费注册 →</a>
        </div>
    </div>

</div>
@endsection