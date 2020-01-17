// 登录模块 begin
window.onload = function () {
    switchPwd();
};
function switchPwd() {
    var passwordeye = $('#passwordeye');
    var showPwd = $("#password");
    passwordeye.off('click').on('click', function () {
        if (passwordeye.hasClass('login-invisible')) {
            passwordeye.removeClass('login-invisible').addClass('login-visible');//密码可见
            showPwd.prop('type', 'text');
        } else {
            passwordeye.removeClass('login-visible').addClass('login-invisible');//密码不可见
            showPwd.prop('type', 'password');
        };
    });
}
$('.login-form').keypress(function (e) {
    var tel = $("#myTel").val();
    var password = $("#password").val();
    if (e.keyCode === 13) {
        resLogin(tel, password);
    }
});
// 登录 模块
$("#login").click(function () {
    var tel = $("#myTel").val(),
        password = $("#password").val();
    resLogin(tel, password);
});
function resLogin(tel, password) {
    var platform = "web";
    var loginInfo = { tel, password, platform };

    if (!tel) {
        $('.myTelMess').text("请输入账号").addClass('errors');
    } else if (($.trim(tel) != "" && !/^1[3456789][0-9]{9}$/.test($.trim(tel)))) {
        $('.myTelMess').text("请输入正确的格式").addClass('errors');
    } else if (!password) {
        $('.loginPwd').text("请输入密码").addClass('errors');
    } else {
        $('.myTelMess').text("").removeClass('errors');
        $('.loginPwd').text("").removeClass('errors');

        axios.post('/getApiToken', loginInfo).then(res => {
            if (!$('#customCheck1').is(':checked')) { //记住我处理
                localStorage.setItem("rememberme", "no");
                Cookies.set('rememberme', "1", { expires: 7 });
            } else {
                localStorage.setItem("rememberme", "yes");
            }
            localStorage.setItem("access_token", res.data.access_token);
            localStorage.setItem("refresh_token", res.data.refresh_token);
            location.href = '/';

        }).catch(err => {
            if (err.response) {
                if (err.response.status === 401) {
                    $('.loginPwd').text("账号或密码错误").addClass('errors');
                    return
                } else {
                    alert('服务器异常, 请稍后再试');
                }
            } else {
                alert('服务器异常, 请稍后再试');
            }
        })
    }
}
// 登录模块 end
// 注册模块 begin

function allRes(params, names, mobileTips, btn, router, text, gainCode) {
    var obj = {};
    obj[names] = params;
    axios.post(router, obj).then(function (res) {
        var status = res.data.status,
            message = res.data.message;
        if (status == true) {
            var status = res.data.status;
            var message = res.data.message;
            $(mobileTips).text(text + '已存在').addClass("err-tips");
            $(mobileTips).text(message).addClass("errors");
            $(btn).addClass('no-selects');
            gainCode && $(gainCode).attr('disabled', true).addClass('no-selects');
        } else if (status == false) {
            $(mobileTips).text('').removeClass("err-tips");
            $(mobileTips).text("手机号可以使用").removeClass("errors");
            $(btn).removeClass('no-selects');
            gainCode && $(gainCode).attr('disabled', false).removeClass('no-selects');
        } else {
            $(mobileTips).text(message).addClass("errors");
            $(btn).addClass('no-selects');
        }
    })
        .catch(function (error) {
            console.log(error);
        });
}
// 重置密码
function restPwdCheckTel(params, names, mobileTips, btn, router, text, gainCode) {
    var obj = {};
    obj[names] = params;
    axios.post(router, obj).then(function (res) {
        var status = res.data.status,
            message = res.data.message;
        if (status == false) {
            var status = res.data.status;
            var message = res.data.message;
            // $(mobileTips).text(text + '不存在').addClass("err-tips");
            $(mobileTips).text(message).addClass("errors");
            $(btn).addClass('no-selects');
            gainCode && $(gainCode).attr('disabled', true).addClass('no-selects');
        } else if (status == true) {
            $(mobileTips).text('').removeClass("err-tips");
            $(mobileTips).text("手机号可以使用").removeClass("errors");
            $(btn).removeClass('no-selects');
            gainCode && $(gainCode).attr('disabled', false).removeClass('no-selects');
        } else {
            $(mobileTips).text(message).addClass("errors");
            $(btn).addClass('no-selects');
        }
    })
        .catch(function (error) {
            console.log(error);
        });
}

/**
 * 手机号验证-获取验证码
 * @param {string} seleter  手机号
 * @param {string} seleter1 提示信息
 * @param {string} codeBtn 验证码按钮
 * @param {string} captcha 图片验证码
 * @param {string} captchaMess 图片验证码提示信息
 * @param {*} regist 短信模板
 */
function telVerify(seleter, seleter1, codeBtn, captcha, captchaMess, regist) {
    var telreg = /^1[3456789][0-9]{9}$/;
    if (!seleter) {
        $(seleter1).text('请填写手机号').addClass("errors");
    } else if (!telreg.test(seleter)) {
        $(seleter1).text('请填写正确的手机格式').addClass("errors");
    } else if (!captcha) {
        $(captchaMess).text('请填写验证码').addClass("errors");
    } else {
        $('telMess').text('').removeClass("errors");
        $(captchaMess).text('').removeClass("errors");

        axios.post('/api/getTelCode', {
            'tel': seleter,
            'tel_time': Date.now(), //当前时间戳
            'captcha_code': captcha,  //验证码
            'tel_type': regist,
            "captcha_key": localStorage.getItem('captcha_key')

        }).then(function (res) {
            // console.log(res);
            var status = res.data.status;
            var message = res.data.message;
            if (status == "success") {
                var k = 60;
                var text = '获取验证码';
                $(codeBtn).attr("disabled", false).removeClass('no-selects');
                var timers = setInterval(function () {
                    if (k > 1) {
                        k--;
                        $(codeBtn).text(k + "秒");
                        $(codeBtn).attr("disabled", true).addClass('no-selects');
                    } else {
                        clearInterval(timers);
                        $(codeBtn).text(text);
                        $(codeBtn).attr("disabled", false).removeClass('no-selects');
                    }
                }, 1000)
                localStorage.setItem('tel_key', res.data.tel_key);
            } else if (status == "fail") {
                $(captchaMess).text(message).addClass('errors');
                $('#captcha')[0].click();
            } else {
                $(captchaMess).text("服务器出错了,稍后再试").addClass('errors');
                $('#captcha')[0].click();
            }
        }).catch(function (err) {
            //   console.log(err);
        });
    }
}
$('#captcha').click(function () {
    getcaptchas();
})
if ($('#registerWrapper').length !== 0 || $("#resetWrapper").length !== 0) { //注册页面首次加载验证码
    getcaptchas();
}

// 获取验证码
function getcaptchas() {
    axios.get("/api/captchas").then(res => {
        $('#captcha').attr('src', res.data.captcha_image_content);
        localStorage.setItem('captcha_key', res.data.captcha_key);
    }).catch(err => {
        console.log(err)
    })
}
//检测手机号是否存在
$('#tel').blur(function () {
    if ($.trim($(this).val()) == "" || ($.trim($(this).val()) != "" && !/^1[3456789][0-9]{9}$/.test($.trim($(this).val())))) {
        $(this).css("border-color", '#e4393c');
        $(this).siblings('span').text('请输入正确的手机号').addClass('errors');
    } else {
        $(this).css("border-color", '#ebebeb');
        $(this).siblings('span').text('').removeClass('errors');
        var params = $(this).val();
        allRes(params, 'tel', '.telMess', "#register", '/checkTelExsit', '手机号', '.get_tel_code');
    }
});
$('.get_tel_code').click(function () {// 注册页面  手机号验证获取验证码
    $('.telMess').text('');
    var tel = $('#tel').val();
    var captcha_code = $('#captcha_code').val();
    telVerify(tel, '.telMess', '.get_tel_code', captcha_code, '.captchaMess', "register");
});
$('.reset_get_tel_code').click(function () { // 密码重置 获取验证码
    $('.telMess').text('');
    var tel = $('#reset-pwd-tel').val();
    var captcha_code = $('#captcha_code').val();
    telVerify(tel, '.telMess', '.reset_get_tel_code', captcha_code, '.captchaMess', "reset");
});

/**
 * 密码判断
 */
$(".pwd").blur(function () {
    if ($.trim($(this).val()) == "" || ($.trim($(this).val()) != "" && !/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/.test($.trim($(this).val())))) {
        $(this).css("border-color", '#e4393c');
        $(this).siblings('span').text('字母、数字、符号两种以上的组合,6-16个字符').addClass('errors');
    } else {
        $(this).css("border-color", '#ebebeb');
        $(this).siblings('span').text('').removeClass('errors');
    }
});
$('.old_pwd').blur(function () {
    var new_pwd = $(this).parent().prev().find('.pwd').val();
    var old_pwd = $.trim($(this).val());
    if (new_pwd != old_pwd) {
        $(this).css("border-color", '#e4393c');
        $(this).siblings('span').text('两次密码不一致').removeClass('defaults').addClass('errors');
    } else {
        $(this).css("border-color", '#ebebeb');
        $(this).siblings('span').text('').removeClass('errors');
    }
})
/**
 * 注册账户
 * @param {string} tel  手机号
 * @param {string} password 密码
 * @param {string} password_confirmation 确认密码
 * @param {string} captcha_code 图片验证码
 * @param {string} tel_code 短信验证码
 */
$('#register').click(function () {
    var tel = $('#tel').val(),
        password = $('#password').val(),
        password_confirmation = $('#password_confirmation').val(),
        captcha_code = $('#captcha_code').val(),
        tel_code = $('#tel_code').val(),
        tel_key = localStorage.getItem("tel_key");
    params = { tel, password, password_confirmation, captcha_code, tel_code, tel_key };
    if (!tel) {
        $('.telMess').text("请输入手机号").addClass('errors');
    } else if (($.trim(tel) != "" && !/^1[3456789][0-9]{9}$/.test($.trim(tel)))) {
        $('.telMess').text("请输入正确的手机号").addClass('errors');
    } else if (!password) {
        $(".pwdMess").text('请输入密码').addClass("errors");
    } else if (($.trim(password) != "" && !/(?!^[0-9]+$)(?!^[A-z]+$)(?!^[^A-z0-9]+$)^.{6,16}$/.test($.trim(password)))) {
        $(".pwdMess").text('字母、数字、符号两种以上的组合,6-16个字符').addClass("errors");
    } else if (password != password_confirmation) {
        $(".againPwdMess").text('两次密码不一致').addClass("errors");
    } else if (!captcha_code) {
        $(".captchaMess").text('请输入验证码').addClass("errors");
    } else if (!tel_code) {
        $(".verifyCodeMess").text('请输入短信验证码').addClass("errors");
    } else {
        registerRes(params);
    }
})
function registerRes(params) {
    axios.post('/register', params).then(function (res) {
        var status = res.data.status;
        var message = res.data.message;
        if (status == "success") {
            $('.message').addClass('show');
            setTimeout(() => {
                $('.message').removeClass('show');
            }, 2000)
            window.location.href = '/login';
        } else if (status == "fail") {
            $('.telMess').text(message).addClass('errors');
        } else {
            $('.telMess').text(message).addClass('errors');
        }
    }).catch(function (err) {
        console.log(err);
    })
}
// 注册模块 end
//手机号方式重置密码
$('#reset-pwd-tel').blur(function () {
    if ($.trim($(this).val()) == "" || ($.trim($(this).val()) != "" && !/^1[3456789][0-9]{9}$/.test($.trim($(this).val())))) {
        $(this).css("border-color", '#e4393c');
        $(this).siblings('span').text('请正确的手机号').addClass('errors');
    } else {
        $(this).css("border-color", '#ebebeb');
        $(this).siblings('span').text('').removeClass('errors');
        var params = $(this).val();
        restPwdCheckTel(params, 'tel', '.telMess', "#resetPwdByTel", '/checkTelExsit', '手机号', '.reset_get_tel_code');
    }
});
$("#resetPwdByTel").click(function () {
    var tel = $("#reset-pwd-tel").val(),
        password = $("#password").val(),
        password_confirmation = $.trim($("#password_confirmation").val()),
        captcha_code = $('#captcha_code').val(),
        tel_code = $('#tel_code').val(),
        tel_key = localStorage.getItem("tel_key");
    resetPwdByTel(tel, password, password_confirmation, captcha_code, tel_code, tel_key);
});
function resetPwdByTel(tel, password, password_confirmation, captcha_code, tel_code, tel_key) {
    var loginInfo = { tel, password, password_confirmation, captcha_code, tel_code, tel_key };
    if (!tel) {
        $('.telMess').text("请输入账号").addClass('errors');
    } else if (($.trim(tel) != "" && !/^1[3456789][0-9]{9}$/.test($.trim(tel)))) {
        $('.telMess').text("请输入正确的格式").addClass('errors');
    } else if (!password) {
        $('.loginPwd').text("请输入密码").addClass('errors');
    } else if (password != password_confirmation) {
        $('.againPwdMess').text("两次密码不一致").addClass('errors');
    } else if (!captcha_code) {
        $('.captchaMess').text("请输入验证码").addClass('errors');
    } else if (!tel_code) {
        $('.verifyCodeMess').text("请输入短信验证码").addClass('errors');
    } else {
        $("#pwdByTel span").each(function () {
            $(this).text("").removeClass('errors');
        })
        axios.post('/resetPwdByTel', loginInfo).then(res => {
            var status = res.data.status;
            var message = res.data.message;
            if (status == "success") {
                $('.resetPwdInfo').text(message).addClass('prompt-mess');
            } else if (status == "fail") {
                $('.verifyCodeMess').text(message).addClass('errors');
            }
        }).catch(err => {
            console.log(err);
        })
    }
}
//邮箱方式重置密码
$('#email').blur(function () {
    console.log(1);
    var email = $(this).val();
    if (!email) {
        $('.emailMess').text("请输入邮箱").addClass('errors');
        $("#resetPwdByEmail").addClass('disables-btn');
    } else if (($.trim(email) != "" && !/^\+?[a-z0-9](([-+.]|[_]+)?[a-z0-9]+)*@([a-z0-9]+(\.|\-))+[a-z]{2,6}$/.test($.trim(email)))) {
        $('.emailMess').text("请输入正确的邮箱格式").addClass('errors');
        $("#resetPwdByEmail").addClass('disables-btn');
    } else {
        $('.emailMess').text("").removeClass('errors');
        axios.post("/checkEmailExsit", { email }).then(res => {
            var status = res.data.status;
            var message = res.data.message;
            if (status == true) {
                $('.emailMess').text("您可以通过邮箱找回密码").removeClass('errors');
                $("#resetPwdByEmail").removeClass('disables-btn');
                resetPwdByEmail();
            } else if (status == false) {
                $('.emailMess').text(message).addClass('errors');
                $("#resetPwdByEmail").addClass('disables-btn');
            }
        }).catch(error => {
            console.log(error);
            // resetPwdByEmail(email);
        });
    }
})
function resetPwdByEmail() {
    $('#resetPwdByEmail').click(function () {
        var email = $("#email").val();
        var obj = {
            email: email,
            type: "reset"
        }
        axios.post("/send_email", obj).then(function (res) {
            var status = res.data.status;
            var message = res.data.message;
            if (status == "success") {
                $("#resetPwdByEmail").removeClass('disables-btn');
                $('.emailMess').text(message).removeClass('errors');
            } else if (status == "fail") {
                $("#resetPwdByEmail").addClass('disables-btn');
                $('.emailMess').text("邮箱不存在").addClass('errors');
            } else {
                $('.emailMess').text("系统繁忙,请稍后再试").addClass('errors');
            }
        })
            .catch(function (error) {
                console.log(error);
            });
    })
}
//通过邮箱重置密码
$("#email-reset").click(function () {
    var password = $('#password').val(),
        password_confirmation = $('#password_confirmation').val(),
        token = $('#email_token').val();
    var obj = { password, password_confirmation, token };
    if (!password) {
        $('.resetPwdMess').text("字母、数字、符号两种以上的组合,6-16个字符").addClass('errors');
    } else if (password != password_confirmation) {
        $('.resetAgainPwdMess').text("两次密码不一致").addClass('errors');
    } else {
        axios.post("/resetPwdByEmail", obj).then(function (res) {
            var status = res.data.status;
            var message = res.data.message;
            if (status == "success") {
                $("#resetPwdByEmail").removeClass('disables-btn');
                $('.resetPwdMess').text(message).removeClass('errors');
            } else if (status == "fail") {
                $("#resetPwdByEmail").addClass('disables-btn');
                $('.resetPwdMess').text(message).addClass('errors');
            }
        }).catch(function (error) {
            console.log(error);
        });
    }
})



