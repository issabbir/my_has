<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/28/20
 * Time: 3:46 PM
 */

namespace App\Contracts;


interface LookupContract
{
    /**
     * @return LGeoDivision[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findDivisions();

    /**
     * @param null $divisionId
     * @return LGeoDistrict[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findDistrictsByDivision($divisionId = null);

    /**
     * @param $districtId
     * @return LGeoThana[]|\Illuminate\Database\Eloquent\Collection
     */
    public function findThanasByDistrict($districtId);

}
