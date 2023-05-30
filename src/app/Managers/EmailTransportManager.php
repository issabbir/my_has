<?php

namespace App\Managers;

use App\Contracts\EmailTransportContract;
use App\Enums\EmailConfigs;


class EmailTransportManager implements EmailTransportContract
{
    /** @var \Swift_SmtpTransport */
    protected $transport;

    /**
     * EmailTransportManager constructor.
     */
    public function __construct()
    {
        $mailConfig = $this->getMailConfig();

        if( $mailConfig['smtp_mail_server'] == '' || $mailConfig['smtp_username'] == '' || $mailConfig['smtp_password'] == '' ) {
            throw new \Swift_TransportException('Email configuration is invalid');
        }

        $this->transport = (new \Swift_SmtpTransport($mailConfig['smtp_mail_server']))
            ->setUsername($mailConfig['smtp_username'])
            ->setPassword($mailConfig['smtp_password']);
    }

    /**
     * @param $configurations
     * @return array
     */
    private function getMailConfig()
    {
        $mailConfig = array();

        $mailConfig['smtp_username'] = EmailConfigs::SMTP_USER_NAME; // used in esheba existing application, check Application_Model_Sendmail class
        $mailConfig['smtp_password'] = EmailConfigs::SMTP_PASSWORD; // used in esheba existing application, check Application_Model_Sendmail class
        $mailConfig['smtp_emailadress'] = EmailConfigs::SMTP_EMAIL_ADDRESS; // used in sepcific mail classes, in e-sheba v2. So, didn't use here
        $mailConfig['smtp_mail_server'] = EmailConfigs::SMTP_MAIL_SERVER; // used in esheba existing application, check Application_Model_Sendmail class
        $mailConfig['smtp_port'] = EmailConfigs::SMTP_PORT; // not used in esheba existing application, check Application_Model_Sendmail class
        $mailConfig['smtp_ssl'] = EmailConfigs::SMTP_SSL; // not used in esheba existing application, check Application_Model_Sendmail class

        return $mailConfig;
    }

    /**
     * @return \Swift_SmtpTransport
     */
    public function getTransport()
    {
        return $this->transport;
    }
}