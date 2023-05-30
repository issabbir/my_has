<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/3/20
 * Time: 2:13 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;

class AllotPoint extends Model
{
    protected $table = 'allot_point';
    protected $primaryKey = 'allot_point_id';
}