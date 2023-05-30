<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LUserLookups extends Model
{
    protected $table = "CPA_SECURITY.l_user_lookups";
    protected $primaryKey = "";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->meaning;
    }

    protected function getValueAttribute() {
        return $this->lookup_code;
    }
}
