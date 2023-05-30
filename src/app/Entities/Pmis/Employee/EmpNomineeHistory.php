<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpNomineeHistory extends Model
{
    protected $table = 'pmis.emp_nominee_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
