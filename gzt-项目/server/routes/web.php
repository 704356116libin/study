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
//if (config('app.env') === 'production') {
//    //前台路由组
//    Route::group(['domain' => 'www.pingshentong.com'], function () {
//        //    include base_path() . '/routes/www.php';
//        include __DIR__ . '/www.php';
//    });
//    //前台路由组
//    Route::group(['domain' => 'pingshentong.com'], function () {
//        //    include base_path() . '/routes/www.php';
//        include __DIR__ . '/www.php';
//    });
//    //子域名设置为
//    Route::group(['domain' => 'pst.pingshentong.com'], function () {
//        include __DIR__ . '/pst.php';
//    });
//} else {
//    Route::group(['domain' => config('app.url')], function () {
//        include __DIR__ . '/www.php';
//    });
    //子域名设置为
//    Route::group(['domain' => 'pst.' . config('app.url')], function () {
        include __DIR__ . '/pst.php';
//    });
//}

// 后台的路由不能与前端路由重复
// 匹配上面已有路由除外的所有,进入前端控制范围 404 在前端处理
// Route::view('/{a}', 'app')->where('a', '.*');
