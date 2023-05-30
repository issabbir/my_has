<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/2/20
 * Time: 1:06 PM
 */

namespace App\Contracts;


interface AdvertisementContract
{
    public function getAdvertisementByHouseType($houseType, $dpt_id);
    public function findHouseTypesByAdvertisementId($advertisementId);
    public function findAvailableHouses($advertisementId, $houseTypeId);
    public function findAvailableHousesWithDor($advertisementId, $houseTypeId);
    public function getapplicationHouse($id);
    public function findPreferenceHouses($applicationId, $advertisementId, $houseTypeId);

    public function getAckCivil($emp_id);
    public function getAckHod($emp_id);
    public function getAckValidity($ack_id);
    public function getMstDatatableHouse($advMstId);
    public function getCivilDatatableHouse();
    public function getHodDatatableHouse($dpt_id, $emp_id);

    public function getMstFlatList($building_id, $house_type_id, $advMstId,$building_status_id);
    public function getCivilFlatList($building_id, $house_type_id,$building_status_id);
    public function getHodFlatList($building_id, $house_type_id, $dpt_id, $emp_id,$building_status_id);

    public function getHouseData($building_id,$house_type_id, $dor_yn,$colonyId);
    public function getBuildingData($house_type_id,$colonyID);
//    public function getHouseType();
    public function getAckdata();
    public function getMstDatatableHouseTwo($ack_id, $adv_mst_id);
    public function notifyEmp($adv_id, $dpt_id);
    public function getHouseTypeData($colonyId);
    public function houseTypeChk($advMstId);
    public function getDept($dpt_id);
    public function getAdvertisementByHouseTypeForArray($houseType, $dpt_id);
}
