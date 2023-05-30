<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LExamResult extends Model
{
    protected $table = "pmis.l_exam_result";
    protected $primaryKey = "exam_result_id";
    
    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->exam_result;
    }
    
    public function getValueAttribute() {
        return $this->exam_result_id;
    }
}