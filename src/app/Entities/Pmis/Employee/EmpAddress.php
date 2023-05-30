<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpAddress extends Model
{
    protected $table = 'pmis.emp_addresses';
    protected $primaryKey = null;
    public $incrementing = false;
}
