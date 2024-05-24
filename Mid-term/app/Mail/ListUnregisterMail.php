<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ListUnregisterMail extends Mailable
{
    use Queueable, SerializesModels;
    public $infoSubject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $infoSubject)
    {
        $this->infoSubject = $infoSubject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Cảnh báo đăng ký học phần')->view('emails.subject_unregister')->with(['infoSubject' => $this->infoSubject]);

    }
}
