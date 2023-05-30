<?php

namespace App\Entities\Colony;

use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Admin\LGeoThana;
use Illuminate\Database\Eloquent\Model;

class Colony extends Model
{
    //
    protected $table = 'l_colony';
    protected $primaryKey = 'colony_id';

    protected $with = ['division', 'district', 'thana', 'colony_type'];

    public function division()
    {
        return $this->belongsTo(LGeoDivision::class, 'geo_division_id');
    }

    public function district()
    {
        return $this->belongsTo(LGeoDistrict::class, 'geo_district_id');
    }

    public function thana()
    {
        return $this->belongsTo(LGeoThana::class, 'geo_thana_id');
    }

    public function colony_type()
    {
        return $this->belongsTo(ColonyType::class, 'colony_type_id');
    }
}
