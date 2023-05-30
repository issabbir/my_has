<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/23/20
 * Time: 4:00 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;

class LHouseType extends Model
{
    protected $table = 'l_house_type';
    protected $primaryKey = 'house_type_id';
}