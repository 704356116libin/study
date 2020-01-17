<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DatabaseChannel
{
    public function send($notifiable,Notification $notification){
        $message=$notification->Notify($notifiable);
    }
}
