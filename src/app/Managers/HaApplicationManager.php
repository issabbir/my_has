<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 3:45 PM
 */

namespace App\Managers;

use App\Contracts\HaApplicationContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HaApplicationManager implements HaApplicationContract
{
    public function findBy($advertisementId, $houseTypeId,$multiWorkflowId=1)
    {
        if($multiWorkflowId ==1){
            $query = <<<QUERY
select
       'haa' as prefix,
       ha.application_id application_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       dep.department_name department_name,
       des.designation designation,
       leg.GRADE_RANGE,
       emp.EMP_DOB,
       emp.EMP_JOIN_DATE,
       ha.ELIGABLE_PROMOTION_DATE,
       (ap.tot_point - ap.female_point) point_from_promo_date,
       ap.female_point,
       to_char(ha.application_date, 'DD-MM-YYYY') application_date,
       ap.tot_point tot_point,
       to_char(hall.take_over_date, 'DD-MM-YYYY') take_over_date,
       hl.house_name,
       ap.approve_yn,
       ap.final_approve_yn,
       ha.workflow_process,
	   ha.approved_yn
from has.ha_application ha
  inner join pmis.employee emp on emp.emp_id = ha.emp_id
  inner join has.allot_point ap on ha.application_id = ap.application_id
  left join pmis.l_designation des on emp.designation_id = des.designation_id
  left join pmis.l_department dep on emp.dpt_department_id = dep.department_id
  left join has.house_allottment hall on hall.application_id = ap.application_id
  left join has.house_list hl on hl.house_id = hall.house_id
  left join pmis.l_emp_grade leg on emp.actual_grade_id = leg.EMP_GRADE_ID
  where ha.advertisement_id = :advertisement_id
   and ha.applied_house_type_id = :applied_house_type_id
   --and(hall.take_over_id is null and hall.hand_over_id is null)
   and hall.hand_over_id is null

AND ha.workflow_process is null

AND (emp.reporting_officer_id =
                             (SELECT emp_id
                                FROM cpa_security.sec_users
                               WHERE user_id = :p_user_id)

        or
        'haa' || TO_CHAR(ha.application_id) IN
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
    'haa'||TO_CHAR(ha.application_id))))

     )
  order by ap.tot_point desc

QUERY;


        }else {


            $query = <<<QUERY
select
       'haa' as prefix,
       ha.application_id application_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       dep.department_name department_name,
       des.designation designation,
       leg.GRADE_RANGE,
       emp.EMP_DOB,
       emp.EMP_JOIN_DATE,
       ha.ELIGABLE_PROMOTION_DATE,
       (ap.tot_point - ap.female_point) point_from_promo_date,
       ap.female_point,
       to_char(ha.application_date, 'DD-MM-YYYY') application_date,
       ap.tot_point tot_point,
       to_char(hall.take_over_date, 'DD-MM-YYYY') take_over_date,
       hl.house_name,
       ap.approve_yn,
        ap.final_approve_yn,
       ha.workflow_process,
	   ha.approved_yn
from has.ha_application ha
  inner join pmis.employee emp on emp.emp_id = ha.emp_id
  inner join has.allot_point ap on ha.application_id = ap.application_id
  left join pmis.l_designation des on emp.designation_id = des.designation_id
  left join pmis.l_department dep on emp.dpt_department_id = dep.department_id
  left join has.house_allottment hall on hall.application_id = ap.application_id
  left join has.house_list hl on hl.house_id = hall.house_id
  left join pmis.l_emp_grade leg on emp.emp_grade_id = leg.EMP_GRADE_ID
  where ha.advertisement_id = :advertisement_id
   and ha.applied_house_type_id = :applied_house_type_id
   --and(hall.take_over_id is null and hall.hand_over_id is null)
   and hall.hand_over_id is null

AND ha.workflow_process is NOT null

AND (emp.reporting_officer_id =
                             (SELECT emp_id
                                FROM cpa_security.sec_users
                               WHERE user_id = :p_user_id)

        or
        'haa' || TO_CHAR(ha.application_id) IN
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
    'haa'||TO_CHAR(ha.application_id))))

     )
  order by ap.tot_point desc

QUERY;
        }

        $conditions = ['advertisement_id' => $advertisementId, 'applied_house_type_id' => $houseTypeId, 'p_user_id' => Auth::id()];

		return DB::select($query, $conditions);
    }
}
