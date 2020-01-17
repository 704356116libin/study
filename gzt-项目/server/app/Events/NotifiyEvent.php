<?php

namespace App\Events;

use App\Models\User;
use App\Notifications\Notifiy;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NotifiyEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
//    public $user;//通知的用户
    public $notifiy;//触发的通知
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Notifiy $notifiy)
    {
//        $this->user=$user;
        $this->notifiy=$notifiy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
