<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpRoster extends Model
{
    protected $table = 'pmis.emp_roster';
    protected $primaryKey = null;
    public $incrementing = false;
}
