<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LGeoCountry;
use Illuminate\Database\Eloquent\Model;

class EmpProfCertificateTemp extends Model
{
    protected $table = 'pmis.emp_prof_certificate_temp';
    protected $primaryKey = 'certificate_id';
    public $incrementing = false;

    protected $with = ['certificate_country'];

    public function certificate_country()
    {
        return $this->belongsTo(LGeoCountry::class, 'certificate_country_id');
    }

}
