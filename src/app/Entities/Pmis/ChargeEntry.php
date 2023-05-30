<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LChargeType;
use App\Entities\Admin\LDepartment;
use App\Entities\Admin\LEmpGrade;
use App\Entities\Pmis\Employee\Employee;
use Illuminate\Database\Eloquent\Model;
use App\Entities\Admin\LDesignation;
use App\Entities\Admin\LDptDivision;
use App\Entities\Admin\LDptSection;
use App\Entities\Admin\LEmpType;

class ChargeEntry extends Model
{
    protected $table = 'charge_entry';
    protected $primaryKey = 'c_order_no';
    protected $with = ['charge'];
    //protected $with = ['dptDivision','department', 'section', 'designation','empGrade'];

    //protected $appends = ['text', 'value'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function charge()
    {
        return $this->belongsTo(LChargeType::class, 'charge_type_id');
    }

    public function dptDivision()
    {
        return $this->belongsTo(LDptDivision::class, 'dpt_division_id');
    }

    public function department()
    {
        return $this->belongsTo(LDepartment::class, 'dpt_department_id');
    }


    public function section()
    {
        return $this->belongsTo(LDptSection::class, 'section_id');
    }

    public function designation()
    {
        return $this->belongsTo(LDesignation::class, 'designation_id');
    }

    public function empGrade()
    {
        return $this->belongsTo(LEmpGrade::class, 'emp_grade_id');
    }



}
