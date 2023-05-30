<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpAttendanceHistory extends Model
{
    protected $table = 'pmis.emp_attendance_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
