<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/16/20
 * Time: 1:50 PM
 */

namespace App\Contracts;


interface InterchangeTakeoverContract
{
    public function findBy($allotmentNo);
}