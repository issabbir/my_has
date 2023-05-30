<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpLeaveTemp extends Model
{
    protected $table = 'pmis.emp_leave_temp';
    protected $primaryKey = null;
    public $incrementing = false;
}
