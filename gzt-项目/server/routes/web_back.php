<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/**
 * 请求测试
 */

Route::get('/ajax','Server\ViewController@ajax');
Route::view('/axios','axios');
Route::view('/ws','test.ws');
Route::view('/file','test.file');
Route::view('/swoole','swoole');

Route::view('/welcome','home');//网站首页
Route::view('/register','auth.register');
Route::view('/login','auth.login')->name('login');
Route::view('/useradmins{a}','admin')->where('a', '.*');

Route::post('/register','Server\UserController@register');//用户注册路由

/**
 * 缓存测试控制器
 */
Route::post('/cache', 'Server\CacheController@cache');
Route::get('/cache', 'Server\CacheController@cache');
Route::get('/connect', 'Server\CacheController@connect');
Route::any('/captcha','Server\CacheController@captcha');//图片验证码
Route::any('/check_captcha','Server\CacheController@checkCaptcha');

Route::post('/checkTelExsit','Server\UserController@checkTelExsit');//检查手机号是否存在
Route::post('/checkNameExsit','Server\UserController@checkNameExsit');//检查用户名是否存在
Route::post('/checkEmailExsit','Server\UserController@checkEmailExsit');//检查邮箱是否存在
/**
 * 登陆注册
 */
Route::post('/getApiToken','Server\TokenController@getAccessToken');//账户密码授权入口
Route::any('/resetPwdByTel','Server\UserController@userSetPwdByTel');//通过手机号重置密码
Route::any('/resetPwdByEmail','Server\UserController@userSetPwdByEmail');//通过邮箱重置密码
Route::get('/reset','Server\ViewController@reset' );//密码重置
Route::get('/email_reset/{token}', 'Server\ViewController@emailResetPwd')->name('email_reset');//返回通过邮箱重置密码视图

/**
 *用户验证
 */
Route::get('/email_verify/{token}','Server\EmailController@userEmailVerify' )->name('email_verify');//邮箱验证
Route::get('/email_unlink/{token}','Server\EmailController@userEmailUnlink' )->name('email_unlink');//邮箱解绑
Route::any('/send_email','Server\EmailController@sendEmail' );//邮件发送总入口
Route::get('/show_email_blad','Server\EmailController@showBlad');//邮件视图
Route::any('/tel_verify','Server\UserController@userTelVerify' );//手机激活验证
Route::any('/tel_unlink','Server\UserController@userTelUnlink' );//手机解绑

Route::get('/home', 'HomeController@index')->name('home');
/**
 * Json资源响应测试组
 */
Route::any('/json_user','Server\DemoController@jsonUser');
Route::any('/bbbb','Server\DemoController@bbbb');

/**
 * 路由测试组
 */
Route::any('/sortTest','Server\DemoController@sortTest');
Route::any('/file','Server\DemoController@file');
Route::any('/bianli','Server\DemoController@bianli');//
Route::any('/redis','Server\DemoController@redis');//redis缓存
Route::any('/oss','Server\DemoController@oss');//阿里oss
Route::any('/pst','Server\DemoController@pst');//阿里oss
Route::any('/mianshi','Server\DemoController@mianshi');//阿里oss
Route::any('/excel','Server\DemoController@excel');//excel
Route::any('/avator','Server\DemoController@avator');//excel
Route::any('/db','Server\DemoController@dbTest');//laravel数据操作测试
Route::any('/relation','Server\DemoController@relation');//excel
Route::any('/secret','Server\DemoController@secret');//加密解密测试
Route::any('/dynamic1','Server\DemoController@dynamic');//动态模块数据测试
Route::post('/api_test','Api\DingoController@api_test')->middleware('auth:api');//api接口测试
Route::post('/refreshToken','Server\TokenController@refreshToken');//token刷新
//权限管理
//Route::get('/canPermission','Server\PermissionController@canPermission');   //判断是否有权限
Route::get('/addPermission','Server\PermissionController@addPermission' );  //创建新的权限类型
//公司管理
Route::get('/createCompany','Server\CompanyController@createCompany');        //创建公司
Route::get('/switchCompany','Server\FirmController@switchCompany');           //切换公司时,修改用户guard_name字段
//管理后台系统--通讯录
Route::get('/departmentList','ServerAdmin\DepartmentController@departmentList');    //部门列表
Route::get('/departmentDetail','ServerAdmin\DepartmentController@departmentDetail');    //部门详情
Route::get('/createDepartment','ServerAdmin\DepartmentController@createDepartment');//添加部门
Route::get('/editDepartment','ServerAdmin\DepartmentController@editDepartment');    //编辑部门
Route::get('/getNodesUsers','ServerAdmin\DepartmentController@getNodesUsers');      //获取一个节点下所有员工的id
Route::get('/addStaff','ServerAdmin\DepartmentController@addStaff');   //给某个部门添加员工

// 企业邀请员工页面
Route::get('/invite-staff', 'Server\RedeemController@redeem');
Route::post('/setUser', 'Server\RedeemController@setUser');

Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');

Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');

Route::get('payment/{order}/wechat', 'PaymentController@payByWechat')->name('payment.wechat');
Route::post('payment/wechat/notify', 'PaymentController@wechatNotify')->name('payment.wechat.notify');

// 后台的路由不能与前端路由重复
// 匹配上面已有路由除外的所有,进入前端控制范围 404 在前端处理
Route::view('/{a}', 'app')->where('a', '.*');

