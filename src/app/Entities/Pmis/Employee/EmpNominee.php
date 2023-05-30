<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpNominee extends Model
{
    protected $table = 'pmis.emp_nominee';
    protected $primaryKey = 'nominee_id';
}
