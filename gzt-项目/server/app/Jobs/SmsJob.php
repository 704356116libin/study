<?php

namespace App\Jobs;

use App\Tools\SmsTool;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tel,$data,$template)
    {
        //
        $this->tel = $tel;
        $this->data = $data;
        $this->template = $template;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        SmsTool::getSmsTool()->sendSms($this->tel,$this->data,$this->template);
    }
}
