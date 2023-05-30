<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmployeeHistory extends Model
{
    protected $table = 'pmis.employee_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
