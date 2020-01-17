<?php

namespace App\Console\Commands;

use App\Tools\SwooleTool;
use App\Tools\TokenTool;
use App\Tools\WebSocketTool;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class WsSwoole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:action {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test swoole socket';
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
        $this->serv=new \swoole_websocket_server(config('web_socket.host'),config('web_socket.port'), SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
        if( config('app.env') === 'production' ) {
            $this->serv->set(
                array(
                    'ssl_cert_file' => '/etc/cert/pst.pingshentong.com/fullchain.pem',
                    'ssl_key_file' => '/etc/cert/pst.pingshentong.com/privkey.pem',
                )
            );
        }
//        $this->serv->set(
//            array(
//                'worker_num'=>1000,
//                'daemonize'=>1,
//                'log_file'=>'/swoole.log',
//                'max_request'=>10000,
//                'dispatch_mode'=>2,
//                'debug_mode'=>1
//            )
//        );
        $tool=WebSocketTool::getWebSocketTool();//回调类
//        $tool=SwooleTool::getSwooleTool();//回调类
        $this->serv->on('open',array($tool,'onOpen'));
//        $this->serv->on('Start',array($tool,'onStart'));
//        $this->serv->on('Connect',array($tool,'onConnect'));
        $this->serv->on('message',array($tool,'onMessage'));
//        $this->serv->on('signal',array($tool,'onSignal'));
        //$this->serv->on('Receive',array($handler,'onReceive'));
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
