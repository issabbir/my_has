<?php

namespace App\Entities\Allottement;

use App\Entities\HouseAllotment\HouseList;
use Illuminate\Database\Eloquent\Model;

class AllottmenttDtl extends Model
{
    protected $table = 'allotment_detail';
    protected $primaryKey = 'allotment_d_id';

    protected $with = ['houseList'];

    public function houseList(){
       return $this->belongsTo(HouseList::class, 'house_id');
    }

}

