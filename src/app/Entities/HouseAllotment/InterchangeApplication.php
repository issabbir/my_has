<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/9/20
 * Time: 12:08 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;

class InterchangeApplication extends Model
{
    protected $table = 'interchange_application';
    protected $primaryKey = 'int_change_id';

    public function first_allotment()
    {
        return $this->belongsTo(HouseAllotment::class, 'first_allot_id');
    }

    public function second_allotment()
    {
        return $this->belongsTo(HouseAllotment::class, 'second_allot_id');
    }
}