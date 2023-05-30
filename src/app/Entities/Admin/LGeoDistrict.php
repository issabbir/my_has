<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LGeoDistrict extends Model
{
    protected $table = "pmis.l_geo_district";
    protected $primaryKey = "geo_district_id";

    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->geo_district_name;
    }

    protected function getValueAttribute() {
        return $this->geo_district_id;
    }
}
