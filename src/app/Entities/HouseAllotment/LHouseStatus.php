<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/23/20
 * Time: 3:50 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;

class LHouseStatus extends Model
{
    protected $table = 'l_house_status';
    protected $primaryKey = 'house_status_id';
}