<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpLeaveHistory extends Model
{
    protected $table = 'pmis.emp_leave_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
