<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailSendInfoUser extends Mailable
{
    use Queueable, SerializesModels;
    public $infoUser;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $infoUser)
    {
       $this->infoUser = $infoUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Thông tin tài khoản')->view('emails.info_user')->with(['infoUser' => $this->infoUser]);
    }
}
