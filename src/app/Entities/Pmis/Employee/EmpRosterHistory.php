<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpRosterHistory extends Model
{
    protected $table = 'pmis.emp_roster_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
