<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/23/20
 * Time: 4:05 PM
 */

namespace App\Entities\HouseAllotment;

use App\Entities\Colony\Colony;
use Illuminate\Database\Eloquent\Model;
use App\Entities\HouseAllotment\LBuildingInfra;
use App\Entities\HouseAllotment\LBuildingStatus;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Pmis\Employee\Employee;

class BuildingList extends Model
{
    protected $table = 'building_list';
    protected $primaryKey = 'building_id';

    protected $with = ['colony','houseType','buildingInfra','employee','buildingStatus'];
    protected $dates = ['hand_over_date', 'inauguration_date', 'expiration_date'];

//    protected $casts = [
//        'hand_over_date'  => 'date:Y-m-d',
//        'inauguration_date'  => 'date:Y-m-d',
//        'expiration_date'  => 'date:Y-m-d',
//    ];

    public function colony()
    {
        return $this->belongsTo(Colony::class, 'colony_id');
    }

    public function houseType()
    {
        return $this->belongsTo(LHouseType::class, 'house_type_id');
    }

    public function buildingInfra()
    {
        return $this->belongsTo(LBuildingInfra::class, 'building_infra_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function buildingStatus()
    {
        return $this->belongsTo(LBuildingStatus::class, 'building_status_id');
    }
}
