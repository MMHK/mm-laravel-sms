<?php

namespace MMHK\SMS\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use MMHK\SMS\Contracts\MessageInterface;
use MMHK\SMS\SMSSentEvent;

class SMSSendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var MessageInterface
     */
    protected $msg;

    protected $params;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(MessageInterface $message, $params = [])
    {
        $this->msg = $message;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $result = app('sms')->sendSync($this->msg);

        event(new SMSSentEvent($result[0], $result[1], $result[2],$this->params));
    }
}
