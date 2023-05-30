<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LTrainingType extends Model
{
    protected $table = "pmis.l_training_type";
    protected $primaryKey = "training_type_id";

    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->training_type;
    }

    public function getValueAttribute() {
        return $this->training_type_id;
    }
}
