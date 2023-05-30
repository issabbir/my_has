<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LRelationType extends Model
{
    protected $table = "pmis.l_relation_type";
    protected $primaryKey = "relation_type_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->relation_type;
    }

    protected function getValueAttribute() {
        return $this->relation_type_id;
    }
}
