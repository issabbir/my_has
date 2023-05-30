<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LDesignation extends Model
{
    protected $table = "pmis.l_designation";
    protected $primaryKey = "designation_id";

    protected $with = ['profession'];

    protected $fillable = [ 'designation','designation_bng','enable_yn','short_name','post_type_id','min_grade_id','max_grade_id',
        'emp_type_id',
        'profession_type_id',
        'ministerial_yn'
    ];

    public function profession() {
        return $this->belongsTo(LProfessionType::class, 'profession_type_id');
    }

    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->designation;
    }

    public function getValueAttribute() {
        return $this->designation_id;
    }
}
