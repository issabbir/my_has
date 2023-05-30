<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpFamilyHistory extends Model
{
    protected $table = 'pmis.emp_family_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
