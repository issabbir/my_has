<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpAttendance extends Model
{
    protected $table = 'pmis.emp_attendance';
    protected $primaryKey = 'attendance_id';
    public $incrementing = false;

    protected $with = ['employee'];

    public function employee() {
        return $this->belongsTo(Employee::class, 'emp_id');
    }
}
