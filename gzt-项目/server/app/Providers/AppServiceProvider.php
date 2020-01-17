<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Yansongda\Pay\Pay;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh-CN');
        Validator::extend('tel_code',function ($attribute,$v,$p){
            return md5($v)==session('tel_code');//短信验证码验证
        });
        Validator::extend('telnum',function ($attribute,$v,$p){
            return md5($v)==session('tel_num');//手机号验证
        });
        Resource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function () {
            $config = config('pay.alipay');
            $config['notify_url'] = 'https://pst.pingshentong.com/payment/alipay/notify';
            $config['return_url'] = 'https://pst.pingshentong.com/payment/alipay/return';
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                $config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] = 'https://pst.pingshentong.com/payment/wechat/notify';
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });
    }
}
