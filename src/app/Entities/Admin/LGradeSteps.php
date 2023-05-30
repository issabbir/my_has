<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LGradeSteps extends Model
{
    protected $table = "pmis.l_grade_steps";
    protected $primaryKey = "grade_steps_id";
    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->basic_amt;
    }

    public function getValueAttribute() {
        return $this->grade_steps_id;
    }
}
