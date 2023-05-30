<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LExamBody extends Model
{
    protected $table = "pmis.l_exam_body";
    protected $primaryKey = "exam_body_id";
    
   protected $appends = ['text', 'value'];
   
   

    public function getTextAttribute() {
        return $this->exam_body_name;
    }
    
    public function getValueAttribute() {
        return $this->exam_body_id;
    }
    
   
}