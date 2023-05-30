<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LFamilyMemberStatus;
use App\Entities\Admin\LGender;
use App\Entities\Admin\LRelationType;
use Illuminate\Database\Eloquent\Model;

class EmpFamilyTemp extends Model
{
    protected $table = 'pmis.emp_family_temp';
    protected $fillable = [ 'emp_id','emp_member_name','emp_member_name_bng','emp_member_relation','emp_member_address','emp_member_dob','emp_member_mobile','emp_member_auth_id',
        'emp_member_medical_id',
        'emp_member_gender_id' ,
        'emp_member_status_id',
        'emp_member_allowance_yn'
    ];
    public $timestamps = false;
    protected $primaryKey = "emp_family_id";
    public $incrementing = false;

    protected $with = ['gender', 'relation', 'family_status'];

    public function gender() {
        return $this->belongsTo(LGender::class, 'gender_id');
    }

    public function relation() {
        return $this->belongsTo(LRelationType::class, 'relation_type_id');
    }

    public function family_status() {
        return $this->belongsTo(LFamilyMemberStatus::class, 'family_member_status_id');
    }
}


