<?php

namespace App\Entities\Allottement;

use App\Entities\HouseAllotment\BuildingList;
use Illuminate\Database\Eloquent\Model;

class AllottmentMST extends Model
{
    protected $table = 'allotment_master';
    protected $primaryKey = 'allotment_m_id';

    protected $casts = [
            'adv_date' => 'date:Y-m-d',
            'app_start_date' => 'date:Y-m-d',
            'app_end_date'   => 'date:Y-m-d'
        ];

    protected $with = ['advDtl', 'building'];

    public function advDtl(){
        return $this->belongsTo(AdvertisementDtl::class, 'adv_dtl_id');
    }
    public function building(){
        return $this->belongsTo(BuildingList::class, 'building_id');
    }
}
