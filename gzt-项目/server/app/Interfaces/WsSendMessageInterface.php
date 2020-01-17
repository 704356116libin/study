<?php
/**
 * Created by PhpStorm.
 * User: bin
 * Date: 2018/11/14
 * Time: 14:01
 */

namespace App\Interfaces;

/**
 * Ws服务器推送数据的接口
 * Interface ValidateInterface
 * @package App\Interfaces
 */
interface WsSendMessageInterface
{
    public static function bindFdUser($redis_u_info,$redis_uid_fd,array $data,int $fd,int $user_id);//ws服务借助redis与用户信息绑定
    public static function removeFdUser($redis_u_info,$redis_uid_fd,$fd);//ws服务通道用户信息移除
    public static function sendNotifyMessage(\swoole_websocket_server $server, $data, $user,$redis_uname,$redis_uid_fd);//通知模块消息推送
}