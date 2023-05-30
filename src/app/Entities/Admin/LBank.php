<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LBank extends Model
{
    protected $table = "pmis.l_banks";
    protected $primaryKey = "bank_id";

    protected $appends = ['text', 'value'];


    protected function getTextAttribute() {
        return $this->bank_name;
    }

    protected function getValueAttribute() {
        return $this->bank_id;
    }
}