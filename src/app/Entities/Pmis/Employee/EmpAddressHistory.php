<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpAddressHistory extends Model
{
    protected $table = 'pmis.emp_addresses_history';
    protected $primaryKey = null;
    public $incrementing = false;
}
