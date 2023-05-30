<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LEmpGrade extends Model
{
    protected $table = "pmis.l_emp_grade";
    protected $primaryKey = "emp_grade_id";

   protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->emp_grade;
    }

    public function getValueAttribute() {
        return $this->emp_grade_id;
    }
}
