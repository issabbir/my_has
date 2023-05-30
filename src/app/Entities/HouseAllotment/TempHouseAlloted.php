<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/23/20
 * Time: 3:02 PM
 */

namespace App\Entities\HouseAllotment;

use App\Entities\Colony\Colony;
use Illuminate\Database\Eloquent\Model;

class TempHouseAlloted extends Model
{
    protected $table = 'TMP_HOUSE_ALLOTED';
    protected $primaryKey = 'id';
}
