<?php
/**
 * Created by PhpStorm.
 * User: mayn
 * Date: 2018/8/21
 * Time: 14:14
 */

namespace App\Tools;
use App\Models\User;

/**
 * 短信工具类
 */
class SwooleTool
{
    static private $swooleTool;
    private $table;
    private $config;
    /**
     *私有构造函数防止new
     */
    private function __construct()
    {
        $this->table=  new \swoole_table(1024);
        $this->table->column('fd', \swoole_table::TYPE_INT);
        $this->table->column('name', \swoole_table::TYPE_STRING, 255);
        $this->table->column('avatar', \swoole_table::TYPE_STRING, 255);
        $this->table->create();
        $this->config=config('swoole_demo');
    }
    /**
     * 单例模式
     */
    static public function getSwooleTool(){
        if(self::$swooleTool instanceof self)
        {
            return self::$swooleTool;
        }else{
            return self::$swooleTool = new self;
        }
    }
    /**
     * 防止被克隆
     */
    private function _clone(){

    }
    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {
        $user = [
            'fd' => $request->fd,
            'name' => $this->config['name'][array_rand($this->config['name'])] . $request->fd,
            'avatar' => $this->config['avatar'][array_rand($this->config['avatar'])]
        ];
        // 放入内存表
        $this->table->set($request->fd, $user);
        $users = [];
        foreach (  $this->table as $row) {
            $users[] = $row;
        }
        var_dump($users);
        $server->push($request->fd, json_encode(['user' => $user,'all' => $users,'type' => 'openSuccess']));
    }
    public function onClose(\swoole_websocket_server $server, int $fd)
    {
        $user = $this->table->get($fd);

        $message="{$user['name']}离开聊天室";
        $type='close';
        $fd=$fd;
        var_dump('连接中断:---fd:'.$fd);
        $message = htmlspecialchars($message);
        $datetime = date('Y-m-d H:i:s', time());
        $user = $this->table->get($fd);

        foreach ($this->table as $item) {
            // 自己不用发送
            if ($item['fd'] == $fd) {
                continue;
            }
            $server->push($item['fd'], json_encode([
                'type' => $type,
                'message' => $message,
                'datetime' => $datetime,
                'user' => $user
            ]));
        }
        $this->table->del($fd);//删除链接信息
    }
    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        $message=$frame->data;
        $id=$message;
//        var_dump(User::find(settype($id,'int')));
        $type='message';
        $fd=$frame->fd;

        $message = htmlspecialchars($message);
        $datetime = date('Y-m-d H:i:s', time());
        $user = $this->table->get($fd);

        foreach ($this->table as $item) {
            // 自己不用发送
            if ($item['fd'] == $fd) {
                continue;
            }
            $server->push($item['fd'], json_encode([
                'type' => $type,
                'message' => $message,
                'datetime' => $datetime,
                'user' => $user
            ]));
        }
    }
    public function onStart(\swoole_websocket_server $server,int $fd)
    {
        Log::info('swoole start');
    }
    public function onConnect($serv, $fd, $from_id)
    {
        Log::info('swoole connect' . $from_id);
    }
}