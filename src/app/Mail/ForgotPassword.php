<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    /** @var $fromEmail */
    private $fromEmail;

    private $pin;

    use Queueable, SerializesModels;

    /**
     * Registration constructor.
     * @param $fromEmail
     * @param $mobile
     * @param $password
     */
    public function __construct($fromEmail, $pin)
    {
        $this->fromEmail = $fromEmail;
        $this->pin = $pin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fromName = 'CHITTAGONG PORT AUTHORITY';
        $subject = 'Reset Your Password!';

        return $this->from(
            [$this->fromEmail, $fromName]
        )->subject($subject)
            ->view('emails.forgot_password')
            ->with(['pin' => $this->pin]);
    }
}
