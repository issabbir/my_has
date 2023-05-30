<?php


namespace App\Entities\HouseAllotment;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\Pmis\Employee\Employee;
use Illuminate\Database\Eloquent\Model;

class Acknowledgemnt extends Model
{
    protected $table = 'dept_acknowledgement';
    protected $primaryKey = 'dept_ack_id';

    protected $with = ['houseAllotment'];

    public function houseAllotment(){
        return $this->belongsTo(HouseAllotment::class, 'application_id', 'application_id');
    }
}
