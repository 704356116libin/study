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
                <button class="repay-btn center" onclick="getMemberPayUrl()">重新支付</button>
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
        var wxApi = {};
        var data = {
            query: {},
            paying: false, // 支付中页面显示
            order_no: '', // 订单号
            confirmShow: false,
            closeShow: false,
            wxOpenIdAuthParams: {
                token: '', // 授权令牌
                url: window.location.pathname.replace(/\//, '') + window.location.search, // 微信授权成功后的跳转链接（不含域名部分（如：index.html））
                scene: 0, // 场景：0 购买 VIP；1 购买第三方图片
                type: 1 // 设备类型：0 PC；1 手机
            },
            token: '',
            agentType: '' // 用户代理
        };

        //获取传过来的参数
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

        function getParamsNeeded() {
            if (data.query.t && ((data.query.sku_id && data.query.user_id && data.query.amount && data.query.length) || data.query.type)) {
                data.wxOpenIdAuthParams.token = data.query.t;
                data.token = data.query.t;
                data.sku_id = data.query.sku_id;
                data.user_id = data.query.user_id;
                data.amount = data.query.amount;
                data.length = data.query.length;
                data.order_no = data.query.order_no;
                getUserAgent();
            } else {
                alert('参数错误，请重试');
                getUserAgent();
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

        function closepay() {
            alert("支付状态错误");
            closeWindow();
        }

        function getMemberPayUrl() {
            // 获取会员支付链接
            if (data.sku_id == 0) {
                closepay();
                return;
            }
            var data1 = {
                sku_id: data.sku_id,
                amount: data.amount,
                length: data.length,
                user_id: data.user_id,
                order_no: data.order_no,
                // sku_id: 8,
                // amount: 10,
                // length: 1,
                // user_id: 11,
                // order_no:'20190507112135815399',
                items: [],

                pt: data.agentType === 'MicroMessenger' ? 2 : 1, // 1支付宝；2微信
                // url: 'mobpaysucceed?t=' + data.token,
                token: data.token,
                js_api: data.agentType === 'MicroMessenger'
            };
            data1.items.push({
                sku_id: data1.sku_id,
                amount: data1.amount,
                length: data1.length,
            });
            axios.post('/pay/getMemberPayUrl', data1,
            ).then(function (res) {

                // -1参数错误；-2未登录或令牌失效；-3支付平台错误；-4存在未支付的订单；-5创建订单出错；
                // -6支付宝下单出错；-7微信下单出错；-105暂不支持套餐降级；-109余额不足；-113服务器繁忙；1下单成功；2优惠券或余额已全额支付
                var code = res.data.code;

                switch (code) {
                    case 1:
                        document.getElementById('data-loading').style.display = "none"
                        data.paying = true;
                        document.querySelector('#paying').style.display = 'block'
                        data.deductShow = false
                        document.querySelector('#deductShow').style.display = 'none'
                        sessionStorage.setItem('orderId', res.data.order_no);
                        document.getElementById('order_no').innerHTML = res.data.order_no;

                        if (data.agentType != 'MicroMessenger') {
                            //支付宝支付
                            data.order_no = res.data.order_no;

                            alipayApp(res.data.orderStr);

                            // window.location.href = res.data.payUrl;
                        } else {
                            // 微信中采用"公众号支付"
                            wxApi = res.data.jsApiParams;
                            data.order_no = res.data.order_no;
                            document.getElementById('order_no').innerHTML = res.data.order_no;

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
                    case 3:
                        alert('该订单已经支付或者关闭');
                        break;
                    default:
                        alert('下单失败，错误码：' + code);
                        closeWindow();
                }
            }, function (error) {
                if (error.response.status === 422) {
                    // http 状态码为 422 代表用户输入校验失败
                    // var html = '<div>';
                    // _.each(error.response.data.errors, function (errors) {
                    //     _.each(errors, function (error) {
                    //         html += error+'<br>';
                    //     })
                    // });
                    // html += '</div>';
                    // swal({content: $(html)[0], icon: 'error'})
                    alert('商品不存在，或者商品数据错误，请重新提交订单');
                } else if (error.response.status === 400) {
                    alert('订单状态不正确，请查看订单是否关闭，请重新购买');
                } else {
                    // 其他情况应该是系统挂了
                    alert('系统错误');
                    // swal('系统错误', '', 'error');
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
            // axios.post('/getWxOpenIdAuthUrl', {
            //     headers:{'Authorization': 'Bearer ' + data.token,},
            //     params:param.join('&'),
            // }).then(function (response) {
            axios.post('/getWxOpenIdAuthUrl', param.join('&')
            ).then(function (response) {
                var code = response.data.code;
                // -1参数有误；-2token错误；1获取链接成功
                switch (code) {
                    case 1:
                        sessionStorage.setItem('hasOpenId', true);
                        // getMemberPayUrl();
                        window.location.replace(response.data.url);
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
                appId: wxApi.appId, //公众号名称，由商户传入
                timeStamp: wxApi.timeStamp, //时间戳，自1970年以来的秒数
                nonceStr: wxApi.nonceStr, //随机串
                package: wxApi.package,
                signType: wxApi.signType, //微信签名方式
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

        function alipayApp(orderStr, callback) {
            alipayReady(function () {
                AlipayJSBridge.call("tradePay", {
                    orderStr: orderStr
                }, function (result) {
                    // alert(JSON.stringify(result));
                    if (result.resultCode == 9000) {
                        alert('恭喜您支付成功');
                        closeWindow();
                    } else {
                        callback(result)
                    }
                });
            });
        }

        window.onload = function () {
            data.query = getParams();
            console.log(data.query);
            getParamsNeeded();
            if (data.query.type === 'repay') {
                repayOrder()
            } else {
                preGetPayUrl();
            }
        };

    </script>
@endsection