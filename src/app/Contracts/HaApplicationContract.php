<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 3:44 PM
 */

namespace App\Contracts;


interface HaApplicationContract
{
    public function findBy($advertisementId, $houseTypeId);
}