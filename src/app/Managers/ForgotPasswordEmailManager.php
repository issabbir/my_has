<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 3/29/20
 * Time: 1:27 PM
 */

namespace App\Managers;


use App\Contracts\EmailTransportContract;
use App\Contracts\ForgotPasswordEmailContract;
use App\Enums\EmailConfigs;
use App\Mail\ForgotPassword;
use App\Managers\EmailTransportManager;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordEmailManager implements ForgotPasswordEmailContract
{
    /** @var EmailTransportManager */
    private $emailTransportManager;

    /**
     * EmailManager constructor.
     * @param EmailTransportContract $emailTransportManager
     */
    public function __construct(EmailTransportContract $emailTransportManager)
    {
        $this->emailTransportManager = $emailTransportManager;

        // FIXME: WE ARE USING SENDMAIL INSTEAD OF SMTP. SO, KEEPING IT OUT! WHEN WE WILL USE SMTP, WE MAY ENABLE IT.
        // Mail::setSwiftMailer(new \Swift_Mailer($this->emailTransportManager->getTransport()));
    }

    public function send($toEmail, $pin)
    {
        Mail::to($toEmail)->send(
            $this->prepare($pin)
        );

        return Mail::failures() ? false : true;
    }

    public function prepare($pin) : ForgotPassword
    {
        return new ForgotPassword(EmailConfigs::FROM_EMAIL, $pin);
    }
}