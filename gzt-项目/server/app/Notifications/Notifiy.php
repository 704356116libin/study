<?php

namespace App\Notifications;

use App\Broadcasting\DatabaseChannel;
use App\Models\User;
use App\Repositories\NotifyRepository;
use App\Tools\EmailTool;
use App\Tools\SmsTool;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

/**
 * 站内通知主类
 * Class InstationNotifiy
 * @package App\Notifications
 */
class Notifiy extends Notification
{
    use Queueable;
    public $user;
    private $data;
    private $notifyRepository;//站内通知仓库
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $data)
    {
        $this->user = $user;
        $this->data = $data;
        $this->notifyRepository =NotifyRepository::getNotifyRepository();
        $this->Notify();
    }

    /**
     * 定义通知的方式
     */
    public function via($notifiable)
    {
        return [DatabaseChannel::class];
    }
    /**
     * 自定义站内信通知(向通知表插入一条记录)
     */
    public function Notify()
    {
      $this->notifyRepository->addNofitication(array_merge(['user_id'=>$this->user->id],$this->data));
    }
}