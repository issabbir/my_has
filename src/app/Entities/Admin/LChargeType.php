<?php


namespace App\Entities\Admin;


use Illuminate\Database\Eloquent\Model;

class LChargeType extends Model
{
    protected $table = "pmis.l_charge_type";
    protected $primaryKey = "charge_type_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->charge_type_name;
    }

    protected function getValueAttribute() {
        return $this->charge_type_id;
    }
}
