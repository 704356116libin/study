<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\Notifiy;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NotifiyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    public $notification;

    public function __construct($user,Notifiy $notification)
    {
        $this->user=$user;
        $this->notification=$notification;
    }

    /**
     *  任务执行的主逻辑
     * @return void
     */
    public function handle()
    {
        $this->user->notify($this->notification);//通知指定用户
    }
    public function failed(\Exception $exception)
    {
        //任务失败的逻辑
        $this->delete();
    }
}
