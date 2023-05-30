<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 3/29/20
 * Time: 1:28 PM
 */

namespace App\Contracts;

use App\Mail\ForgotPassword;

interface ForgotPasswordEmailContract
{
    public function send($toEmail, $information);
    public function prepare($information) : ForgotPassword;
}