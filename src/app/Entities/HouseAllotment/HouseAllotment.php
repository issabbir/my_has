<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 1:53 PM
 */

namespace App\Entities\HouseAllotment;

use Illuminate\Database\Eloquent\Model;
use App\Entities\Pmis\Employee\Employee;

class HouseAllotment extends Model
{
    protected $table = 'house_allottment';
    protected $primaryKey = 'allot_id';

    protected $casts = [
        'application_date' => 'date:d-m-Y',
        'approval_date' => 'date:d-m-Y'
    ];

    public static function find($allotmentId)
    {
    }

    public function advertisement()
    {
        return $this->belongsTo(HaAdvMst::class, 'adv_id', 'house_allottment.advertisement_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function house()
    {
        return $this->belongsTo(HouseList::class, 'house_id');
    }

}
