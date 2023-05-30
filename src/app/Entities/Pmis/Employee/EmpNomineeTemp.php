<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LGender;
use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LMaritalStatus;
use App\Entities\Admin\LRelationType;
use Illuminate\Database\Eloquent\Model;

class EmpNomineeTemp extends Model
{
    protected $table = 'pmis.emp_nominee_temp';
    protected $primaryKey = 'nominee_id';
    public $incrementing = false;


    protected $with = ['gender', 'marital_status', 'relationship', 'district'];

    public function gender()
    {
        return $this->belongsTo(LGender::class, 'nominee_gender_id');
    }

    public function marital_status()
    {
        return $this->belongsTo(LMaritalStatus::class, 'nominee_marital_status_id');
    }

    public function relationship()
    {
        return $this->belongsTo(LRelationType::class, 'relationship_id');
    }

    public function district()
    {
        return $this->belongsTo(LGeoDistrict::class, 'district_id');
    }
}
