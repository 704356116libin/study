<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Events\NotifiyEvent;
use App\Interfaces\WsSendMessageInterface;
use App\Models\CompanyNotice;
use App\Models\User;
use App\Notifications\Notifiy;
use Illuminate\Support\Facades\Redis;

/**
 * Ws服务器推送数据的工具类
 */
class WsSendMessageTool implements WsSendMessageInterface
{
    /**
     * 通知模块进行消息推送
     * @param \swoole_websocket_server $server:ws服务器
     * @param $data:总数据
     * @param $user:
     * @param $redis_uname:redis中存储用户信息的hash表
     * @param $redis_fd_uid:redis中存储fd和user_id的hash表
     */
    public static function sendNotifyMessage(\swoole_websocket_server $server, $data, $user,$redis_uname,$redis_uid_fd)
    {
        $user_ids = $data['user_ids'];
        $datas=Redis::hgetall($redis_uname);//拿到缓存中所有的用户信息
        foreach ($datas as $key=>$u_data){
            $u_data=json_decode($u_data,true);
            foreach ($user_ids as $id) {
                $fds=json_decode(Redis::hget($redis_uid_fd,$id),true);
                if ($u_data['user_id']==$id&&in_array($u_data['fd'],$fds)){//确认fd与user_id确实绑定
                    //向ws服务器推送单条数据--需不需要再次封装数据待续。。。。
                    if ($server->exist($u_data['fd'])) {
                        $server->push($u_data['fd'], json_encode(['type' => config('notify.notify_way.dynamic.dynamic_single'), 'data' => $data['single_data']]));
                        dump($u_data['fd'] . ':推送通知成功(实时)', $data['single_data']);
                    }
                };
            }
        }
    }
    /**
     * 动态模块推送相应标识公共方法
     * @param \swoole_websocket_server $server:ws服务器
     * @param $data:总数据
     * @param $redis_uname:redis中存储用户信息的hash表
     * @param $redis_fd_uid:redis中存储fd和user_id的hash表
     * @param $notify_way:推送标识
     */
    public static function sendDynamicMessage(\swoole_websocket_server $server, $data,$redis_uname,$redis_uid_fd,$notify_way)
    {
        $user_id=$data['user_id'];
        $fds=json_decode(Redis::hget($redis_uid_fd,$user_id),true);
        dump($fds);
        if (!is_null($fds)) {
            foreach ($fds as $fd) {
                $u_data = json_decode(Redis::hget($redis_uname, $fd), true);
                if ($u_data['fd'] == $fd && $u_data['user_id'] == $user_id) {//确认fd与user_id确实绑定
                    //向ws服务器推送单条数据--需不需要再次封装数据待续。。。。
                    if ($server->exist($u_data['fd'])) {
                        //'data' 预留数据组后续可利用
                        $server->push($u_data['fd'], json_encode(['type' => $notify_way, 'data' => []]));
                        dump('user:' . $u_data['user_id'] . '通道:' . $u_data['fd'] . '推送'.$notify_way);
                    }
                };
            }
        }
    }
    /**
     * redis绑定用户信息
     * @param $redis_u_info:redis中存放fd-u_info的hash表名
     * @param $redis_uid_fd:存放user_id绑定的fd数组的hash表明
     * @param array $data:用户信息数组
     * @param int $fd:当前通道
     * @param int $user_id:用户id
     */
    public static function bindFdUser($redis_u_info,$redis_uid_fd,array $data,int $fd,int $user_id)
    {
        Redis::hset($redis_u_info,$fd ,json_encode($data));//压入通道绑定user信息
        //拿到user_id所关联的通道array
        $fds=json_decode(Redis::hget($redis_uid_fd,$user_id),true);
        $fds[]=$fd;
        $fds=array_unique($fds);
        Redis::hset($redis_uid_fd,$user_id ,json_encode($fds));//压入通道fd关联user_id信息
    }
    /**
     * 移除通道綁定的信息
     * @param $redis_u_info:fd通道绑定的user信息
     * @param $redis_uid_fd:相应的user_id绑定的fd
     * @param $fd:目标通道
     */
    public static function removeFdUser($redis_u_info,$redis_uid_fd,$fd)
    {
        if (!is_null(Redis::hget($redis_u_info,$fd))) {
            $user_id=json_decode(Redis::hget($redis_u_info,$fd),true)['user_id'];
            Redis::hdel($redis_u_info,$fd);//删除当前通道绑定的用户信息
            $fds=json_decode(Redis::hget($redis_uid_fd,$user_id),true);//拿到通道数组
            $index=array_search($fd,$fds);
            unset($fds[$index]);
            $fds= array_values($fds);
            if(count($fds)!=0){
                Redis::hset($redis_uid_fd,$user_id ,json_encode($fds));//压入通道fd关联user_id信息
            }else{
                Redis::hdel($redis_uid_fd,$user_id );//移除uid绑定的通道信息
            }

        }
    }
}