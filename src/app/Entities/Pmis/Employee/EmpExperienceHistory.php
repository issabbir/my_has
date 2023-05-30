<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpExperienceHistory extends Model
{
    protected $table = 'pmis.emp_experience_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
