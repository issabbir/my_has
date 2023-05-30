<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpFamily extends Model
{
    protected $table = 'pmis.emp_family';
    protected $primaryKey = 'emp_family_id';
    public $incrementing = false;
}
