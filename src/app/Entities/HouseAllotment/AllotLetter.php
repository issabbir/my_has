<?php

namespace App\entities\houseAllotment;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\Pmis\Employee\Employee;
use Illuminate\Database\Eloquent\Model;

class AllotLetter extends Model
{
    protected $table = 'allot_letter';
    protected $primaryKey = 'allot_letter_id';

//    protected $with = ['houseAllotment'];

//    public function houseAllotment(){
//        return $this->belongsTo(HouseAllotment::class, 'application_id', 'application_id');
//    }
//    public function employee(){
//        return $this->belongsTo(Employee::class, 'emp_id');
//    }
}
