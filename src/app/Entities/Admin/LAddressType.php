<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LAddressType extends Model
{
    protected $table = "pmis.l_address_type";
    protected $primaryKey = "address_type_id";
    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->address_type;
    }

    protected function getValueAttribute() {
        return $this->address_type_id;
    }
}
