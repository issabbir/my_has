<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LGender extends Model
{
    protected $table = "pmis.l_gender";
    protected $primaryKey = "gender_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->gender_name;
    }

    protected function getValueAttribute() {
        return $this->gender_id;
    }
}
