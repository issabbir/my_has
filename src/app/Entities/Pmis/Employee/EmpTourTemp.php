<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LFamilyMemberStatus;
use App\Entities\Admin\LGender;
use App\Entities\Admin\LGeoCountry;
use App\Entities\Admin\LRelationType;
use App\Entities\Admin\LTourType;
use Illuminate\Database\Eloquent\Model;

class EmpTourTemp extends Model
{
    protected $table = 'pmis.emp_tour_temp';
     protected $fillable = [ 'emp_id','tour_name','tour_name_bng','tour_type_id','tour_country_id','tour_description','tour_start_date','tour_end_date',
        'tour_duration',
        'tour_sponsor' ,
        'tour_sponsor_bng'
    ];
    public $timestamps = false;
    protected $primaryKey = "tour_id";
    public $incrementing = false;

    protected $with = ['tour_type', 'country'];

    public function tour_type() {
        return $this->belongsTo(LTourType::class, 'tour_type_id');
    }

    public function country()
    {
        return $this->belongsTo(LGeoCountry::class, 'tour_country_id');
    }
}
