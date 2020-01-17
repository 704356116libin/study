<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

/**
 * swoole聊天室演示服务工具类
 */
class WebSocketTool2
{
    static private $webSocketTool;
    private $table;
    private $config;
    private $userTool;//用户工具类
    private $redis_uname;//redis中存放user信息的hash表明
    private $redis_fd_uid;//fd关联user_id  hash表明
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->config=config('web_socket');
        $this->userTool=UserTool::getUserTool();
        $this->redis_uname=$this->config['redis']['u_info'];
        $this->redis_fd_uid=$this->config['redis']['fd_uid'];
    }
    /**
     * 单例模式
     */
    static public function getWebSocketTool(){
        if(self::$webSocketTool instanceof self)
        {
            return self::$webSocketTool;
        }else{
            return self::$webSocketTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    /**
     * 监听用户连接事件
     * @param \swoole_websocket_server $server
     * @param \swoole_http_request $request
     */
    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        dump($request,111);
        $user=$this->userTool->getRandomUser();
        $r_data = [];//需要压入or取出的redis的数据
        //放入redis缓存中
        $fd=$request->fd;
        if(! Redis::hexists ($this->redis_uname,$user->id)){
            $r_data=[
                'fd' => $fd,
                'user_id' => $user->id,
                'name' => $user->name,
                'avatar' => $this->config['avatar'][array_rand($this->config['avatar'])]
            ];
        }else{
            $r_data=json_decode(Redis::hget($this->redis_uname,$user->id),true);
            $r_data['fd']=$fd;
        }
        Redis::hset($this->redis_uname,$user->id,json_encode($r_data));//压入user信息
        Redis::hset($this->redis_fd_uid,$fd,$user->id);//压入通道fd关联user_id信息
        //对需要通知(在线状态)的用户进行实时通知---现在只是返回当前聊天室在线的人员
        $users=[];
        $users[]=$r_data;//首先压入当前通道的用户
        $datas = Redis::hgetall($this->redis_uname);//redis缓存hash表中连接人数据
        dump($datas,$user->id);
        foreach (  $datas as $key=>$v){
            $v=json_decode($v,true);
            //不需要对自己发送上线通知,移除相同的通道缓存
            if($v['fd']==$fd&&$v['user_id']!=$user->id){
                //清除没用的缓存
                Redis::hdel($this->redis_uname,$v['user_id']);
                Redis::hdel($this->redis_fd_uid,$v['fd']);
                dump("清除{$v['user_id']}");
                continue;
            }
            elseif ($v['fd']!=$fd&&$v['user_id']==$user->id){
                //保留当前通道
                Redis::hdel($this->redis_uname,$v['user_id']);
                Redis::hdel($this->redis_fd_uid,$v['fd']);
                dump("关闭{$user->id}");
                $server->disconnect($v['fd'],1001,"{$user->name}已在别处连接,此通道关闭");
                continue;
            }
            elseif ($v['fd']==$fd&&$v['user_id']==$user->id){
                //不给自己推送上线通知
                dump("跳过{$user->id}");
                continue;
            }
            try {
                $server->push($v['fd'], json_encode(['user' => $r_data, 'message' => "{$user->name}.上线了!", 'type' => 'open']));//通知在线人员页面更新
                dump("用户:{$v['user_id']}-通道:{$v['fd']}--推送{$user->id}上线成功");
                $users[]=$v;
            } catch (\Exception $e) {
                //删除redis缓存信息
                Redis::hdel($this->redis_uname,$v['user_id']);//删除用户信息
                Redis::hdel($this->redis_fd_uid,$v['fd']);//删除fd与user_id关联信息
            }
        }
        dump(Redis::hgetall($this->redis_uname),$users);
        $server->push($fd, json_encode(['user' => $r_data,'all' => $users,'type' => 'openSuccess']));
    }
    /**
     * 监听用户连接关闭状态
     * @param \swoole_websocket_server $server
     * @param int $fd
     */
    public function onClose(\swoole_websocket_server $server, int $fd)
    {
        $fd=$fd;//通道id
        $u_key=Redis::hget($this->redis_fd_uid,$fd);
        $user = json_decode(Redis::hget($this->redis_uname,$u_key),true);
        $message="{$user['name']}离开聊天室";
        $type='close';
        $message = htmlspecialchars($message);
        $datetime = date('Y-m-d H:i:s', time());
        dump($u_key,$user,$message);
        $datas = Redis::hgetall($this->redis_uname);//redis缓存hash表中连接人数据
        foreach (  $datas as $key=>$v){
            $v=json_decode($v,true);
            // 自己不用发送
            if ($v['user_id'] == $user['user_id']) {
                continue;
            }
            $server->push($v['fd'], json_encode([
                'type' => $type,
                'message' => $message,
                'datetime' => $datetime,
                'user' => $user
            ]));
        }
        //删除redis缓存信息
        Redis::hdel($this->redis_uname,$user['user_id']);//删除用户信息
        Redis::hdel($this->redis_fd_uid,$fd);//删除fd与user_id关联信息

    }
    /**
     * 监听客户端向服务端推送数据
     * @param \swoole_websocket_server $server
     * @param \swoole_websocket_frame $frame
     */
    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {

        $message=$frame->data;
        $id=$message;
        $type='message';
        $fd=$frame->fd;//用户连接的通道id
        $u_key=Redis::hget($this->redis_fd_uid,$fd);
        $user = json_decode(Redis::hget($this->redis_uname,$u_key),true);

        $message = htmlspecialchars($message);
        $datetime = date('Y-m-d H:i:s', time());

        $datas = Redis::hgetall($this->redis_uname);//redis缓存hash表中连接人数据
        var_dump($datas);
        foreach (  $datas as $key=>$v){
            $v=json_decode($v,true);
            // 自己不用发送
            if ($v['user_id'] == $user['user_id']) {
                continue;
            }
            $server->push($v['fd'], json_encode([
                'type' => $type,
                'message' => $message,
                'datetime' => $datetime,
                'user' => $user
            ]));
        }
    }
    public function onStart($serv)
    {
        Log::info('WsServer start');
    }
    public function onConnect($serv, $fd, $from_id)
    {
        Log::info('WsServer connect' . $from_id);
    }
}