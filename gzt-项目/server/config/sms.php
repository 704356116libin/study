<?php
/**
 * 阿里云--短信配置文件
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/9/18
 * Time: 16:03
 */
return [
    'timeout' => 5.0,// HTTP 请求的超时时间（秒）
    'default' => [// 默认发送配置
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,// 网关调用策略，默认：顺序调用
        'gateways' => [
            'aliyun',
            /* 'alidayu',
             'yuntongxun'*/
        ],
    ],
    'gateways' => [// 可用的网关配置
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ],
        'aliyun' => [
            'access_key_id'     =>  env('ALIYUN_ACCESS_KEY_ID'),
            'access_key_secret' =>  env('ALIYUN_ACCESS_KEY_SECRET'),
            'sign_name'         => '阿里云短信测试专用',
        ],
    ],
    'templete'=>[

    ],//模板库名
];