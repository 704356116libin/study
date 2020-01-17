@extends('layouts.app')
@section('content')
  <div class="page-wrapper">
    <div class="spinner" id="data-loading">
      <div class="rect1"></div>
      <div class="rect2"></div>
      <div class="rect3"></div>
      <div class="rect4"></div>
      <div class="rect5"></div>
      <p>订单获取中……</p>
    </div>
    <span class="logo"></span>
    <div class="paying" id="paying">
      <div class="hr"></div>
      <div class="pending-icon middle"></div>
      <p class="title middle">正在支付中</p>
      <div class="center-content">
        <p class="order-no content">订单号：
          <span id="order_no"></span>
        </p>
        <p class="content">您的订单正在确认中，请稍后...</p>
        <p class="tip content center">交易完成后将自动返回</p>
        <button class="repay-btn center" onclick="getTariffPackagesV2()">重新支付</button>
      </div>
    </div>
    <div class="deduct-page" id="deductShow">
      <div class="order-info">
        <div class="left">
          <span class="vip-level middle" id="vipIcon"></span>
          <div class="vip-info middle">
            <p id="typeDesc"></p>
            <p>
              <span id="dayCount"></span>个月</p>
          </div>
        </div>
        <div class="right">
          <p>应付金额：
            <span class="blue" id="discountPrice"></span>元</p>
          <p>抵扣金额：
            <span class="blue" id="deductCount"></span>元</p>
        </div>
      </div>
      <div class="pay-info">
        <p class="tip">您已购方案的剩余时长会折算为相应金额，供付款时抵扣</p>
        <p class="need-to-pay">需支付金额：
          <span class="blue" id="needToPay"></span>元</p>
        <button class="pay-now" onclick="preGetPayUrl()">立即支付</button>
      </div>
    </div>
  </div>
@endsection
@section('scriptsAfterJs')
  <script>
    'use strict';
    var httpConfig = {
      APIDOMAIN:"//api.pingshentong.com",
      APIDOMAINV2:"//api-v2.pingshentong.com"
    };
    var wxApi = {};
    var data = {
      query: {},
      paying: false, // 支付中页面显示
      deductShow: false, // 抵扣信息
      order_no: '', // 订单号
      tariff_package_id: 0, //会员套餐id
      confirmShow: false,
      closeShow: false,
      wxOpenIdAuthParams: {
        token: '', // 授权令牌
        url: window.location.pathname.replace(/\//, '') + window.location.search, // 微信授权成功后的跳转链接（不含域名部分（如：index.html））
        scene: 0, // 场景：0 购买 VIP；1 购买第三方图片
        type: 1 // 设备类型：0 PC；1 手机
      },
      curTariffPackage: {}, // 当前套餐信息
      deductCount: 0, // 抵扣金额
      bottomAmount: 0, // 优惠券使用门槛
      discountCodeAmount: 0, // 优惠券额度
      token: '',
      agentType: '' // 用户代理
    };

    function getParams() {
      var temp = location.search;
      var params = {};
      if (location.search.indexOf('?') >= 0) {
        temp = location.search.substr(1);
      }
      if (temp != '') {
        temp = temp.split('&');
        for (var index = 0; index < temp.length; index++) {
          var element = temp[index];
          var a = element.split('=');
          params[a[0]] = a[1] || '';
        }
      }
      return params;
    }

    function setEledata(dataName, value) {
      var temp = document.querySelector('#' + dataName);
      temp.innerHTML.replace('{{' + dataName + '}}', value)
    }
    function getUserAgent() {
      // 判断用户代理
      var ag = navigator.userAgent;
      if (ag.indexOf('MicroMessenger') !== -1) {
        data.agentType = 'MicroMessenger';
      } else if (ag.indexOf('Alipay') !== -1) {
        data.agentType = 'Alipay';
      } else {
        data.agentType = 'browser';
      }
    }

    function http(name, data) {
      var url = ''
      if (typeof name === "string") {
        url = httpConfig.APIDOMAIN + name
      } else {
        for (var value of name) {
          if (typeof value === "string") {
            url = httpConfig.APIDOMAINV2 + value
          }
        }
      }
      url += '.do?_dataType=json'
      return new Promise(function (resolve, reject) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        // xhr.setRequestHeader("Access-Control-Allow-Origin","*");
        // xhr.setRequestHeader("Access-Control-Allow-Headers","X-Requested-With,Content-Type");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;");
        xhr.withCredentials = true;
        xhr.send(data);
        xhr.onreadystatechange = function () {
          if (xhr.readyState == 4 && xhr.status == 200) {
            resolve(JSON.parse(xhr.responseText));
          } else if (xhr.readyState == 4 && xhr.status >= 400) {
            reject(xhr);
          }
        };
      });
    }

    function getParamsNeeded() {
      if (data.query.t && (data.query.id || data.query.type)) {
        data.wxOpenIdAuthParams.token = data.query.t;
        data.token = data.query.t;
        data.tariff_package_id = data.query.id;
        getUserAgent();
      } else {
        alert('参数错误，请重试');
        closeWindow();
      }
    }

    function preGetPayUrl() {
      if (data.agentType === 'MicroMessenger') {
        if (sessionStorage.getItem('hasOpenId')) {
          getMemberPayUrl();
        } else {
          getWxOpenIdAuthUrl();
        }
      } else {
        // 支付宝
        getMemberPayUrl();
      }

    }

    function getSurplusDaysDeductedAmount() {
      http(['/pay/getSurplusDaysDeductedAmount'], 'token=' + data.token).then(function (res) {

        var code = res.body.code;
        var count = 0;
        var vipLevel = 0; // 1 个人VIP；2 协作VIP；3 企业VIP
        switch (code) {
          case 1:
            count = res.body.count;
            vipLevel = res.body.vipLevel;
            if (vipLevel > 0 && vipLevel < data.curTariffPackage.level) {
              data.deductCount = count;
              document.getElementById('deductCount').innerHTML = (data.deductCount / 100).toFixed(2)
              document.getElementById('needToPay').innerHTML = data.discountPrice - data.query.quan - data.deductCount > 0 ? ((data.discountPrice - data.query.quan - data.deductCount) / 100).toFixed(2) : 0
              if (!data.paying) {
                document.getElementById('data-loading').style.display = "none"
                data.deductShow = true;
                document.querySelector('#deductShow').style.display = 'block'
              }
              if (sessionStorage.getItem('hasOpenId')) {
                preGetPayUrl()
              }
            } else {
              preGetPayUrl();
            }
            break;
          case -2:
            alert('获取可抵扣金额失败：token 有误');
            break;
          default:
            alert('获取可抵扣金额失败，错误码：' + code);
        }
        if (code !== 1) {
          closeWindow();
        }
      });
    }

    function getTariffPackagesV2() {

      http(['/vip/getVipPricingPackages']).then(function (res) {
        if (res.body.code !== 200) {
          alert('获取会员套餐出错，错误码：' + res.body.code);
          closeWindow();
          return;
        }
        res.body.data.forEach(function (pack) {
          pack.details.forEach(function (details) {
            details.packages.forEach(function (single) {
              // 单个套餐
              if (single.id == data.tariff_package_id) {
                data.curTariffPackage.level = pack.level;
                var vipIcon = document.getElementById('vipIcon')
                switch (pack.level) {
                  case 1:
                    vipIcon.classList.add("member")
                    break;
                  case 2:
                    vipIcon.classList.add("team")
                    break;
                  case 3:
                    vipIcon.classList.add("enterprise")
                    break;
                }
                data.curTariffPackage.typeDesc = pack.typeDesc;
                data.curTariffPackage.detail = single; // todo
                document.getElementById('typeDesc').innerHTML = pack.typeDesc
                document.getElementById('dayCount').innerHTML = single.dayCount / 31
                document.getElementById('discountPrice').innerHTML = (single.discountPrice - data.query.quan) / 100
                data.discountPrice = single.discountPrice
              }
            });
          });
        });
        getCouponInfo();
        getSurplusDaysDeductedAmount();
      }, function (err) {
        alert('获取会员套餐失败：网络出错');
        closeWindow();
      });
    }

    function getMemberPayUrlForUnpaidOrder() {
      var unpaid = {
        url: 'mobpaysucceed?t=' + data.query.t,
        token: data.query.t,
      }
      var param = []
      for (var t in unpaid) {
        param.push(t + '=' + unpaid[t])
      }
      http(['/pay/getMemberPayUrlForUnpaidOrder'], param.join('&')).then(function (res) {
        var result = res.body
        // 状态码（-1未登录；-3没有未支付的订单；-5下单失败；1获取支付链接成功）
        switch (result.code) {
          case 1:
            window.location.href = result.payUrl
            break
          default:
            alert('重新支付失败，错误码：' + result.code)
        }
      })
    }

    function getCouponInfo() {
      var param = 'token=' + data.token;

      if (data.query.cc && !data.query.quan) {
        param += '&c_code=' + data.query.cc;
      } else {
        return;
      }
      http('/pay/getCouponInfo', param).then(function (res) {
        if (res.body.qcb) {
          var info = res.body.qcb,
                  now = new Date().getTime();
          if (info.isUse === 1) {
            alert('优惠券已被使用');
          } else if (now <= info.startTime.time) {
            alert('未到优惠券使用开始时间');
          } else if (now >= info.endTime.time) {
            alert('优惠券已过期');
          } else {
            data.discountCodeAmount = info.amount; // 优惠券额度
            data.bottomAmount = info.bottomAmount;
            if (data.bottomAmount > data.curTariffPackage.detail.discountPrice) {
              data.discountPrice = data.curTariffPackage.detail.discountPrice
            } else {
              data.discountPrice = data.curTariffPackage.detail.discountPrice - data.discountCodeAmount
            }
            document.getElementById('discountPrice').innerHTML = (data.discountPrice / 100).toFixed(2)
            document.getElementById('needToPay').innerHTML = data.discountPrice - data.deductCount > 0 ? ((data.discountPrice - data.deductCount) / 100).toFixed(2) : 0
          }
        } else {
          alert('获取优惠券额度出错，错误码：' + res.body.code);
        }
      });
    }

    function closepay() {
      alert("支付状态错误");
      closeWindow();
    }

    function cancelMemberOrder() {
      var repay = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
      http(['/pay/cancelMemberOrder'], 'token=' + data.token).then(function (res) {
        // 0取消失败 ；1取消成功；-2用户未登录；-3用户会员订单状态有误
        var code = res.body.code;
        switch (code) {
          case 0:
            // alert('取消失败')
            break;
          case 1:
            if (repay) getMemberPayUrl();
            break;
          case -2:
            //  alert('请先登录')
            break;
          case -3:
            //  alert('会员订单状态有误')
            break;
          default:
                // alert('取消订单失败，错误码：' + code)
        }
        sessionStorage.removeItem("orderId");
      });
    }
    function getMemberPayUrl() {
      // 获取会员支付链接
      if (data.tariff_package_id == 0) {
        closepay();
        return;
      }


      var data1 = {
        tpi: data.tariff_package_id,
        pt: data.agentType === 'MicroMessenger' ? 2 : 1, // 1支付宝；2微信
        url: 'mobpaysucceed?t=' + data.token,
        token: data.token,
        js_api: data.agentType === 'MicroMessenger'
      };
      if (data.query.cc && !data.query.quan) {
        data1.cc = data.query.cc;
      } else if (data.query.quan) {
        data1.coupon_union_code = data.query.cc;
      }
      if (data.query.ic) {
        // 邀请码
        data1.invitation_code = data.query.ic
      }
      var param = [];
      for (var t in data1) {
        param.push(t + '=' + data1[t]);
      }
      http(['/pay/getMemberPayUrl'], param.join('&')).then(function (res) {

        // -1参数错误；-2未登录或令牌失效；-3支付平台错误；-4存在未支付的订单；-5创建订单出错；
        // -6支付宝下单出错；-7微信下单出错；-105暂不支持套餐降级；-109余额不足；-113服务器繁忙；1下单成功；2优惠券或余额已全额支付
        var code = res.body.code;
        switch (code) {
          case 1:
            document.getElementById('data-loading').style.display = "none"
            data.paying = true;
            document.querySelector('#paying').style.display = 'block'
            data.deductShow = false
            document.querySelector('#deductShow').style.display = 'none'
            sessionStorage.setItem('orderId', res.body.bill_serial_no);
            if (data.agentType != 'MicroMessenger') {
              //支付宝支付
              window.location.href = res.body.payUrl;
            } else {
              // 微信中采用"公众号支付"
              wxApi = res.body.jsApiParams;
              data.order_no = res.body.order_no;
              document.getElementById('order_no').innerHTML = res.body.order_no;

              if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                  document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
                } else if (document.attachEvent) {
                  document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                  document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
                }
              } else {
                onBridgeReady();
              }
            }
            break;
          case 2:
            window.location.replace('/mobpaysucceed?t=' + data.token)
            break;
          case -2:
            alert("令牌失效，请重新获取二维码");
            closeWindow();
            break;
          case -4:
            // alert("存在未支付的订单")
            cancelMemberOrder(true);
            break;
          case -105:
            alert("暂不支持套餐降级");
            closeWindow();
            break;
          case -109:
            alert('余额不足');
            break;
          default:
            alert('下单失败，错误码：' + code);
            closeWindow();
        }
      });
    }

    function getWxOpenIdAuthUrl() {
      // 微信JSSDK支付获取用户OpenId
      //getParamsNeeded();
      //if (sessionStorage.getItem('hasOpenId')) return;

      var param = [];
      for (var t in data.wxOpenIdAuthParams) {
        param.push(t + '=' + encodeURIComponent(data.wxOpenIdAuthParams[t]));
      }
      http('/login/getWxOpenIdAuthUrl', param.join('&')).then(function (res) {
        var code = res.body.code;
        console.log(res)

        // -1参数有误；-2token错误；1获取链接成功
        switch (code) {
          case 1:
            sessionStorage.setItem('hasOpenId', true);
            window.location.replace(res.body.url);
            break;
          case -1:
            alert('获取openId参数有误');
            break;
          case -2:
            alert('获取openId令牌错误');
            break;
        }
      });
    }

    function wxPaySuccess() {
      alert('恭喜您支付成功');
      closeWindow();
    }

    function wxPayError() {
      // cancelMemberOrder();
    }

    function closeWindow() {
      if (data.agentType === 'MicroMessenger') {
        var close = function close() {
          WeixinJSBridge.call('closeWindow');
        };

        if (typeof WeixinJSBridge == "undefined") {
          if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', close, false);
          } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', close);
            document.attachEvent('onWeixinJSBridgeReady', close);
          }
        } else {
          close();
        }
      } else if (data.agentType == 'Alipay') {
        alipayReady(function () {
          AlipayJSBridge.call('popWindow');
        });
      }
    }

    function onBridgeReady() {
      WeixinJSBridge.invoke('getBrandWCPayRequest', {
        appId: wxApi.appid, //公众号名称，由商户传入
        timeStamp: wxApi.timestamp, //时间戳，自1970年以来的秒数
        nonceStr: wxApi.nonce_str, //随机串
        package: wxApi.packages,
        signType: 'MD5', //微信签名方式
        paySign: wxApi.paySign //微信签名
      }, function (res) {

        // alert(JSON.stringify(res))

        if (res.err_msg == 'get_brand_wcpay_request:ok') {
          // 成功
          wxPaySuccess();
        } else {
          console.log("微信支付失败")
          // alert("微信支付失败");
          wxPayError();
        }
      });
    }
    function alipayReady(callback) {
      // 如果jsbridge已经注入则直接调用
      if (window.AlipayJSBridge) {
        callback && callback();
      } else {
        // 如果没有注入则监听注入的事件
        document.addEventListener('AlipayJSBridgeReady', callback, false);
      }
    }

    window.onload = function () {
      data.query = getParams();
      data.query.quan = ~~data.query.quan
      console.log(data.query);

      getParamsNeeded();
      if (data.query.type === 'repay') {
        getMemberPayUrlForUnpaidOrder()
      } else {
        getTariffPackagesV2();
      }
    };


  </script>
@endsection