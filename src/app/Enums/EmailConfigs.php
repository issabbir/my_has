<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 3/29/20
 * Time: 1:51 PM
 */

namespace App\Enums;


class EmailConfigs
{
    public const FROM_EMAIL = 'no-reply@cpa.gov.bd';
    public const SMTP_USER_NAME = 'smtp_username'; // used in esheba existing application, check Application_Model_Sendmail class
    public const SMTP_PASSWORD = 'smtp_password'; // used in esheba existing application, check Application_Model_Sendmail class
    public const SMTP_EMAIL_ADDRESS = 'smtp_emailadress'; // used in sepcific mail classes, in e-sheba v2. So, didn't use here
    public const SMTP_MAIL_SERVER = 'smtp_mail_server'; // used in esheba existing application, check Application_Model_Sendmail class
    public const SMTP_PORT = 'smtp_port'; // not used in esheba existing application, check Application_Model_Sendmail class
    public const SMTP_SSL = 'smtp_ssl'; // not used in esheba existing application, check Application_Model_Sendmail class
}