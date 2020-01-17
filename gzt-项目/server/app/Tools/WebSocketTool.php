<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Events\NotifiyEvent;
use App\Models\CompanyNotice;
use App\Models\User;
use App\Notifications\Notifiy;
use Illuminate\Support\Facades\Redis;

/**
 * 短信工具类
 */
class WebSocketTool
{
    static private $webSocketTool;
    private $table;
    private $config;
    private $userTool;//用户工具类
    private $notifyTool;//通知工具类
    private $redis_u_info;//redis中存放user信息的hash表明
    private $redis_uid_fd;//user_id 关联fd hash表名
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->config=config('web_socket');
        $this->userTool=UserTool::getUserTool();
        $this->notifyTool=NotifyTool::getNotifyTool();
        $this->redis_u_info=$this->config['redis']['u_info'];
        $this->redis_uid_fd=$this->config['redis']['uid_fd'];
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
        if(!array_key_exists('client_type',$request->header)){//判断是否从业务逻辑推送
            $user=User::find(FunctionTool::decrypt_id(($request->get)['user_id']));
            $r_data = [];//需要压入or取出的redis的数据
            //放入redis缓存中
            $fd=$request->fd;
            $r_data=[
                'fd' => $fd,
                'user_id' => $user->id,
                'name' => $user->name,
            ];
            WsSendMessageTool::bindFdUser($this->redis_u_info,$this->redis_uid_fd,$r_data,$fd,$user->id);//绑定用户信息
            dump('ws服务器用户绑定');
            dump(Redis::hgetall($this->redis_u_info),Redis::hgetall($this->redis_uid_fd));

        }
    }
    /**
     * 监听用户连接关闭状态
     * @param \swoole_websocket_server $server
     * @param int $fd
     */
    public function onClose(\swoole_websocket_server $server, int $fd)
    {
        dump('连接断开逻辑');
        WsSendMessageTool::removeFdUser($this->redis_u_info,$this->redis_uid_fd,$fd);
    }
    /**
     * 监听客户端向服务端推送数据
     * @param \swoole_websocket_server $server
     * @param \swoole_websocket_frame $frame
     */
    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        dump('ws服务器处理消息推送');
        dump(Redis::hgetall($this->redis_u_info),Redis::hgetall($this->redis_uid_fd));
        $data=json_decode($frame->data,true);
        //用户信息数组
        $user = json_decode(Redis::hget($this->redis_u_info,$frame->fd),true);
        if(!is_null($data)){
            //推送数据的方式--相应的标识进行相应的处理
            switch ($data['notify_way']){
                case config('notify.notify_way.active')://业务客户端主动推送
                    WsSendMessageTool::sendNotifyMessage($server, $data, $user,$this->redis_u_info,$this->redis_uid_fd);//推送数据
                    break;
                case config('notify.notify_way.dynamic.refresh') ://工作动态列表数据刷新
                    WsSendMessageTool::sendDynamicMessage($server,$data,$this->redis_u_info,$this->redis_uid_fd,
                        config('notify.notify_way.dynamic.dynamic_refresh'));
                    break;
                case config('notify.notify_way.company.current_company_alter') ://用户当前企业变更标识
                    WsSendMessageTool::sendDynamicMessage($server,$data,$this->redis_u_info,$this->redis_uid_fd,
                        config('notify.notify_way.company.current_company_alter'));
                    break;
                default ://前端ws交互

                    break;
            }
        }
    }
}