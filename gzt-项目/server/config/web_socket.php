<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/12/21
 * Time: 16:59
*/
return [
    /*
     * swoole服务器启动的地址&端口
     */
    'host' => '0.0.0.0',
    'port' => 9501,
    /*
     *swoole演示聊天室启动的地址&端口
     */
    'chat_host'=>'0.0.0.0',
    'chat_port'=>9502,
    'redis' => [
        'u_info'=>'u_info',//存放用户通道绑定信息的hash表名
        'uid_fd'=>'uid_fd',//通道id与user_id关联表名
    ],

    'avatar' => [
        '/images/avatar/1.jpg',
        '/images/avatar/2.jpg',
        '/images/avatar/cat.jpg',
        '/images/avatar/people.jpg'
    ],
];