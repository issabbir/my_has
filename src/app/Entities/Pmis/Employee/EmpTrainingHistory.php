<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpTrainingHistory extends Model
{
    protected $table = 'pmis.emp_training_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
