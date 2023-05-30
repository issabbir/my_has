<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LEmpStatus extends Model
{
    protected $table = "pmis.l_emp_status";
    protected $primaryKey = "emp_status_id";

    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->emp_status;
    }

    public function getValueAttribute() {
        return $this->emp_status_id;
    }
}
