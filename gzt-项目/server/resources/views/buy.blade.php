@extends('layouts.app') 
@section('styles')
<style>
.banner {
  background: white;
}
.photograph{
  background-image: url(/images/120_60.png)
}
.left li{
  color: black;
}
.right li a{
  border-color: #cccccc!important;
  color: black!important;
}
body{
background: whitesmoke; 
}
.service{
  width: 100%;
  height:1039px;
}
.service .container .title{
   font-size: 32px;
   color: #1890ff;
   letter-spacing: 6px;
   margin-top: 50px;
   margin-bottom: 50px;
   text-align: center;
}
.version-item{
  width: 259px;
  height: 700px;
  text-align: center;
  margin-bottom: 55px;
  background-image: url(/images/bj.png);
}
.version{
  width: 259px;
  height: 160px;
  border-radius: 6px 6px 0px 0px;
}
.content{
  font-size: 18px;
  color: #000000;
}
.version-item .content{
  height: 524px;
  padding-top: 20px;
}
.content p{
  line-height: 22px;
  font-size: 15px;
}
.version-item .version .edition{
  padding-top: 20px;
  font-size: 24px;
  color: #2b2b2b;
} 
.version-item .version .month{
  font-size: 30px;
  color: #ffffff;
}
.version-item .version .people{
  font-size: 22px;
  color: #ffffff;
}
.support{
  text-align: center;
}
.second{
  font-size: 40px;
}
.tel{
  font-size: 24px;
}
.answer{
  width: 100%;
  height: 434px;
  background: #daeafb;
}
.answer .container .title{
  font-size: 40px;
  color: #40454a;
  text-align: center;
}
.question{
  margin-top: 40px;
  margin-bottom: 29px;
}
ul li .btn-primary{
  background: #00A0EA!important;
  color: white!important;
}
</style>
@endsection
@section('content')
<div class="service">
<div class="container">
  <div class="title">坚持做到工程行业最优美的产品，为您提供工程行业更好的服务!</div>
  <div class="show">
    <div class="row">
      <div class="col-3">
    <div class="version-item">
      <div class="version">
         <div class="edition">免费版</div>
         <div class="month">0元/月</div>
         <div class="people">5名成员</div>
      </div>
      <div class="content">
        <p style="margin-top:30px;">自由选择组织某一个人</p>
        <p>随意搭配组织方便快捷</p>
        <p>自由选择组织某一个人</p>
        <p>随意搭配组织方便快捷</p>
        <p>自由选择组织某一个人</p>
        <p>随意搭配组织方便快捷</p>
      </div>
    </div>
    </div>
    <div class="col-3">
    <div class="version-item">
        <div class="version">
           <div class="edition">人数版</div>
           <div class="month">299元/年</div>
           <div class="people">100名成员</div>
        </div>
      
      <div class="content">
        <div style="text-align:left; margin-left:45px">
        <p style="margin-top:30px;">≤5人 3免费  1G空间</p>
        <p>≤10人 认证后（免费）  1G空间</p>
        <p>≤100人 299元/年 2G空间 </p>
        <p>≤200人 259元/年 2G空间 </p>
        <p>≤300人 219元/年 2G空间 </p> 
        <p>≤400人 179元/年 2G空间 </p>
        <p>≤500人 139元/年 2G空间 </p>
        <p>≤500人 <a href="#">联系客服</a></p>
        </div>
        <p>更多特权</p>
        <p>专属企业网盘</p>
        <p>优先使用新功能</p>
        <p>优先售后与技术支持</p>
     
      </div>
    </div>
    </div>
    <div class="col-3">
      <div class="version-item">
          <div class="version">
             <div class="edition">短信版</div>
             <div class="month">0.1元/条</div>
             <div class="people">成员使用</div>
          </div>
          <div class="content">
            <p style="margin-top:30px;">可以发起审批使用</p>
            <p>发起审批超时使用</p>
            <p>邀请好友短信提醒</p>
            <p>等等...</p>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="version-item">
            <div class="version">
               <div class="edition">网盘版</div>
               <div class="month">6000元/年</div>
               <div class="people">容量：50GB</div>
            </div>
            <div class="content">
              <p style="margin-top:30px">可以装企业文件</p>
              <p>安全丶加密丶可靠</p>
              <p>企业成员文件管理</p>
              <p>文件加密与协作</p>
            </div>
          </div>
        </div>
        </div>
        <div class="support">
          <p class="second">需要网盘扩容丶或企业部署技术支持？</p>
          <p class="tel">欢迎与我们及时沟通：0374-6666666</p>
        </div>
  </div>
</div>
</div>
<div class="answer">
  <div class="container">
    <div class="title" style="padding-top:30px;">支付常遇见的问题与解答</div>
    <div class="row">
      <div class="col">
        <div class="question">1、创建一个企业可以邀请多少人？</div>
        <div class="reply">答：创建一个新企业可以邀请3,名好友、如果达到上限可以申请购买多人版、可以根据公司人数进行购买升级</div>
      </div>
      <div class="col">
          <div class="question">2、短信费用怎么收费？</div>
          <div class="reply">答：发起审批的时候您可以勾选短信通知、如果勾选上将会以短信形式通知审批方、这时会产生短信费用。</div>
        </div>
    </div>
    <div class="row">
        <div class="col">
          <div class="question">3、创建一个企业可以邀请多少人？</div>
          <div class="reply">答：创建一个新企业可以邀请3,名好友、如果达到上限可以申请购买多人版、可以根据公司人数进行购买升级</div>
        </div>
        <div class="col">
            <div class="question">4、短信费用怎么收费？</div>
            <div class="reply">答：发起审批的时候您可以勾选短信通知、如果勾选上将会以短信形式通知审批方、这时会产生短信费用。</div>
          </div>
      </div>
  </div>
</div>
@endsection