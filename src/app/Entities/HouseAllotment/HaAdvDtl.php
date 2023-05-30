<?php

namespace App\Entities\HouseAllotment;
use App\Entities\HouseAllotment\HouseList;
use Illuminate\Database\Eloquent\Model;

class HaAdvDtl extends Model
{
    protected $table = 'ha_adv_dtl';
    protected $primaryKey = 'adv_dtl_id';

    protected $with = ['houseList'];

    public function houseList(){
        return $this->belongsTo(HouseList::class, 'house_id');
    }

}
