<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LGeoCountry extends Model
{
    protected $table = "pmis.l_geo_country";
    protected $primaryKey = "country_id";

    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->country;
    }

    public function getValueAttribute() {
        return $this->country_id;
    }

}







