<?php

namespace App\Listeners;

use App\Events\NotifiyEvent;
use App\Jobs\NotifiyJob;
use App\Notifications\Notifiy;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifiyEventListener
{
    public function __construct()
    {
        //
    }

    /**
     * 处理站内通知事件
     * @param  NotificationEvent  $event
     * @return void
     */
    public function handle(NotifiyEvent $event)
    {
        dispatch(new NotifiyJob($event->notifiy->user,$event->notifiy))->onQueue('notify');
    }
}
