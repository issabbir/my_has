<?php


namespace App\Entities\Admin;


use Illuminate\Database\Eloquent\Model;

class LAttributeType extends Model
{
    protected $table = "pmis.l_attribute_type";
    protected $primaryKey = "attribute_type_id";
    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->attribute_name;
    }

    protected function getValueAttribute() {
        return $this->attribute_type_id;
    }
}
