<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpTourHistory extends Model
{
    protected $table = 'pmis.emp_tour_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
