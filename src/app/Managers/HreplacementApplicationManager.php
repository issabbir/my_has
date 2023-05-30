<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/9/20
 * Time: 12:55 PM
 */

namespace App\Managers;


use App\Contracts\HreplacementApplicationContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HreplacementApplicationManager implements HreplacementApplicationContract
{
    public function query()
    {
//        $query = <<<QUERY
//SELECT ra.replace_app_id,
//       ra.replace_app_date,
//       ra.ALLOT_ID allot_id,
//       employee.emp_code          employee_code,
//       employee.emp_name          employee_name,
//       employee_house.house_name  employee_house_name,
//       ra.approved_by,
//       ra.approved_date,
//       ra.approved_yn,
//       ra.WORKFLOW_PROCESS
//FROM has.REPLACEMENT_APPLICATION ra
//       INNER JOIN has.HOUSE_ALLOTTMENT allotment ON ra.ALLOT_ID = allotment.ALLOT_ID
//       INNER JOIN pmis.EMPLOYEE employee ON employee.emp_id = allotment.EMP_ID
//       INNER JOIN has.HOUSE_LIST employee_house ON allotment.house_id = employee_house.house_id
//WHERE
//       ra.APPROVED_YN = 'N'
//       AND allotment.cancel_yn = 'N'
//QUERY;

        $user = auth()->id();
        $query = <<<QUERY
SELECT ra.replace_app_id,
       ra.replace_app_date,
       ra.ALLOT_ID allot_id,
       employee.emp_code          employee_code,
       employee.emp_name          employee_name,
       employee_house.house_name  employee_house_name,
       ra.approved_by,
       ra.approved_date,
       ra.approved_yn,
       ra.WORKFLOW_PROCESS
FROM has.REPLACEMENT_APPLICATION ra
       INNER JOIN has.HOUSE_ALLOTTMENT allotment ON ra.ALLOT_ID = allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE employee ON employee.emp_id = allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST employee_house ON allotment.house_id = employee_house.house_id
WHERE
       ra.APPROVED_YN = 'N'
       AND allotment.cancel_yn = 'N'
       AND ra.insert_by = $user
QUERY;

        return DB::select($query);
    }

    public function approveQuery()
    {
        $query = <<<QUERY
SELECT 'raa' as prefix,
        ra.replace_app_id,
       ra.replace_app_date,
       ra.ALLOT_ID allot_id,
       emp.emp_code          employee_code,
       emp.emp_name          employee_name,
       employee_house.house_name  employee_house_name,
       ra.approved_by,
       to_char(ra.approved_date, 'DD-MM-YYYY hh:mm:ss') approved_date,
       ra.approved_yn
FROM has.REPLACEMENT_APPLICATION ra
       INNER JOIN has.HOUSE_ALLOTTMENT allotment ON ra.ALLOT_ID = allotment.ALLOT_ID
       INNER JOIN pmis.EMPLOYEE emp ON emp.emp_id = allotment.EMP_ID
       INNER JOIN has.HOUSE_LIST employee_house ON allotment.house_id = employee_house.house_id
WHERE allotment.cancel_yn = 'N' AND allotment.allot_yn = 'Y'
 AND (emp.reporting_officer_id =
                             (SELECT emp_id
                                FROM cpa_security.sec_users
                               WHERE user_id = :p_user_id)

        or
        'raa' || TO_CHAR(ra.replace_app_id) IN
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
                                              AND wp.workflow_object_id =
    'raa' || TO_CHAR(ra.replace_app_id))))

     )
QUERY;

        $conditions = ['p_user_id' => Auth::id()];

        return DB::select($query, $conditions);
    }
}
