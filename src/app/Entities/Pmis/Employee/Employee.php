<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LDepartment;
use Illuminate\Database\Eloquent\Model;
use App\Entities\Admin\LBank;
use App\Entities\Admin\LBloodGroup;
use App\Entities\Admin\LBranch;
use App\Entities\Admin\LDesignation;
use App\Entities\Admin\LDptDivision;
use App\Entities\Admin\LDptSection;
use App\Entities\Admin\LEmpGrade;
use App\Entities\Admin\LEmpStatus;
use App\Entities\Admin\LEmpType;
use App\Entities\Admin\LGender;
use App\Entities\Admin\LGradeSteps;
use App\Entities\Admin\LLocation;
use App\Entities\Admin\LMaritalStatus;
use App\Entities\Admin\LNationality;
use App\Entities\Admin\LPostType;
use App\Entities\Admin\LQuota;
use App\Entities\Admin\LReligion;
use App\Entities\Admin\LSalutation;

class Employee extends Model
{
    protected $table = 'pmis.employee';
    protected $primaryKey = 'emp_id';
    public $incrementing = false;

    /*protected $with = ['salutation', 'gender', 'religion', 'bloodGroup', 'nationality', 'quota', 'empStatus',
        'empGrade', 'dptDivision', 'department', 'section', 'designation', 'type', 'maritalStatus',
        'postType', 'gradeStep', 'location', 'bank', 'branch'];*/
    protected $with = ['dptDivision','department', 'section', 'designation','grade'];
    /** NOTE: Some columns suffixed with *_ids. But no lookup table for those. ie. emp_security_id.
     So, those relationships are not defined and there is no entry in $with property. */

    protected $appends = ['text', 'value', 'option_name'];

    protected function getTextAttribute() {
        return $this->emp_name;
    }

    protected function getOptionNameAttribute() {
        return $this->emp_code." ".$this->emp_name;
    }

//    protected function getTextAttributeCode() {
//        if ($this->option_name)
//            return $this->option_name;
//
//        return $this->emp_code;
//    }

    protected function getValueAttribute() {
        return $this->emp_id;
    }

    public function empType()
    {
        return $this->belongsTo(LEmpType::class, 'emp_type_id');
    }

    public function salutation()
    {
        return $this->belongsTo(LSalutation::class, 'salutation_id');
    }

    public function gender()
    {
        return $this->belongsTo(LGender::class, 'emp_gender_id');
    }

    public function religion()
    {
        return $this->belongsTo(LReligion::class, 'emp_religion_id');
    }

    public function bloodGroup()
    {
        return $this->belongsTo(LBloodGroup::class, 'emp_blood_group_id');
    }

    public function nationality()
    {
        return $this->belongsTo(LNationality::class, 'emp_nationality_id');
    }

    public function quota()
    {
        return $this->belongsTo(LQuota::class, 'emp_quota_id');
    }

    public function empStatus()
    {
        return $this->belongsTo(LEmpStatus::class, 'emp_status_id');
    }

    public function grade()
    {
        return $this->belongsTo(LEmpGrade::class, 'emp_grade_id');
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

    public function type()
    {
        return $this->belongsTo(LEmpType::class, 'emp_type_id');
    }

    public function maritalStatus()
    {
        return $this->belongsTo(LMaritalStatus::class, 'maritial_status_id');
    }

    public function postType()
    {
        return $this->belongsTo(LPostType::class, 'post_type_id');
    }

    public function gradeStep()
    {
        return $this->belongsTo(LGradeSteps::class, 'grade_step_id');
    }

    public function location()
    {
        return $this->belongsTo(LLocation::class, 'location_id');
    }

    public function bank()
    {
        return $this->belongsTo(LBank::class, 'bank_id');
    }

    public function branch()
    {
        return $this->belongsTo(LBranch::class, 'branch_id');
    }

}
