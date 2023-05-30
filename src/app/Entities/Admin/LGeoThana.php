<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LGeoThana extends Model
{
    protected $table = "pmis.l_geo_thana";
    protected $primaryKey = "geo_thana_id";


    protected $appends = ['text', 'value'];

    protected function getTextAttribute() {
        return $this->geo_thana_name;
    }

    protected function getValueAttribute() {
        return $this->geo_thana_id;
    }
}
