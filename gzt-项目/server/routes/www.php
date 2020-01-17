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

Route::view('/','home');//网站首页
Route::view('/buy','buy');//

// Route::get('/home', 'HomeController@index')->name('home');

// 后台的路由不能与前端路由重复
// 匹配上面已有路由除外的所有,进入前端控制范围 404 在前端处理
// Route::view('/{a}', 'app')->where('a', '.*');

