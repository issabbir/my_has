<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/29/20
 * Time: 2:28 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;

class HaAppEmpFamily extends Model
{
    protected $table = 'ha_app_emp_family';
    protected $primaryKey = 'app_family_id';

    public function ha_application()
    {
        return $this->belongsTo(HaApplication::class, 'application_id');
    }
}