<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LDepartment extends Model
{
    protected $table = "pmis.l_department";
    protected $primaryKey = "department_id";

    protected $with = ['division'];

    public function division() {
        return $this->belongsTo(LDptDivision::class, 'dpt_division_id');
    }
    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->department_name;
    }

    public function getValueAttribute() {
        return $this->department_id;
    }
}
