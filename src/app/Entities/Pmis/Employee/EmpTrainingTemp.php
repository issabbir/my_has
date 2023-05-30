<?php

namespace App\Entities\Pmis\Employee;

use App\Entities\Admin\LGeoCountry;
use App\Entities\Admin\LTrainingType;
use Illuminate\Database\Eloquent\Model;

class EmpTrainingTemp extends Model
{
    protected $table = 'pmis.emp_training_temp';
    protected $primaryKey = 'training_id';
    public $incrementing = false;

    protected $with = ['training_type','training_country'];


    public function training_type()
    {
        return $this->belongsTo(LTrainingType::class, 'training_type_id');
    }
    public function training_country()
    {
        return $this->belongsTo(LGeoCountry::class, 'trainig_country_id');
    }
}
