<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LExam extends Model
{
    protected $table = "pmis.l_exam";
    protected $primaryKey = "exam_id";
    
     protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->exam_name;
    }
    
    public function getValueAttribute() {
        return $this->exam_id;
    }
}