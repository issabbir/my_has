<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/28/20
 * Time: 3:45 PM
 */

namespace App\Managers;


use App\Contracts\LookupContract;
use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Admin\LGeoThana;

class LookupManager implements LookupContract
{
    /**
     * @return LGeoDivision[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findDivisions()
    {
        return LGeoDivision::all();
    }

    /**
     * @param null $divisionId
     * @return LGeoDistrict[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findDistrictsByDivision($divisionId = null)
    {
        if($divisionId) {
            return LGeoDistrict::where('geo_division_id', $divisionId)->get();
        }

        return LgeoDistrict::all();
    }

    /**
     * @param $districtId
     * @return LGeoThana[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findThanasByDistrict($districtId)
    {
        if($districtId) {
            return LGeoThana::where('geo_district_id', $districtId)->get();
        }

        return LGeoThana::all();
    }

}
