<?php

namespace App\Jobs;

use App\Mail\MailSendInfoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
class SendInfoUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected  $infoUser;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $infoUser)
    {
        $this->infoUser = $infoUser;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->infoUser['email'])->send(new MailSendInfoUser($this->infoUser));
    }
}
