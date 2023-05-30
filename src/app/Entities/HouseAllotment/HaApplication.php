<?php

namespace App\Entities\HouseAllotment;

use App\Entities\Pmis\Employee\Employee;
use Illuminate\Database\Eloquent\Model;

class HaApplication extends Model
{
    protected $table = 'ha_application';
    protected $primaryKey = 'application_id';
    protected $with = ['ha_app_emp_families'];
    public $timestamps = false;

    protected $casts = [
        'application_date' => 'date:d-m-Y',
    ];

    public function advertisement()
    {
        return $this->belongsTo(HaAdvMst::class, 'advertisement_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function house_type()
    {
        return $this->belongsTo(LHouseType::class, 'applied_house_type_id');
    }

    public function ha_app_emp_families()
    {
        return $this->hasMany(HaAppEmpFamily::class, 'application_id');
    }

    public function allot_point()
    {
        return $this->hasOne(AllotPoint::class, 'application_id');
    }

    public function houseallotment()
    {
        return $this->hasOne(HouseAllotment::class, 'application_id');
    }
}
