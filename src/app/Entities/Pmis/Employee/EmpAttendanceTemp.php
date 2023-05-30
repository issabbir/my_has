<?php

namespace App\Entities\Pmis\Employee;


use App\Entities\Admin\LRosterShift;
use Illuminate\Database\Eloquent\Model;

class EmpAttendanceTemp extends Model
{
    protected $table = 'pmis.emp_attendance_temp';
    protected $primaryKey = 'attendance_id';
    public $incrementing = false;
    protected $with = ['employee','shift'];
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
    public function shift()
    {
        return $this->belongsTo(LRosterShift::class, 'shift_id');
    }
}
