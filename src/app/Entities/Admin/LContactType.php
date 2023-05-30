<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LContactType extends Model
{
    protected $table = "pmis.l_contact_type";
    protected $primaryKey = "emp_contact_type_id";
    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->emp_contact_type;
    }

    protected function getValueAttribute() {
        return $this->emp_contact_type_id;
    }
}
