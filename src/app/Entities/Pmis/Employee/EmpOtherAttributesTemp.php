<?php


namespace App\Entities\Pmis\Employee;


use App\Entities\Admin\LEmpType;
use App\Entities\Admin\LAttributeType;
use Illuminate\Database\Eloquent\Model;

class EmpOtherAttributesTemp extends Model
{
    protected $table = 'pmis.emp_other_attributes_temp';
    protected $primaryKey = 'attribute_id';
    public $incrementing = false;

    public function emp_type() {
        return $this->belongsTo(LEmpType::class, 'emp_type_id');
    }

    public function clubs()
    {
        return $this->belongsTo(LAttributeType::class, 'attribute_type_id');
    }
}
