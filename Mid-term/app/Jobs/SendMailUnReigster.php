<?php

namespace App\Jobs;

use App\Mail\ListUnregisterMail;
use App\Mail\MailSendInfoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class SendMailUnReigster implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $subjectUnReg;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $subjectUnReg)
    {
        $this->subjectUnReg = $subjectUnReg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->subjectUnReg['email'])->send(new ListUnregisterMail($this->subjectUnReg));

    }
}
