<?php

namespace App\Console\Commands;

use App\Tools\TokenTool;
use Illuminate\Console\Command;

class AccessTokenRevoke extends Command
{
    /**
     * 自定义命令的名称
     */
    protected $signature = 'accessToken:revoke';
    /**
     *自定义命令的描述
     */
    protected $description = 'Access_token令牌超时废除命令';
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
     * 命令的执行逻辑
     */
    public function handle()
    {
        TokenTool::getTokenTool()->revokeToken();
    }
}
