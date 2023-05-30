<?php


namespace App\Entities\Admin;


use Illuminate\Database\Eloquent\Model;

class LBuildingType extends Model
{
    protected $table = "pmis.l_building_type";
    protected $primaryKey = "building_id";
    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->building_type_name;
    }

    protected function getValueAttribute() {
        return $this->building_id;
    }
}
