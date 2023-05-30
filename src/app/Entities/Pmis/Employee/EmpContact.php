<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpContact extends Model
{
    protected $table = 'pmis.emp_contacts';
    protected $primaryKey = null;
    public $incrementing = false;
}
