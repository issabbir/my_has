<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/16/20
 * Time: 1:52 PM
 */

namespace App\Managers;


use App\Contracts\InterchangeTakeoverContract;
use Illuminate\Support\Facades\DB;

class InterchangeTakeoverManager implements InterchangeTakeoverContract
{
    public function findBy($allotmentNo)
    {
        $query = <<<QUERY
SELECT al.allot_letter_id allot_letter_id,
       first_employee.emp_id fe_id,
       first_employee.emp_code fe_code,
       first_employee.emp_name fe_name,
       fe_designation.designation fe_designation,
       fe_department.department_name fe_department_name,
       fe_section.dpt_section fe_section_name,
       first_house_list.house_name fe_house_name,
       first_house_list.house_size fe_house_size,
       first_house_list.FLOOR_NUMBER fe_floor_number,
       first_building_list.building_name fe_building_name,
       first_colony_list.colony_name fe_colony_name,
       first_house_type.house_type fe_house_type,
       first_take_over.HOUSE_DETAILS fe_house_details,
       first_take_over.SANITARY_FITTINGS fe_sanitary_fettings,
       first_take_over.ELECTRICAL_FITTINGS fe_electrical_fettings,
       second_employee.emp_id se_id,
       second_employee.emp_code se_code,
       second_employee.emp_name se_name,
       se_designation.designation se_designation,
       se_department.department_name se_department_name,
       se_section.dpt_section se_section_name,
       second_house_list.house_name se_house_name,
       second_house_list.house_size se_house_size,
       second_house_list.FLOOR_NUMBER se_floor_number,
       second_building_list.building_name se_building_name,
       second_colony_list.colony_name se_colony_name,
       second_house_type.house_type se_house_type,
       second_take_over.HOUSE_DETAILS se_house_details,
       second_take_over.SANITARY_FITTINGS se_sanitary_fettings,
       second_take_over.ELECTRICAL_FITTINGS se_electrical_fettings
FROM has.ALLOT_LETTER al
       INNER JOIN has.INTERCHANGE_APPLICATION ia ON ia.INT_CHANGE_ID = al.INT_CHANGE_ID
       LEFT JOIN has.HOUSE_ALLOTTMENT first_allotment ON (first_allotment.ALLOT_ID = ia.FIRST_ALLOT_ID AND first_allotment.cancel_yn = 'N')
       LEFT JOIN pmis.employee first_employee ON (first_employee.emp_id = first_allotment.EMP_ID AND first_employee.emp_status_id = 1)
       LEFT JOIN pmis.L_DESIGNATION fe_designation ON first_employee.designation_id = fe_designation.designation_id
       LEFT JOIN pmis.L_DEPARTMENT fe_department ON first_employee.dpt_department_id = fe_department.department_id
       LEFT JOIN pmis.L_DPT_SECTION fe_section ON first_employee.section_id = fe_section.dpt_section_id
       LEFT JOIN has.HOUSE_LIST first_house_list ON first_house_list.house_id = first_allotment.HOUSE_ID
       LEFT JOIN has.BUILDING_LIST first_building_list ON first_building_list.BUILDING_ID = first_house_list.BUILDING_ID
       LEFT JOIN has.L_COLONY first_colony_list ON first_colony_list.COLONY_ID = first_building_list.COLONY_ID
       LEFT JOIN has.L_HOUSE_TYPE first_house_type ON first_house_type.HOUSE_TYPE_ID = first_house_list.HOUSE_TYPE_ID
       LEFT JOIN has.TAKE_OVER first_take_over ON first_take_over.TAKE_OVER_ID = first_allotment.TAKE_OVER_ID
       LEFT JOIN has.HOUSE_ALLOTTMENT second_allotment ON (second_allotment.ALLOT_ID = ia.SECOND_ALLOT_ID AND second_allotment.cancel_yn = 'N')
       LEFT JOIN pmis.employee second_employee ON (second_employee.emp_id = second_allotment.EMP_ID AND first_employee.emp_status_id = 1)
       LEFT JOIN pmis.L_DESIGNATION se_designation ON second_employee.designation_id = se_designation.designation_id
       LEFT JOIN pmis.L_DEPARTMENT se_department ON second_employee.dpt_department_id = se_department.department_id
       LEFT JOIN pmis.L_DPT_SECTION se_section ON second_employee.section_id = se_section.dpt_section_id
       LEFT JOIN has.HOUSE_LIST second_house_list ON second_house_list.house_id = second_allotment.HOUSE_ID
       LEFT JOIN has.BUILDING_LIST second_building_list ON second_building_list.BUILDING_ID = second_house_list.BUILDING_ID
       LEFT JOIN has.L_COLONY second_colony_list ON second_colony_list.COLONY_ID = second_building_list.COLONY_ID
       LEFT JOIN has.L_HOUSE_TYPE second_house_type ON second_house_type.HOUSE_TYPE_ID = second_house_list.HOUSE_TYPE_ID
       LEFT JOIN has.TAKE_OVER second_take_over ON second_take_over.TAKE_OVER_ID = second_allotment.TAKE_OVER_ID
       WHERE al.ALLOT_LETTER_NO = :allot_letter_no
QUERY;

        $interchange = DB::selectOne($query, ['allot_letter_no' => $allotmentNo]);

        if($interchange) {
            $jsonEncodedInterchange = json_encode($interchange);
            $interchangeArray = json_decode($jsonEncodedInterchange, true);

            return $interchangeArray;
        }

        return [];
    }
}