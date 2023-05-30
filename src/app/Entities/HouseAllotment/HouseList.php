<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/23/20
 * Time: 3:02 PM
 */

namespace App\Entities\HouseAllotment;

use App\Entities\Admin\LDepartment;
use App\Entities\Colony\Colony;
use Illuminate\Database\Eloquent\Model;

class HouseList extends Model
{
    protected $table = 'house_list';
    protected $primaryKey = 'house_id';
    protected $appends = ['in_advertisement'];
    protected $fillable = ['dpt_department_id', 'dept_ack_id'];

    protected $with = ['housestatus', 'housetype', 'buildinglist', 'colonylist','department'];

    public function housestatus() {
        return $this->belongsTo(LHouseStatus::class, 'house_status_id');
    }

    public function housetype() {
        return $this->belongsTo(LHouseType::class, 'house_type_id');
    }

    public function buildinglist() {
        return $this->belongsTo(BuildingList::class, 'building_id');
    }

    public function colonylist() {
        return $this->belongsTo(Colony::class, 'colony_id');
    }

    public function ackdData() {
        return $this->belongsTo(Acknowledgemnt::class, 'dept_ack_id');
    }
    public function department() {
        return $this->belongsTo(LDepartment::class, 'dpt_department_id','department_id');
    }

    protected function getInAdvertisementAttribute() {
        if($this->advertise_yn == 'Y') {
            return 'Yes';
        } else if($this->advertise_yn == 'N') {
            return 'No';
        }
    }
}
