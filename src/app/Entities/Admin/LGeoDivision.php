<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LGeoDivision extends Model
{
    protected $table = "pmis.l_geo_division";
    protected $primaryKey = "geo_division_id";

    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->geo_division_name;
    }

    protected function getValueAttribute() {
        return $this->geo_division_id;
    }
}
