<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/9/20
 * Time: 12:55 PM
 */

namespace App\Managers;


use App\Contracts\HinterChangeApplicationContract;
use App\Enums\AppType;
use App\Enums\YesNoFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HinterChangeApplicationManager implements HinterChangeApplicationContract
{
    public function query()
    {
        $query = <<<QUERY
SELECT ia.int_change_id,
       ia.int_change_app_date,
       ia.FIRST_ALLOT_ID first_allot_id,
       first_employee.emp_code          first_employee_code,
       first_employee.emp_name          first_employee_name,
       first_employee_house.house_name  first_employee_house_name,
       ia.SECOND_ALLOT_ID second_allot_id,
       second_employee.emp_code         second_employee_code,
       second_employee.emp_name         second_employee_name,
       second_employee_house.house_name second_employee_house_name,
       ia.approved_by,
       ia.approved_date,
       ia.approved_yn,
       ia.WORKFLOW_PROCESS
FROM has.INTERCHANGE_APPLICATION ia
       INNER JOIN has.HOUSE_ALLOTTMENT first_allotment ON ia.FIRST_ALLOT_ID = first_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE first_employee ON first_employee.emp_id = first_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST first_employee_house ON first_allotment.house_id = first_employee_house.house_id
       INNER JOIN has.HOUSE_ALLOTTMENT second_allotment ON ia.SECOND_ALLOT_ID = second_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE second_employee ON second_employee.emp_id = second_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST second_employee_house ON second_allotment.house_id = second_employee_house.house_id
WHERE
       ia.APPROVED_YN = 'N'
       AND first_allotment.cancel_yn = 'N'
       AND second_allotment.cancel_yn = 'N'
QUERY;

        return DB::select($query);
    }

    public function approveQuery()
    {
        $query = <<<QUERY
SELECT
       'iaa' as prefix,
       ia.int_change_id,
       ia.int_change_app_date,
       ia.FIRST_ALLOT_ID first_allot_id,
       first_employee.emp_code          first_employee_code,
       first_employee.emp_name          first_employee_name,
       first_employee_house.house_name  first_employee_house_name,
       ia.SECOND_ALLOT_ID second_allot_id,
       second_employee.emp_code         second_employee_code,
       second_employee.emp_name         second_employee_name,
       second_employee_house.house_name second_employee_house_name,
       ia.approved_by approved_by,
       to_char(ia.approved_date, 'DD-MM-YYYY hh:mm:ss') approved_date,
       ia.approved_yn approved_yn
FROM has.INTERCHANGE_APPLICATION ia
       INNER JOIN has.HOUSE_ALLOTTMENT first_allotment ON ia.FIRST_ALLOT_ID = first_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE first_employee ON first_employee.emp_id = first_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST first_employee_house ON first_allotment.house_id = first_employee_house.house_id
       INNER JOIN has.HOUSE_ALLOTTMENT second_allotment ON ia.SECOND_ALLOT_ID = second_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE second_employee ON second_employee.emp_id = second_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST second_employee_house ON second_allotment.house_id = second_employee_house.house_id
WHERE
      ia.ACTIVE_YN = 'Y'
      AND first_allotment.cancel_yn = 'N'
      AND second_allotment.cancel_yn = 'N'
       AND (second_employee.reporting_officer_id =
                             (SELECT emp_id
                                FROM cpa_security.sec_users
                               WHERE user_id = :p_user_id)

        or
        'iaa' || TO_CHAR(ia.int_change_id) IN
(SELECT wp.workflow_object_id
                                    FROM pmis.workflow_process wp
                                         INNER JOIN pmis.workflow_steps ws
                                             ON (wp.workflow_step_id =
                                                 ws.workflow_step_id)
                                   WHERE (   (    ws.role_yn <> 'Y'
                                       AND ws.user_id = :p_user_id)
                                          OR (    ws.user_role IN
(SELECT r.role_key
                                                         FROM cpa_security.sec_users
                                                              su
                                                              INNER JOIN
                                                              cpa_security.sec_user_roles
                                                              ur
                                                                  ON (ur.user_id =
                                                                      su.user_id)
                                                              INNER JOIN
                                                              cpa_security.sec_role
                                                              r
                                                                  ON (r.role_id =
                                                                      ur.role_id)
                                                        WHERE     su.user_id =
                                                                  :p_user_id
AND r.role_key <>
'CONTROLING_OFFICER')
                                              AND  wp.workflow_object_id =
    'iaa'||TO_CHAR(ia.int_change_id))))

     )

QUERY;

        $conditions = ['p_user_id' => Auth::id()];

        return DB::select($query, $conditions);
    }

    public function interChangeAllotmentLetterQuery()
    {
        $query = <<<QUERY
SELECT ia.int_change_id,
       ia.int_change_app_date,
       ia.FIRST_ALLOT_ID first_allot_id,
       first_employee.emp_code          first_employee_code,
       first_employee.emp_name          first_employee_name,
       first_employee_house.house_name  first_employee_house_name,
       ia.SECOND_ALLOT_ID               second_allot_id,
       second_employee.emp_code         second_employee_code,
       second_employee.emp_name         second_employee_name,
       second_employee_house.house_name second_employee_house_name,
       ia.approved_by approved_by,
       to_char(ia.approved_date, 'DD-MM-YYYY hh:mm:ss') approved_date,
       ia.approved_yn approved_yn,
       AL.allot_letter_id,
       AL.allot_letter_date,
       AL.allot_letter_no,
       AL.memo_date,
       AL.memo_no,
       AL.delivery_yn,
       AL.ack_yn

FROM  HAS.ALLOT_LETTER AL
       LEFT JOIN has.INTERCHANGE_APPLICATION ia ON AL.INT_CHANGE_ID = ia.INT_CHANGE_ID
       INNER JOIN has.HOUSE_ALLOTTMENT first_allotment ON ia.FIRST_ALLOT_ID = first_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE first_employee ON first_employee.emp_id = first_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST first_employee_house ON first_allotment.house_id = first_employee_house.house_id
       INNER JOIN has.HOUSE_ALLOTTMENT second_allotment ON ia.SECOND_ALLOT_ID = second_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE second_employee ON second_employee.emp_id = second_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST second_employee_house ON second_allotment.house_id = second_employee_house.house_id
WHERE
       ia.ACTIVE_YN = 'Y'   -- ia.ACTIVE_YN = 'N'
       AND ia.APPROVED_YN = 'Y'
       AND first_allotment.cancel_yn = 'N'
       AND second_allotment.cancel_yn = 'N'
QUERY;

        return DB::select($query);
    }


    public function newInterChangeAllotmentLetterSearch($searchTerm)
    {
        $query = <<<QUERY
SELECT AL.allot_letter_no
FROM  HAS.ALLOT_LETTER AL
       LEFT JOIN has.INTERCHANGE_APPLICATION ia ON AL.INT_CHANGE_ID = ia.INT_CHANGE_ID
       INNER JOIN has.HOUSE_ALLOTTMENT first_allotment ON ia.FIRST_ALLOT_ID = first_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE first_employee ON first_employee.emp_id = first_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST first_employee_house ON first_allotment.house_id = first_employee_house.house_id
       INNER JOIN has.HOUSE_ALLOTTMENT second_allotment ON ia.SECOND_ALLOT_ID = second_allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE second_employee ON second_employee.emp_id = second_allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST second_employee_house ON second_allotment.house_id = second_employee_house.house_id
WHERE
       ia.ACTIVE_YN = 'Y' --  ia.ACTIVE_YN = 'N'
       AND ia.APPROVED_YN = 'Y'
       AND first_allotment.cancel_yn = 'N'
       AND second_allotment.cancel_yn = 'N'
       AND LOWER(al.allot_letter_no) LIKE :allot_letter_no
       AND al.app_type_id = :app_type_id
       AND al.delivery_yn = :delivery_yn
       AND al.application_id IS NULL
       AND al.replace_app_id IS NULL
       AND ROWNUM <= 10
QUERY;

        return DB::select($query, ['allot_letter_no' => strtolower('%'.trim($searchTerm).'%'), 'app_type_id' => AppType::INTERCHANGE, 'delivery_yn' => YesNoFlag::YES]);
    }

}
