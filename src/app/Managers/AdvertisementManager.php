<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/2/20
 * Time: 1:06 PM
 */

namespace App\Managers;

use App\Contracts\AdvertisementContract;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Enums\HouseStatus;
use Illuminate\Support\Facades\DB;
use function HighlightUtilities\splitCodeIntoArray;

/**
 * Class AdvertisementManager
 * @package App\Managers
 */
class AdvertisementManager implements AdvertisementContract
{
    /**
     * @param $houseType
     * @return array
     */
    public function getAdvertisementByHouseType($houseType, $dpt_id)
    {
        $query = <<<QUERY
SELECT ham.adv_id adv_id, ham.adv_number adv_number FROM HA_ADV_MST ham
  LEFT JOIN HA_ADV_DTL had
    ON ham.adv_id = had.adv_id
  LEFT JOIN HOUSE_LIST hl
      ON had.house_id =  hl.house_id
  LEFT JOIN L_HOUSE_TYPE lht
    ON hl.house_type_id = lht.house_type_id
WHERE hl.house_type_id = :house_type_id
and ham.approved_yn = :approved
AND ham.DPT_DEPARTMENT_ID = :dpt_department_id
AND TRUNC (SYSDATE) BETWEEN TRUNC (ham.APP_START_DATE)
                               AND TRUNC (ham.APP_END_DATE)
GROUP BY ham.adv_id, ham.adv_number
QUERY;

        $currentDate = new \DateTime();

        return DB::select($query, ['house_type_id' => $houseType,
        'dpt_department_id' => $dpt_id,
        'approved'=> 'Y'

        /*    'app_end_date' => $currentDate->format('Y-m-d')*/
        ]);
    }

    /**
     * @param $advertisementId
     * @return array
     * @throws \Exception
     */
    public function findHouseTypesByAdvertisementId($advertisementId)
    {
        $query = <<<QUERY
SELECT lht.HOUSE_TYPE_ID house_type_id, lht.house_type FROM HA_ADV_MST ham
  LEFT JOIN HA_ADV_DTL had
    on ham.adv_id = had.adv_id,
    HOUSE_LIST hl,
    L_HOUSE_TYPE lht
WHERE
      had.house_id = HL.house_id
AND
      hl.house_type_id = lht.house_type_id
AND
      ham.adv_id = :advertisement_id
/*AND
      ham.app_end_date < :app_end_date*/
GROUP BY lht.HOUSE_TYPE_ID, lht.house_type
ORDER BY lht.house_type_id
QUERY;

        $currentDate = new \DateTime();

        return DB::select($query, [
            'advertisement_id' => $advertisementId,
            /*'app_end_date' => $currentDate->format('Y-m-d'),*/
        ]);
    }

    /**
     * @param $advertisementId
     * @param $houseTypeId
     * @return array
     */
    public function findAvailableHouses($advertisementId, $houseTypeId)
    {
        $query = <<<QUERY
SELECT hl.house_id house_id,
       hl.house_name house_name,
       hl.house_name_bng house_name_bng,
       hl.house_code house_code,
       hl.dormitory_yn,
       hl.building_id,
       bl.building_name,
       hl.colony_id,
       cl.colony_name
FROM HA_ADV_MST ham
  LEFT JOIN HA_ADV_DTL had ON ham.adv_id = had.adv_id
  LEFT JOIN HOUSE_LIST hl ON had.house_id = hl.house_id
  LEFT JOIN building_list bl ON bl.building_id = hl.building_id
  LEFT JOIN l_colony cl ON cl.colony_id = hl.colony_id
  LEFT OUTER JOIN HOUSE_ALLOTTMENT ha
          ON (had.ADV_ID = ha.ADVERTISEMENT_ID AND had.HOUSE_ID = ha.HOUSE_ID)
  LEFT JOIN L_HOUSE_TYPE lht ON lht.house_type_id = hl.house_type_id
WHERE
      ha.HOUSE_ID IS NULL
AND ham.adv_id = :advertisement_id
  AND lht.house_type_id = :house_type_id
--AND hl.house_status_id = :house_status_id
AND hl.advertise_yn = 'Y'
ORDER BY HL.house_code ASC
QUERY;

//echo $query;exit();

//        return DB::select($query, ['advertisement_id' => $advertisementId, 'house_type_id' => $houseTypeId, 'house_status_id' => HouseStatus::AVAILABLE]); //changed on 09-May-22 according to Yesmin
        return DB::select($query, ['advertisement_id' => $advertisementId, 'house_type_id' => $houseTypeId, ]);
    }

    public function findAvailableHousesWithDor($advertisementId, $houseTypeId)
    {
        $query = <<<QUERY
SELECT hl.house_id house_id,
       hl.house_name house_name,
       hl.house_name_bng house_name_bng,
       hl.house_code house_code,
       hl.dormitory_yn,
       hl.building_id,
       bl.building_name
FROM HA_ADV_MST ham
  LEFT JOIN HA_ADV_DTL had ON ham.adv_id = had.adv_id
  LEFT JOIN HOUSE_LIST hl ON had.house_id = hl.house_id
  LEFT JOIN building_list bl ON bl.building_id = hl.building_id
  LEFT OUTER JOIN HOUSE_ALLOTTMENT ha
          ON (had.ADV_ID = ha.ADVERTISEMENT_ID AND had.HOUSE_ID = ha.HOUSE_ID)
  LEFT JOIN L_HOUSE_TYPE lht ON lht.house_type_id = hl.house_type_id
WHERE
      ha.HOUSE_ID IS NULL
AND ham.adv_id = :advertisement_id
  AND lht.house_type_id in (:house_type_id, '11')
AND hl.house_status_id = :house_status_id
AND hl.advertise_yn = 'Y'
ORDER BY HL.house_code ASC
QUERY;

//echo $query;exit();

        return DB::select($query, ['advertisement_id' => $advertisementId, 'house_type_id' => $houseTypeId, 'house_status_id' => HouseStatus::AVAILABLE]);
    }


    public function findPreferenceHouses($applicationId, $advertisementId, $houseTypeId)
    {
        $query = <<<QUERY
SELECT hl.house_id           house_id,
         hl.house_name         house_name,
         hl.house_name_bng     house_name_bng,
         hl.house_code         house_code,
         hl.building_id,
         bl.building_name,
         hl.colony_id,
         cl.colony_name
    FROM HA_ADV_MST              ham
         LEFT JOIN HA_ADV_DTL had ON ham.adv_id = had.adv_id
         LEFT JOIN HOUSE_LIST hl ON had.house_id = hl.house_id
         LEFT JOIN building_list bl ON bl.building_id = hl.building_id
         LEFT JOIN l_colony cl ON cl.colony_id = hl.colony_id
--          LEFT OUTER JOIN HOUSE_ALLOTTMENT ha
--              ON (    had.ADV_ID = ha.ADVERTISEMENT_ID
--                  AND had.HOUSE_ID = ha.HOUSE_ID)
         LEFT JOIN L_HOUSE_TYPE lht ON lht.house_type_id = hl.house_type_id,
         HAS.APP_PREFERENCE_HOUSE haph
   WHERE     ham.ADV_ID = haph.ADVERTISEMENT_ID
         AND had.HOUSE_ID = haph.HOUSE_ID
         --AND ha.HOUSE_ID IS NULL
         AND ham.adv_id = :advertisement_id
         --AND lht.house_type_id =  :house_type_id
         --AND hl.house_status_id = :house_status_id
         and haph.APPLICATION_ID = :APPLICATION_ID
         AND hl.advertise_yn = 'Y'
         AND HL.HOUSE_ID NOT IN
                 (SELECT HOUSE_ID
                    FROM HOUSE_ALLOTTMENT HA
                   WHERE     HAD.ADV_ID = HA.ADVERTISEMENT_ID
                         AND HAD.HOUSE_ID = HA.HOUSE_ID)
ORDER BY haph.SEQ_ID

QUERY;

//        return DB::select($query, ['APPLICATION_ID' => $applicationId, 'advertisement_id' => $advertisementId, 'house_type_id' => $houseTypeId, 'house_status_id' => HouseStatus::AVAILABLE]);
        return DB::select($query, ['APPLICATION_ID' => $applicationId, 'advertisement_id' => $advertisementId]);
    }
public function findavailableWithoutPreferenceHouses( $advertisementId)
    {

        $query = <<<QUERY
SELECT  hl.house_id           house_id,
         hl.house_name         house_name,
         hl.house_name_bng     house_name_bng,
         hl.house_code         house_code,
         hl.building_id,
         bl.building_name,
         hl.colony_id,
         cl.colony_name
         from HAS.HA_ADV_DTL ha
         LEFT JOIN HOUSE_LIST hl ON ha.house_id = hl.house_id
         LEFT JOIN building_list bl ON bl.building_id = hl.building_id
         LEFT JOIN l_colony cl ON cl.colony_id = hl.colony_id
 WHERE     NOT EXISTS
               (SELECT H.HOUSE_ID
                  FROM HAS.HOUSE_ALLOTTMENT h
                  WHERE ha.house_id = h.house_id)
                 and  HA.ADV_ID = :advertisement_id

QUERY;

     return DB::select($query, ['advertisement_id' => $advertisementId]);
    }

    public function getAllotedFlat($user)
    {
        $query = <<<QUERY
SELECT DISTINCT am.ALLOTMENT_M_ID,
                  am.ALLOTMNT_NUMBER,
                  AM.ALLOTMENT_DATE,
                  am.DEPARTMENT_NAME,
                  hod.id_head_of_department,
                  hod.HEAD_OF_DEPARTMENT,
                  am.DESCRIPTION,
                  am.DESCRIPTION_BNG,
                  COUNT (ad.HOUSE_ID) as house
    FROM has.allotment_master am,
         has.allotment_detail ad,
         has.Head_of_dept_vu hod
   WHERE     am.ALLOTMENT_M_ID = ad.ALLOTMENT_M_ID
         AND am.DEPARMENT_ID = hod.DEPARTMENT_ID
         AND hod.id_head_of_department = :p_head_id
GROUP BY am.ALLOTMENT_M_ID,
         am.ALLOTMNT_NUMBER,
         AM.ALLOTMENT_DATE,
         am.DEPARTMENT_NAME,
         hod.id_head_of_department,
         hod.HEAD_OF_DEPARTMENT,
         am.DESCRIPTION,
         am.DESCRIPTION_BNG
QUERY;

        return DB::select($query, ['p_head_id' => $user]);
    }
    public function getAckdata()
    {

        $querys = <<<QUERY
SELECT DA.DEPT_ACK_ID, DA.DEPT_ACK_NO, DA.NO_OF_ALLOTED_FLAT
  FROM DEPT_ACKNOWLEDGEMENT DA, HOUSE_LIST HL
 WHERE     DA.DEPT_ACK_ID = HL.DEPT_ACK_ID(+)
       AND HL.DEPT_ACK_ID IS NULL
       AND HL.DPT_DEPARTMENT_ID IS NULL
       AND DA.DPT_DEPARTMENT_ID <>117
       and DA.OLD_ACK_YN <> 'Y'
       and DA.TRANSFERRED_YN <> 'Y'
QUERY;

        $ackdata = DB::select(DB::raw($querys));
        return $ackdata;
    }

 public function getBuildingData($house_type_id,$colonyId)
    {

        $querys = <<<QUERY
SELECT DISTINCT H.BUILDING_ID, B.BUILDING_NAME
  FROM HOUSE_LIST H, BUILDING_LIST B
 WHERE     H.BUILDING_ID = B.BUILDING_ID
       AND H.HOUSE_STATUS_ID = 1
       AND H.ADVERTISE_YN = 'N'
        AND H.RESERVE_YN = 'N'
       AND H.HOUSE_TYPE_ID = '$house_type_id'
       AND H.COLONY_ID = '$colonyId'
       AND H.DEPT_ACK_ID IS NULL
QUERY;

        $buildingdata = DB::select(DB::raw($querys));
        return $buildingdata;
    }

 public function getHouseData($building_id,$house_type_id, $dor_yn,$colonyId)
    {
        if($dor_yn == 'Y')
        {
            $querys = <<<QUERY
SELECT H.HOUSE_ID, H.HOUSE_CODE, H.HOUSE_NAME,B.BUILDING_ROAD_NO
  FROM HOUSE_LIST H, BUILDING_LIST B
 WHERE  H.BUILDING_ID = B.BUILDING_ID   AND H.HOUSE_STATUS_ID = 1
       AND H.DORMITORY_YN = 'Y'
       AND H.ADVERTISE_YN = 'N'
       AND H.RESERVE_YN = 'N'
       AND H.BUILDING_ID = '$building_id'
       AND H.COLONY_ID = '$colonyId'
       AND H.HOUSE_TYPE_ID = '$house_type_id'
       AND DPT_DEPARTMENT_ID IS NULL
       AND DEPT_ACK_ID IS NULL
QUERY;

        }
        else
        {
            $querys = <<<QUERY
SELECT H.HOUSE_ID, H.HOUSE_CODE, H.HOUSE_NAME,B.BUILDING_ROAD_NO
  FROM HOUSE_LIST H, BUILDING_LIST B
 WHERE  H.BUILDING_ID = B.BUILDING_ID   AND H.HOUSE_STATUS_ID = 1
       AND H.DORMITORY_YN = 'N'
       AND H.ADVERTISE_YN = 'N'
       AND H.RESERVE_YN = 'N'
       AND H.BUILDING_ID = '$building_id'
       AND H.COLONY_ID = '$colonyId'
       AND H.HOUSE_TYPE_ID = '$house_type_id'
       AND DPT_DEPARTMENT_ID IS NULL
       AND DEPT_ACK_ID IS NULL
QUERY;


        }

        $housedata = DB::select(DB::raw($querys));
        return $housedata;
    }

 public function getHouseTypeData($colonyId)
    {


        $querys = <<<QUERY
SELECT DISTINCT C.COLONY_NAME, T.HOUSE_TYPE,A.HOUSE_TYPE_ID
    FROM HOUSE_LIST A, L_COLONY C, L_HOUSE_TYPE T
   WHERE     A.HOUSE_TYPE_ID = T.HOUSE_TYPE_ID
         AND A.COLONY_ID = C.COLONY_ID
         AND A.HOUSE_STATUS_ID = 1
         AND A.ADVERTISE_YN = 'N'
         AND A.DEPT_ACK_ID IS NULL
         AND A.HOUSE_TYPE_ID NOT IN (6, 7)
         AND A.COLONY_ID = '$colonyId'
ORDER BY T.HOUSE_TYPE ASC
QUERY;


        $housetypedata = DB::select(DB::raw($querys));
        return $housetypedata;
    }

    public function getapplicationHouse($id)
    {
        $querys = <<<QUERY
   SELECT HAP.HOUSE_ID,
         HL.HOUSE_NAME,
         HL.HOUSE_CODE,
         HL.DORMITORY_YN,
         HL.BUILDING_ID,
         BL.BUILDING_NAME,
         HL.COLONY_ID,
         LC.COLONY_NAME
    FROM HAS.HA_APPLICATION      HA,
         HAS.APP_PREFERENCE_HOUSE HAP,
         HAS.HOUSE_LIST          HL,
         HAS.BUILDING_LIST       BL,
         HAS.L_COLONY            LC
   WHERE     HA.APPLICATION_ID = :application_id
         AND HA.APPLICATION_ID = HAP.APPLICATION_ID
         AND HL.HOUSE_ID = HAP.HOUSE_ID
         AND BL.BUILDING_ID = HL.BUILDING_ID
         AND LC.COLONY_ID = HL.COLONY_ID
ORDER BY HAP.SEQ_ID ASC
QUERY;

        $housedata = DB::select($querys, ['application_id' => $id]);
        return $housedata;
    }

    public function getAckCivil($emp_id){
        $querys = "SELECT DA.DEPT_ACK_ID, DA.DEPT_ACK_NO, DA.NO_OF_ALLOTED_FLAT
  FROM DEPT_ACKNOWLEDGEMENT DA, HOUSE_LIST HL
 WHERE     DA.DEPT_ACK_ID = HL.DEPT_ACK_ID(+)
       AND HL.DEPT_ACK_ID IS NULL
       AND HL.DPT_DEPARTMENT_ID IS NULL
       AND NVL(DA.DORMITORY_YN, 'N') = 'N' ";

        $civil_data = DB::select($querys);

        $querys_2 = "SELECT DISTINCT DA.DEPT_ACK_ID,
                DA.DEPT_ACK_NO,
                HL.DEPT_ACK_ID,
                HL.DPT_DEPARTMENT_ID
  FROM DEPT_ACKNOWLEDGEMENT DA, HOUSE_LIST HL, PMIS.EMPLOYEE pe
 WHERE     HL.DEPT_ACK_ID = DA.DEPT_ACK_ID
       AND hl.DPT_DEPARTMENT_ID = pe.DPT_DEPARTMENT_ID
       AND pe.emp_id = $emp_id
     --  AND hl.advertise_yn <> 'Y'
       AND hl.HOUSE_TYPE_ID IN (1,
                                    2,
                                    3,
                                    4,
                                    5,
                                    10,
                                    11)";

        $hod_data = DB::select($querys_2);
        return array_merge($civil_data, $hod_data);
    }

    public function getAckHod($emp_id){
        $querys = "SELECT DISTINCT DA.DEPT_ACK_ID,
                DA.DEPT_ACK_NO,
                HL.DEPT_ACK_ID,
                HL.DPT_DEPARTMENT_ID
  FROM DEPT_ACKNOWLEDGEMENT DA, HOUSE_LIST HL, PMIS.EMPLOYEE pe
 WHERE     HL.DEPT_ACK_ID = DA.DEPT_ACK_ID
       AND hl.DPT_DEPARTMENT_ID = pe.DPT_DEPARTMENT_ID
       AND pe.emp_id = $emp_id
    --   AND hl.advertise_yn <> 'Y'
       AND hl.HOUSE_TYPE_ID IN (1,
                                    2,
                                    3,
                                    4,
                                    5,
                                    10,
                                    11)";

        return DB::select($querys);
    }

    public function getAckValidity($ack_id){
        $querys = "SELECT TO_CHAR(DEPT_REQ_VALID_FROM, 'dd-MM-YYYY') AS VALID_FROM, TO_CHAR(DEPT_REQ_VALID_TO, 'dd-MM-YYYY') AS VALID_TO, DEPARTMENT_NAME FROM DEPT_ACKNOWLEDGEMENT
WHERE DEPT_ACK_ID = $ack_id";

        return DB::select($querys);
    }

    public function getMstDatatableHouse($advMstId){
//        $querys = "SELECT DISTINCT
//         ht.HOUSE_TYPE,
//         ht.house_type_id,
//         hl.HOUSE_STATUS_ID,
//         bl.building_name,
//         bl.building_id,
//         COUNT (ht.HOUSE_TYPE)
//             OVER (PARTITION BY hl.BUILDING_ID, ht.HOUSE_TYPE)
//             AS ALLOTED_HOUSE,
//         bs.BUILDING_STATUS,
//         bl.BUILDING_ROAD_NO,
//         c.COLONY_NAME,
//         c.COLONY_id
//    FROM HAS.HOUSE_LIST       hl,
//         HAS.BUILDING_LIST    bl,
//         HAS.L_HOUSE_TYPE     ht,
//         HAS.L_BUILDING_STATUS bs,
//         HAS.L_COLONY         c
//   WHERE     hl.BUILDING_ID = bl.BUILDING_ID
//         AND hl.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID
//         AND hl.HOUSE_STATUS_ID = bs.BUILDING_STATUS_ID
//         AND c.COLONY_ID = bl.COLONY_ID
//         AND hl.DEPT_ACK_ID = $ack_id
//        order by bl.building_id,c.COLONY_id";

        $querys = "SELECT DISTINCT
         ht.HOUSE_TYPE,
         ht.house_type_id,
         hl.HOUSE_STATUS_ID,
         bl.building_name,
         bl.building_id,
         COUNT (ht.HOUSE_TYPE)
             OVER (PARTITION BY hl.BUILDING_ID, ht.HOUSE_TYPE)
             AS ALLOTED_HOUSE,
         bs.BUILDING_STATUS,
         bl.BUILDING_ROAD_NO,
         c.COLONY_NAME,
         c.COLONY_id,
         da.DEPT_ACK_ID,'acknowledge'||da.DEPT_ACK_ID obj,da.workflow_process
    FROM HAS.HOUSE_LIST       hl,
         HAS.BUILDING_LIST    bl,
         HAS.L_HOUSE_TYPE     ht,
         HAS.L_BUILDING_STATUS bs,
         HAS.L_COLONY         c,
         HAS.HA_ADV_DTL had,
         HAS.DEPT_ACKNOWLEDGEMENT da
   WHERE     hl.BUILDING_ID = bl.BUILDING_ID
         AND hl.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID
         AND hl.HOUSE_STATUS_ID = bs.BUILDING_STATUS_ID
         AND c.COLONY_ID = bl.COLONY_ID
         AND hl.HOUSE_ID = had.HOUSE_ID
         AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
         AND had.ADV_ID = $advMstId
        order by bl.building_id,c.COLONY_id";

        return DB::select($querys);
    }

    public function getCivilDatatableHouse(){
//        $querys = "SELECT DISTINCT
//       ht.HOUSE_TYPE,
//       ht.house_type_id,
//       bl.building_name,
//       bl.building_id,
//       COUNT (ht.HOUSE_TYPE)
//           OVER (PARTITION BY hl.BUILDING_ID, ht.HOUSE_TYPE)
//           AS ALLOTED_HOUSE,
//       bs.BUILDING_STATUS
//  FROM HAS.HOUSE_LIST         hl,
//       HAS.BUILDING_LIST      bl,
//       HAS.L_HOUSE_TYPE       ht,
//       HAS.L_BUILDING_STATUS  bs
// WHERE     hl.BUILDING_ID = bl.BUILDING_ID
//       AND hl.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID
//       AND bl.BUILDING_STATUS_ID = bs.BUILDING_STATUS_ID
//       AND hl.ADVERTISE_YN <> 'Y'
//       AND hl.RESERVE_YN <> 'Y'
//       AND hl.HOUSE_TYPE_ID NOT IN (1,
//                                    2,
//                                    3,
//                                    4,
//                                    5)
//       AND bs.BUILDING_STATUS_ID = 1";

        $querys = "SELECT ht.HOUSE_TYPE,
         ht.house_type_id,
         bl.building_name,
         bl.building_id,
         bs.BUILDING_STATUS,
         bl.BUILDING_ROAD_NO,
         c.COLONY_NAME,
         c.COLONY_id,
         hl.HOUSE_STATUS_ID,bl.BUILDING_STATUS_ID,
         COUNT (ht.house_type_id)     AS ALLOTED_HOUSE
         -- da.DEPT_ACK_ID ,'acknowledge'||da.DEPT_ACK_ID obj,da.workflow_process
    FROM HAS.HOUSE_LIST       hl,
         HAS.BUILDING_LIST    bl,
         HAS.L_HOUSE_TYPE     ht,
         HAS.L_BUILDING_STATUS bs,
         HAS.L_COLONY         c
         -- HAS.DEPT_ACKNOWLEDGEMENT da
   WHERE     hl.BUILDING_ID = bl.BUILDING_ID
         AND hl.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID
         AND bl.BUILDING_STATUS_ID = bs.BUILDING_STATUS_ID
         AND c.COLONY_ID = bl.COLONY_ID
         -- AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
         AND hl.ADVERTISE_YN <> 'Y'
         AND hl.RESERVE_YN <> 'Y'
         AND hl.HOUSE_TYPE_ID NOT IN (1,
                                      2,
                                      3,
                                      4,
                                      5,
                                      10,
                                      11)
         --AND bs.BUILDING_STATUS_ID = 1
         AND hl.HOUSE_STATUS_ID in (1, 2)
GROUP BY ht.HOUSE_TYPE,
         ht.house_type_id,
         bl.building_name,
         bl.building_id,
         bs.BUILDING_STATUS,
         bl.BUILDING_ROAD_NO,
         c.COLONY_NAME,
         c.COLONY_id,bl.BUILDING_STATUS_ID,
         hl.HOUSE_STATUS_ID
         -- ,da.DEPT_ACK_ID,'acknowledge'||da.DEPT_ACK_ID,da.workflow_process

ORDER BY ht.house_type_id,c.COLONY_id";
//echo $querys;exit;
        return DB::select($querys);
    }

    public function getHodDatatableHouse($dpt_id, $emp_id){

        /*
        $querys = "SELECT DISTINCT
         ht.HOUSE_TYPE,
         ht.house_type_id,
         bl.building_name,
         bl.building_id,
         hl.HOUSE_STATUS_ID,
         COUNT (ht.HOUSE_TYPE) OVER (PARTITION BY hl.BUILDING_ID, hl.HOUSE_STATUS_ID, ht.house_type_id) AS ALLOTED_HOUSE,
         bs.BUILDING_STATUS,
         bs.BUILDING_STATUS_id,
         bl.BUILDING_ROAD_NO,
         c.COLONY_NAME,
         c.COLONY_id
         ,da.approved_yn
         -- ,da.DEPT_ACK_ID,'acknowledge'||da.DEPT_ACK_ID obj,da.workflow_process
    FROM HAS.HOUSE_LIST       hl,
         HAS.BUILDING_LIST    bl,
         HAS.L_HOUSE_TYPE     ht,
         HAS.L_BUILDING_STATUS bs,
         PMIS.EMPLOYEE        pe,
         HAS.L_COLONY         c,
         HAS.DEPT_ACKNOWLEDGEMENT da
   WHERE    hl.BUILDING_ID = bl.BUILDING_ID
        AND hl.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID
        AND hl.HOUSE_STATUS_ID = bs.BUILDING_STATUS_ID
        AND hl.DPT_DEPARTMENT_ID = pe.DPT_DEPARTMENT_ID
        AND c.COLONY_ID = bl.COLONY_ID
        AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
        AND hl.DPT_DEPARTMENT_ID = $dpt_id
        AND pe.emp_id = $emp_id
        AND hl.advertise_yn <> 'Y'
        AND hl.house_status_id in (1, 2)
         -- AND da.WORKFLOW_PROCESS is not null
        AND DA.APPROVED_YN ='Y'
ORDER BY bl.building_id,c.COLONY_id";
        */
        $querys="select distinct
             ht.HOUSE_TYPE, ht.house_type_id, hl.house_status_id
             ,bl.building_name,bl.building_id,bl.BUILDING_ROAD_NO,bl.BUILDING_STATUS_id
             ,bs.BUILDING_STATUS
             ,c.COLONY_NAME, c.COLONY_id
             ,COUNT (ht.HOUSE_TYPE) -- OVER (PARTITION BY ht.HOUSE_TYPE, ht.house_type_id)
             AS ALLOTED_HOUSE
             ,da.approved_yn
             from
             HAS.HOUSE_LIST       hl,
             HAS.BUILDING_LIST    bl,
             HAS.L_HOUSE_TYPE     ht,
             HAS.DEPT_ACKNOWLEDGEMENT da,
             HAS.L_BUILDING_STATUS bs,
             HAS.L_COLONY         c
             where
                    hl.BUILDING_ID = bl.BUILDING_ID
                    AND hl.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID
                    AND bl.BUILDING_STATUS_id = bs.BUILDING_STATUS_ID
                    AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
                    AND bl.COLONY_ID = c.COLONY_ID
                    AND hl.DPT_DEPARTMENT_ID = $dpt_id
                    AND DA.APPROVED_YN ='Y'
                    AND hl.advertise_yn <> 'Y'
                    AND hl.house_status_id in (1, 2)
                    AND hl.HOUSE_TYPE_ID IN (1,
                                      2,
                                      3,
                                      4,
                                      5,
                                      10,
                                      11)
                    group by ht.HOUSE_TYPE, ht.house_type_id, hl.house_status_id
                            ,bl.building_name,bl.building_id,bl.BUILDING_ROAD_NO,bl.BUILDING_STATUS_id
                            ,da.approved_yn ,c.COLONY_NAME, c.COLONY_id,bs.BUILDING_STATUS
            ORDER BY ht.HOUSE_TYPE ";

        return DB::select($querys);
    }

    public function getMstFlatList($building_id, $house_type_id, $advMstId,$building_status_id=null){

//        $querys = "SELECT house_name,
//       hl.house_id,
//       advertise_yn,
//       hl.dormitory_yn,
//       house_status_id,
//       house_code
//       da.DEPT_ACK_ID,
//       'acknowledge' || da.DEPT_ACK_ID     obj,
//       da.workflow_process
//  FROM HAS.HOUSE_LIST hl, HAS.HA_ADV_DTL had, HAS.DEPT_ACKNOWLEDGEMENT da
// WHERE     hl.BUILDING_ID = $building_id
//       -- AND hl.HOUSE_TYPE_ID = $house_type_id
//       AND DA.APPROVED_YN = 'Y'
//       AND hl.HOUSE_ID = had.HOUSE_ID
//       AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
//       AND had.ADV_ID = $advMstId";

        $querys = "SELECT distinct house_name,
       hl.house_id,
       advertise_yn,
       hl.dormitory_yn,
       house_status_id,
       house_code
       --da.DEPT_ACK_ID,
       --'acknowledge' || da.DEPT_ACK_ID     obj,
       --da.workflow_process
  FROM HAS.HOUSE_LIST hl, HAS.HA_ADV_DTL had, HAS.DEPT_ACKNOWLEDGEMENT da
 WHERE     hl.BUILDING_ID = $building_id
       -- AND hl.HOUSE_TYPE_ID = $house_type_id
       AND DA.APPROVED_YN = 'Y'
       AND hl.HOUSE_ID = had.HOUSE_ID
       --AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
       AND had.ADV_ID = $advMstId";
//echo $querys;exit;
        return DB::select($querys);
    }

    public function getCivilFlatList($building_id, $house_type_id,$building_status_id=null){

        $querys = "SELECT house_name, hl.house_id, hl.dormitory_yn, house_status_id, house_code,da.DEPT_ACK_ID,'acknowledge'||da.DEPT_ACK_ID obj,da.workflow_process
  FROM HOUSE_LIST hl, HAS.DEPT_ACKNOWLEDGEMENT da
 WHERE     hl.BUILDING_ID = $building_id
       AND hl.HOUSE_TYPE_ID = $house_type_id
       AND hl.advertise_yn <> 'Y'
       AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
       AND DA.APPROVED_YN ='Y'
       --AND hl.house_status_id = 1
       AND hl.HOUSE_TYPE_ID NOT IN (1,
                                    2,
                                    3,
                                    4,
                                    5,
                                    10,
                                    11)";

        return DB::select($querys);
    }

    public function getHodFlatList($building_id, $house_type_id, $dpt_id, $emp_id,$building_status_id){
        $querys = "SELECT house_name, hl.house_id, hl.dormitory_yn, house_status_id, house_code, house_code,
                   da.DEPT_ACK_ID,'acknowledge'||da.DEPT_ACK_ID obj,da.workflow_process,da.WORKFLOW_PROCESS_ID, da.APPROVED_YN,bl.BUILDING_STATUS_ID
        FROM HOUSE_LIST hl, PMIS.EMPLOYEE pe, HAS.DEPT_ACKNOWLEDGEMENT da, building_list bl
        WHERE     hl.DPT_DEPARTMENT_ID = pe.DPT_DEPARTMENT_ID
        AND bl.building_id = hl.building_id
        AND hl.BUILDING_ID = $building_id
        AND hl.HOUSE_TYPE_ID = $house_type_id
        AND hl.DEPT_ACK_ID = da.DEPT_ACK_ID(+)
        AND hl.DPT_DEPARTMENT_ID = $dpt_id
        AND pe.emp_id = $emp_id
        AND bl.BUILDING_STATUS_ID = $building_status_id
        AND hl.advertise_yn <> 'Y'
        AND DA.APPROVED_YN ='Y'
        -- AND da.WORKFLOW_PROCESS is not null
        "; //--AND hl.house_status_id = 1 //AND da.APPROVED_YN = 'Y'

//        echo '<pre>'.$querys;
        return DB::select($querys);
    }


    public function findWorkflowStepLimit($workflow_obj_id,$workflow_process =null){
      /*  $sql ="select wp.WORKFLOW_PROCESS_ID,wp.WORKFLOW_OBJECT_ID,wp.WORKFLOW_STEP_ID, wp.NOTE,
       (select max(ws.PROCESS_STEP) from  PMIS.WORKFLOW_STEPS ws where APPROVAL_WORKFLOW_ID = $workflow_process) max_step_limit,
       (select ws2.PROCESS_STEP from PMIS.WORKFLOW_STEPS ws2 where ws2.WORKFLOW_STEP_ID = wp.WORKFLOW_STEP_ID ) running_step
       from
       PMIS.WORKFLOW_PROCESS wp
       where WORKFLOW_OBJECT_ID = '".$workflow_obj_id."' and wp.workflow_step_id is not null order by WORKFLOW_PROCESS_ID desc";*/
        $sql ="select count(wp.WORKFLOW_PROCESS_ID) finally_approved
       from PMIS.WORKFLOW_PROCESS wp
       where WORKFLOW_OBJECT_ID = '".$workflow_obj_id."' and wp.workflow_step_id is null ";
        $returnData = DB::select($sql);
        return $returnData;
    }

    public function getMstDatatableHouseTwo($ack_id, $adv_mst_id){
        $querys = "SELECT DISTINCT
       ht.HOUSE_TYPE,
       ht.house_type_id,
       bl.building_name,
       bl.building_id,
       COUNT (ht.HOUSE_TYPE)
           OVER (PARTITION BY hl.BUILDING_ID, ht.HOUSE_TYPE)
           AS ALLOTED_HOUSE,
       DECODE (hl.ADVERTISE_YN,  'Y', 'ADVERTISED',  'N', 'NOT ADVERTISED')
           ADVERTISE_YN,
       bs.BUILDING_STATUS
  FROM HAS.HOUSE_LIST         hl,
       HAS.BUILDING_LIST      bl,
       HAS.L_HOUSE_TYPE       ht,
       HAS.L_BUILDING_STATUS  bs,
       HAS.HA_ADV_DTL         ad
 WHERE     hl.BUILDING_ID = bl.BUILDING_ID
       AND hl.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID
       AND hl.HOUSE_STATUS_ID = bs.BUILDING_STATUS_ID
       AND hl.DEPT_ACK_ID = $ack_id
       AND ad.ADV_ID = $adv_mst_id
       AND ad.HOUSE_ID = hl.HOUSE_ID
       AND hl.ADVERTISE_YN = 'Y'
UNION ALL
SELECT DISTINCT
       ht.HOUSE_TYPE,
       hl.HOUSE_TYPE_ID,
       -- HL.HOUSE_ID,
       bl.building_name,
       bl.building_id,
       COUNT (ht.HOUSE_TYPE)
           OVER (PARTITION BY hl.BUILDING_ID, ht.HOUSE_TYPE)
           AS ALLOTED_HOUSE,
       DECODE (hl.ADVERTISE_YN,  'Y', 'ADVERTISED',  'N', 'NOT ADVERTISED')
           ADVERTISE_YN,
       bs.BUILDING_STATUS
  FROM HAS.HOUSE_LIST         hl,
       HAS.L_HOUSE_TYPE       ht,
       HAS.BUILDING_LIST      bl,
       HAS.L_BUILDING_STATUS  bs
 WHERE     hl.HOUSE_STATUS_ID = 1
       AND hl.ADVERTISE_YN = 'N'
       AND HL.HOUSE_TYPE_ID = HT.HOUSE_TYPE_ID
       AND HL.BUILDING_ID = BL.BUILDING_ID
       AND HL.HOUSE_TYPE_ID = BL.HOUSE_TYPE_ID
       AND BL.BUILDING_STATUS_ID = BS.BUILDING_STATUS_ID
       AND hl.DEPT_ACK_ID = $ack_id
ORDER BY 4";

        return DB::select($querys);
    }

    public function notifyEmp($adv_id, $dpt_id)
    {

        if($dpt_id)
        {
            $type_query = <<<QUERY
SELECT DISTINCT house_type_id
                              FROM HAS.HA_ADV_MST  hm,
                                   has.ha_adv_dtl  hd,
                                   HAS.HOUSE_LIST  hl
                             WHERE     hm.ADV_ID = hd.ADV_ID
                                   AND hd.HOUSE_ID = hl.HOUSE_ID
                                   AND hm.ADV_ID = $adv_id
                                   AND hm.DPT_DEPARTMENT_ID = $dpt_id
QUERY;

            $types = DB::select(DB::raw($type_query));
            foreach ($types as $type)
            {
                if($type->house_type_id == 11) {
                    $is_dor = true;
                    break;
                }
                else
                {
                    $is_dor = false;
                }
            }

            if($is_dor == false)
            {
                $querys = <<<QUERY
SELECT DISTINCT se.USER_ID
  FROM PMIS.EMPLOYEE e, CPA_SECURITY.SEC_USERS se
 WHERE     e.emp_id = se.EMP_ID
       AND e.EMP_GRADE_ID IN
               (SELECT EMP_GRADE_ID
                  FROM has.l_house_emp_grade_map gm
                 WHERE HOUSE_TYPE_ID IN
                           (SELECT DISTINCT house_type_id
                              FROM HAS.HA_ADV_MST  hm,
                                   has.ha_adv_dtl  hd,
                                   HAS.HOUSE_LIST  hl
                             WHERE     hm.ADV_ID = hd.ADV_ID
                                   AND hd.HOUSE_ID = hl.HOUSE_ID
                                   AND hm.ADV_ID = $adv_id
                                   AND hm.DPT_DEPARTMENT_ID = $dpt_id))
       AND e.EMP_ACTIVE_YN = 'Y'
       AND e.DPT_DEPARTMENT_ID = $dpt_id
       AND NOT EXISTS
               (SELECT DISTINCT emp_id
                  FROM has.house_allottment
                 WHERE     emp_id = e.EMP_ID
                       AND e.EMP_ID IS NOT NULL
                       AND ALLOT_YN = 'Y')
QUERY;
            }
            else
            {
                $querys = <<<QUERY
SELECT DISTINCT se.USER_ID
  FROM PMIS.EMPLOYEE e, CPA_SECURITY.SEC_USERS se
 WHERE     e.emp_id = se.EMP_ID
       AND e.EMP_GRADE_ID IN
               (SELECT EMP_GRADE_ID
                  FROM has.l_house_emp_grade_map gm
                 WHERE HOUSE_CATEGORY_ID = 3 or HOUSE_TYPE_ID IN
                           (SELECT DISTINCT house_type_id
                              FROM HAS.HA_ADV_MST  hm,
                                   has.ha_adv_dtl  hd,
                                   HAS.HOUSE_LIST  hl
                             WHERE     hm.ADV_ID = hd.ADV_ID
                                   AND hd.HOUSE_ID = hl.HOUSE_ID
                                   AND hm.ADV_ID = $adv_id
                                   AND hm.DPT_DEPARTMENT_ID = $dpt_id))
       AND e.EMP_ACTIVE_YN = 'Y'
       AND e.DPT_DEPARTMENT_ID = $dpt_id
       AND NOT EXISTS
               (SELECT DISTINCT emp_id
                  FROM has.house_allottment
                 WHERE     emp_id = e.EMP_ID
                       AND e.EMP_ID IS NOT NULL
                       AND ALLOT_YN = 'Y')
QUERY;

            }
        }
        else
        {
            $querys = <<<QUERY
SELECT DISTINCT se.USER_ID
  FROM PMIS.EMPLOYEE e, CPA_SECURITY.SEC_USERS se
 WHERE     e.emp_id = se.EMP_ID
       AND e.EMP_GRADE_ID IN
               (SELECT EMP_GRADE_ID
                  FROM has.l_house_emp_grade_map gm
                 WHERE HOUSE_TYPE_ID IN
                           (SELECT DISTINCT house_type_id
                              FROM HAS.HA_ADV_MST  hm,
                                   has.ha_adv_dtl  hd,
                                   HAS.HOUSE_LIST  hl
                             WHERE     hm.ADV_ID = hd.ADV_ID
                                   AND hd.HOUSE_ID = hl.HOUSE_ID
                                   AND hm.ADV_ID = $adv_id))
       AND e.EMP_ACTIVE_YN = 'Y'
       AND NOT EXISTS
               (SELECT DISTINCT emp_id
                  FROM has.house_allottment
                 WHERE     emp_id = e.EMP_ID
                       AND e.EMP_ID IS NOT NULL
                       AND ALLOT_YN = 'Y')
QUERY;
        }
        return DB::select(DB::raw($querys));
    }

    public function houseTypeChk($advMstId)
    {
        $house_type = DB::select('select hl.house_id from house_list hl, ha_adv_dtl had where hl.HOUSE_ID = had.house_id and hl.house_type_id in (1,2,3,4,
                                    5,
                                    10,11) and had.adv_id = '.$advMstId);

        if($house_type)
        {
            return 'abcd';
        }
        else
        {
            return 'efg';
        }
    }

    public function getDept($dpt_id)
    {
        return DB::selectOne('select department_id, department_name from pmis.l_department where department_id = '.$dpt_id);
    }

    public function getAdvertisementByHouseTypeForArray($houseType, $dpt_id)
    {
        $hType = '';
        foreach ($houseType as $type)
        {
            if($type == end($houseType)) {
                $hType .= $type;
            }
            else {
                $hType .= $type . ', ';
            }
        }
        if (isset($hType)){
            if ($hType == 6 or $hType== 7 or $hType == 9){
                $query = <<<QUERY
SELECT ham.adv_id adv_id, ham.adv_number adv_number FROM HA_ADV_MST ham
  LEFT JOIN HA_ADV_DTL had
    ON ham.adv_id = had.adv_id
  LEFT JOIN HOUSE_LIST hl
      ON had.house_id =  hl.house_id
  LEFT JOIN L_HOUSE_TYPE lht
    ON hl.house_type_id = lht.house_type_id
WHERE hl.house_type_id in ($hType)
AND ham.DPT_DEPARTMENT_ID is null
and ham.approved_yn = :approved
AND TRUNC (SYSDATE) BETWEEN TRUNC (ham.APP_START_DATE)
                               AND TRUNC (ham.APP_END_DATE)
GROUP BY ham.adv_id, ham.adv_number
QUERY;
            }else{
                $query = <<<QUERY
SELECT ham.adv_id adv_id, ham.adv_number adv_number FROM HA_ADV_MST ham
  LEFT JOIN HA_ADV_DTL had
    ON ham.adv_id = had.adv_id
  LEFT JOIN HOUSE_LIST hl
      ON had.house_id =  hl.house_id
  LEFT JOIN L_HOUSE_TYPE lht
    ON hl.house_type_id = lht.house_type_id
WHERE hl.house_type_id in ($hType)
AND ham.DPT_DEPARTMENT_ID = $dpt_id
and ham.approved_yn = :approved
AND TRUNC (SYSDATE) BETWEEN TRUNC (ham.APP_START_DATE)
                               AND TRUNC (ham.APP_END_DATE)
GROUP BY ham.adv_id, ham.adv_number
QUERY;
            }
        }

//        dd($query);
        $currentDate = new \DateTime();

        return DB::select($query, [
//            'dpt_department_id' => $dpt_id,
            'approved'=> 'Y'
            /*    'app_end_date' => $currentDate->format('Y-m-d')*/
        ]);

    }
}
