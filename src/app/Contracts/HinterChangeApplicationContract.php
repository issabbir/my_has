<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/9/20
 * Time: 12:56 PM
 */

namespace App\Contracts;


interface HinterChangeApplicationContract
{
    public function query();
    public function approveQuery();
    public function interChangeAllotmentLetterQuery();
}
