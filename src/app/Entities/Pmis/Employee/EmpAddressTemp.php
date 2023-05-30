<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LAddressType;
use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Admin\LGeoThana;
use Illuminate\Database\Eloquent\Model;

class EmpAddressTemp extends Model
{
    protected $table = 'pmis.emp_addresses_temp';
    protected $primaryKey = 'emp_address_id';
    public $incrementing = false;

    protected $with = ['address_type', 'geo_division', 'geo_district', 'geo_thana'];

    public function address_type()
    {
        return $this->belongsTo(LAddressType::class, 'address_type_id');
    }

    public function geo_division()
    {
        return $this->belongsTo(LGeoDivision::class, 'division_id');
    }
    public function geo_district()
    {
        return $this->belongsTo(LGeoDistrict::class, 'district_id');
    }
    public function geo_thana()
    {
        return $this->belongsTo(LGeoThana::class, 'thana_id');
    }
}
