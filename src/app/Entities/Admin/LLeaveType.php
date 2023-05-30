<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LLeaveType extends Model
{
    protected $table = "pmis.l_leave_type";
    protected $primaryKey = "leave_type_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->leave_type;
    }

    protected function getValueAttribute() {
        return $this->leave_type_id;
    }
}
