<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/11/20
 * Time: 5:44 PM
 */

namespace App\Managers;


use App\Contracts\HouseContract;
use Illuminate\Support\Facades\DB;

class HouseManager implements HouseContract
{
    public function findAvailableHouses($gradeId)
    {
//        $query = <<<QUERY
//
//
//SELECT hl.house_id,
//       hl.house_name,
//       hl.house_name_bng,
//       lht.house_type_id,
//       lht.house_type,
//       lht.house_type_bng
//FROM HOUSE_LIST hl
//       LEFT JOIN L_HOUSE_TYPE lht ON hl.house_type_id = lht.house_type_id
//       LEFT JOIN L_HOUSE_EMP_GRADE_MAP lhegp ON lht.house_type_id = lhegp.house_type_id
//       LEFT JOIN REPLACEMENT_APPLICATION ra ON ra.REPLACE_HOUSE_ID = hl.HOUSE_ID
//WHERE hl.HOUSE_STATUS_ID = :house_status_id
//  AND ra.REPLACE_HOUSE_ID IS NULL
//  AND hl.ADVERTISE_YN = 'N'
//  AND hl.RESERVE_YN = 'N'
//  AND lhegp.emp_grade_id = :emp_grade_id
//QUERY;

        $query = <<<QUERY
SELECT lc.colony_name,
        bl.building_name,
        hl.house_id,
       hl.house_name,
       hl.house_name_bng,
       lht.house_type_id,
       lht.house_type,
       lht.house_type_bng
FROM HOUSE_LIST hl
       LEFT JOIN L_HOUSE_TYPE lht ON hl.house_type_id = lht.house_type_id
       LEFT JOIN L_HOUSE_EMP_GRADE_MAP lhegp ON lht.house_type_id = lhegp.house_type_id
       LEFT JOIN REPLACEMENT_APPLICATION ra ON ra.REPLACE_HOUSE_ID = hl.HOUSE_ID
       LEFT JOIN l_colony lc ON lc.colony_id = hl.colony_id
       LEFT JOIN building_list bl ON bl.building_id = hl.building_id
WHERE hl.HOUSE_STATUS_ID = :house_status_id
  AND ra.REPLACE_HOUSE_ID IS NULL
  AND hl.ADVERTISE_YN = 'N'
  AND hl.RESERVE_YN = 'N'
  AND lhegp.emp_grade_id = :emp_grade_id
QUERY;


        $conditions = ['emp_grade_id' => $gradeId, 'house_status_id' => 1];

        return DB::select($query, $conditions);
    }
}
