<?php

namespace App\Console\Commands;

use App\Tools\SwooleTool;
use App\Tools\TokenTool;
use App\Tools\WebSocketTool;
use App\Tools\WebSocketTool2;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 聊天室启动服务命令
 * Class WsSwoole
 * @package App\Console\Commands
 */
class SwooleChat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:chat {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'swoole chat demo';
    private $serv=null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fire();
    }

    public function fire(){
        $arg=$this->argument('action');
        switch($arg){
            case 'start':
                $this->info('swoole observer started');
                $this->start();
                break;
            case 'stop':
                $this->info('stoped');
                $this->stop();
                break;
            case 'restart':
                $this->info('restarted');
                break;
        }
    }

    public function start(){
        $this->serv=new \swoole_websocket_server(config('web_socket.chat_host'),config('web_socket.chat_port'));
        $tool=WebSocketTool2::getWebSocketTool();//回调类
        $this->serv->on('open',array($tool,'onOpen'));
        $this->serv->on('message',array($tool,'onMessage'));
        $this->serv->on('close',array($tool,'onClose'));
        $this->serv->start();
    }
    public function stop(){
        if(!is_null($this->serv)){
            $this->serv->stop(-1,false);//停止服务;
        }else{
            echo 'Ws服务未启动';
        }

    }
    protected function getArguments(){
        return array(
            'action',InputArgument::REQUIRED,'start|stop|restart'
        );
    }
}
