<?php

namespace App\Entities\Pmis\Employee;

use Illuminate\Database\Eloquent\Model;

class EmpLeave extends Model
{
    protected $table = 'pmis.emp_leave';
    protected $primaryKey = 'leave_id';

    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->leave_id."~".$this->leave_start_date."~".$this->leave_end_date;
    }

    public function getValueAttribute() {
        return $this->leave_id;
    }
}
