<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LInstitute;
use Illuminate\Database\Eloquent\Model;
use App\Entities\Admin\LExam;
use App\Entities\Admin\LExamResult;
use App\Entities\Admin\LExamBody;

class EmpEducationTemp extends Model {

    protected $table = 'pmis.EMP_EDUCATION_TEMP';
    protected $fillable = ['emp_id', 'exam_id', 'exam_body_id', 'exam_result_id', 'subject', 'passing_year', 'subject_bng','instutute_id'];

    protected $primaryKey = 'emp_education_id';
    public $incrementing = false;
    protected $with = ['exam', 'result', 'exam_body', 'institute'];

    public function exam() {
        return $this->belongsTo(LExam::class, "exam_id");
    }

    public function result() {
        return $this->belongsTo(LExamResult::class, "exam_result_id");
    }

    public function exam_body() {
        return $this->belongsTo(LExamBody::class, "exam_body_id");
    }

    public function institute()
    {
        return $this->belongsTo(LInstitute::class, 'instutute_id');
    }
}
