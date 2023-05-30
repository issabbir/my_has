<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/27/20
 * Time: 3:32 PM
 */

namespace App\Entities\HouseAllotment;
use App\Entities\HouseAllotment\HaAdvDtl;
use App\Entities\HouseAllotment\BuildingList;

use Illuminate\Database\Eloquent\Model;

class HaAdvMst extends Model
{
    protected $table = 'ha_adv_mst';
    protected $primaryKey = 'adv_id';

    protected $casts = [
        'adv_date' => 'date:Y-m-d',
        'app_start_date' => 'date:Y-m-d',
        'app_end_date'   => 'date:Y-m-d'
    ];

    protected $with = ['advDtl', 'building'];

    public function advDtl(){
        return $this->belongsTo(HaAdvDtl::class, 'adv_dtl_id');
    }
    public function building(){
        return $this->belongsTo(BuildingList::class, 'building_id');
    }
}
