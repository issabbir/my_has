<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpRosterTemp extends Model
{
    protected $table = 'pmis.emp_roster_temp';
    protected $primaryKey = null;
    public $incrementing = false;
}
