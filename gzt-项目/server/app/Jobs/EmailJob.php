<?php

namespace App\Jobs;

use App\Tools\EmailTool;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user,$type,$emailData)
    {
        //$user,$type,$emailData
        $this->user = $user;
        $this->type = $type;
        $this->emailData = $emailData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //发送邮件通知
        EmailTool::getEmailTool()->sendEmail($this->user,$this->type,$this->emailData);

    }
}
