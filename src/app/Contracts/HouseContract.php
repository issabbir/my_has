<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/11/20
 * Time: 5:44 PM
 */

namespace App\Contracts;


interface HouseContract
{
    public function findAvailableHouses($gradeId);
}