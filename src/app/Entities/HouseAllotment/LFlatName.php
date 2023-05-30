<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/23/20
 * Time: 4:00 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;

class LFlatName extends Model
{
    protected $table = 'l_flat_name';
    protected $primaryKey = 'flat_name_id';
}
