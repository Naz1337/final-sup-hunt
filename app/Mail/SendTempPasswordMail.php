<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTempPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tempPassword;

    public function __construct($user, $tempPassword)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    public function build()
    {
        return $this->subject('Your Temporary Password')
                    ->view('emails.temp-password');
    }
}