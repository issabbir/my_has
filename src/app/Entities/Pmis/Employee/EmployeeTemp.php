<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LDepartment;
use App\Entities\Admin\LDesignation;
use App\Entities\Admin\LDptDivision;
use App\Entities\Admin\LDptSection;
use App\Entities\Admin\LEmpGrade;
use App\Entities\Admin\LGender;
use App\Entities\Admin\LOtCategory;
use App\Entities\Admin\LReligion;
use App\Entities\Admin\LSalutation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use JamesDordoy\LaravelVueDatatable\Traits\LaravelVueDatatableTrait;

class EmployeeTemp extends Model
{
    protected $table = 'pmis.employee_temp';
    protected $primaryKey = 'emp_id';
    public $incrementing = false;
    protected $with = ['dptDivision','designation','department', 'salutation_id','emp_gender_id','emp_religion_id','ot_category_id', 'section', 'designation','grade'];

    protected $casts = [
        'emp_lpr_date'  => 'date:d-m-Y',
        'emp_join_date' => 'date:d-m-Y',
    ];

    public function dptDivision()
    {
        return $this->belongsTo(LDptDivision::class, 'dpt_division_id');
    }
    public function designation()
    {
        return $this->belongsTo(LDesignation::class, 'designation_id');
    }

    public function department()
    {
        return $this->belongsTo(LDepartment::class, 'department_id');
    }

    public function salutation_id()
    {
        return $this->belongsTo(LSalutation::class, 'salutation_id');
    }

    public function emp_gender_id()
    {
        return $this->belongsTo(LGender::class, 'emp_gender_id');
    }

    public function emp_religion_id()
    {
        return $this->belongsTo(LReligion::class, 'emp_religion_id');
    }
    public function ot_category_id()
    {
        return $this->belongsTo(LOtCategory::class, 'ot_category_id');
    }

    public function section()
    {
        return $this->belongsTo(LDptSection::class, 'section_id');
    }

    public function grade()
    {
        return $this->belongsTo(LEmpGrade::class, 'emp_grade_id');
    }
}
