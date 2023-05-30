<?php

namespace App\Managers\Pmis\Employee;

use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Pmis\Employee\Employee;
use App\Enums\Department;
use App\Enums\Pmis\Employee\Statuses;
use App\Enums\YesNoFlag;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\DB;

class EmployeeManager implements EmployeeContract
{
    protected $employee;

    protected $auth;

    public function __construct(Employee $employee, Guard $auth)
    {
        $this->employee = $employee;
        $this->auth = $auth;
    }
//
//    public function findEmployeeCodesBy($searchTerm) {
//        return $this->employee->where(
//            [
//                ['emp_code', 'like', ''.$searchTerm.'%'],
//                ['emp_status_id', '=', Statuses::ON_ROLE],
//            ]
//        )->limit(10)->get('emp_code');
//    }

    public function findEmployeeCodesBy($searchTerm)
    {
        return $this->employee->where(
            [
                ['emp_code', 'like', '' . $searchTerm . '%'],
                ['emp_status_id', '=', Statuses::ON_ROLE],
            ]
        )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);
    }

    public function findEmployeeCodesByRepApproved()
    {
        /* Formatted on 11-23-2021 5:40:37 PM (QP5 v5.326) */
        return DB::select("
SELECT pem.emp_code, pem.emp_id, pem.emp_name
  FROM HAS.HOUSE_ALLOTTMENT         hall,
       PMIS.EMPLOYEE                pem,
       HAS.REPLACEMENT_APPLICATION  hra
 WHERE     pem.emp_id = hall.emp_id
       AND hall.allot_id = hra.allot_id
       AND hra.approved_yn = 'Y'");
    }


    public function findEmployeeInformation($employeeCode)
    {

        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
       to_char( emp.emp_join_date, 'dd-Mon-YYYY') as emp_join_date ,
       trunc(emp.EMP_LPR_DATE) emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       mar.MARITIAL_STATUS_ID maritial_status_id,
       gradesteps.BASIC_AMT current_basic,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       house.house_type_id house_type_id,
       house.house_category_id,
       house_type.house_type house_type_name,
       (house_type.house_type || ' - Type') eligible_for,
       (house_type.house_type_id ) eligible_id,
       (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =1 AND EMP_ID = emp.EMP_ID AND ROWNUM = 1)  emp_email,
       (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =2 AND EMP_ID = emp.EMP_ID AND ROWNUM = 1)  emp_mbl,
          emp.MERIT_POSITION
FROM
     pmis.EMPLOYEE emp
     LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
     LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
     LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
     LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
     LEFT JOIN pmis.L_MARITIAL_STATUS mar
        on emp.EMP_MARITIAL_STATUS_ID = mar.MARITIAL_STATUS_ID
     LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.ACTUAL_GRADE_ID = empgrade.EMP_GRADE_ID,
  pmis.L_GRADE_STEPS gradesteps,
  has.L_HOUSE_EMP_GRADE_MAP house,
  has.L_HOUSE_TYPE house_type
WHERE
  emp.emp_code = :emp_code
  AND emp.EMP_STATUS_ID IN (1,11,13)
  AND emp.ACTUAL_GRADE_ID = gradesteps.grade_id
  AND emp.GRADE_STEP_ID = gradesteps.GRADE_STEPS_ID
  AND emp.ACTUAL_GRADE_ID = house.emp_grade_id
  AND house.house_type_id = house_type.house_type_id
QUERY;

//        $employee = DB::selectOne($query, ['emp_code' => $employeeCode, 'emp_status_id' => Statuses::ON_ROLE]);
        $employee = DB::selectOne($query, ['emp_code' => $employeeCode]);
        //$employee = DB::selectOne($query);

//dd($employee,$query, $employeeCode);
        if ($employee) {

            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }


    public function findEmployeeWithAllottedHouseInformation($employeeCode)
    {
        $query = <<<QUERY
SELECT
  emp.emp_code emp_code,
  emp.emp_name emp_name,
  ha.allot_yn allot_yn
FROM
     pmis.EMPLOYEE emp
     LEFT JOIN has.HOUSE_ALLOTTMENT ha
        ON emp.emp_id = ha.emp_id
WHERE
  emp.emp_code = :emp_code
  AND emp.emp_gender_id = 1
  AND (ha.CANCEL_YN = 'N' OR ha.CANCEL_YN IS NULL)
QUERY;

        $employee = DB::selectOne($query, ['emp_code' => $employeeCode]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }


    public function findEmployeeBasicInformationWithAllocatedHouse($employeeCode)
    {
        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
       emp.EMP_JOIN_DATE emp_join_date,
       emp.EMP_LPR_DATE emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       H.HOUSE_CODE, H.HOUSE_NAME, H.HOUSE_NAME_BNG,
       BL.BUILDING_NAME,BL.BUILDING_NAME_BNG,
       HL.APPROVAL_DATE,
       HL.APPLICATION_ID,
       HL.ADVERTISEMENT_ID,
       HAM.ADV_NUMBER
FROM
    HOUSE_ALLOTTMENT HL
    LEFT JOIN HOUSE_LIST H
        ON h.house_id = HL.HOUSE_ID
    LEFT JOIN pmis.EMPLOYEE emp
        on HL.emp_id = emp.EMP_ID
    LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
    LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
    LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID
    LEFT JOIN BUILDING_LIST BL
        ON bl.BUILDING_ID = H.BUILDING_ID
    LEFT JOIN HA_ADV_MST HAM
        ON HAM.ADV_ID = HL.ADVERTISEMENT_ID

WHERE
    emp.emp_code = :emp_code
    AND emp.emp_status_id = :emp_status_id
    AND HL.cancel_yn = 'N'
QUERY;
        //emp.EMP_ID = '1912011709027753' , emp.EMP_STATUS_ID

        $employee = DB::selectOne($query, ['emp_code' => $employeeCode, 'emp_status_id' => Statuses::ON_ROLE]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    // Start replace
    public function findEmployeeBasicInformationWithReplacedAllocatedHouse($employeeCode)
    {

        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
       emp.EMP_JOIN_DATE emp_join_date,
       emp.EMP_LPR_DATE emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       H.HOUSE_CODE, H.HOUSE_NAME, H.HOUSE_NAME_BNG,
       BL.BUILDING_NAME,BL.BUILDING_NAME_BNG,
       HL.APPROVAL_DATE,
       HL.APPLICATION_ID,
       HL.ADVERTISEMENT_ID,
       HAM.ADV_NUMBER,
       CL.COLONY_NAME
FROM
    REPLACEMENT_APPLICATION RA
    left JOIN ALLOT_LETTER AL
    ON AL.REPLACE_APP_ID = RA.REPLACE_APP_ID
    LEFT JOIN HOUSE_ALLOTTMENT HL
        ON HL.ALLOT_ID = RA.ALLOT_ID
    LEFT JOIN HOUSE_LIST H
        ON h.house_id = RA.REPLACE_HOUSE_ID
    LEFT JOIN pmis.EMPLOYEE emp
        on HL.emp_id = emp.EMP_ID
    LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
    LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
    LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID
    LEFT JOIN BUILDING_LIST BL
        ON bl.BUILDING_ID = H.BUILDING_ID
    LEFT JOIN HA_ADV_MST HAM
        ON HAM.ADV_ID = HL.ADVERTISEMENT_ID
          LEFT JOIN L_COLONY CL ON CL.COLONY_ID = BL.COLONY_ID

WHERE
    emp.emp_code = :emp_code
    AND emp.emp_status_id = :emp_status_id
    AND HL.cancel_yn = 'N'
QUERY;

        //emp.EMP_ID = '1912011709027753' , emp.EMP_STATUS_ID

        $employee = DB::selectOne($query, ['emp_code' => $employeeCode, 'emp_status_id' => Statuses::ON_ROLE]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    // End replace

    public function findEmployeeBasicInformationWithAllocatedHouseWithLetterByAllotmentNo($allotmentNo)
    {
        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
       emp.EMP_JOIN_DATE emp_join_date,
       emp.EMP_LPR_DATE emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       H.HOUSE_CODE, H.HOUSE_NAME, H.HOUSE_NAME_BNG, H.DORMITORY_YN,
       BL.BUILDING_NAME,BL.BUILDING_NAME_BNG,
       HL.APPROVAL_DATE,
       HL.APPLICATION_ID,
       HL.ADVERTISEMENT_ID,
       HAM.ADV_NUMBER,
       AL.allot_letter_date,
       AL.allot_letter_id,
       AL.allot_letter_no,
       H.FLOOR_NUMBER,
       H.HOUSE_SIZE,
        H.WATER_TAP,
       C.COLONY_NAME,
       HT.HOUSE_TYPE,
       T.HOUSE_DETAILS,
       T.SANITARY_FITTINGS,
       T.ELECTRICAL_FITTINGS,
       T.TAKE_OVER_ID


FROM
    ALLOT_LETTER AL
    LEFT JOIN HOUSE_ALLOTTMENT HL
        ON AL.APPLICATION_ID = HL.APPLICATION_ID
    LEFT JOIN HOUSE_LIST H
        ON h.house_id = HL.HOUSE_ID
    LEFT JOIN pmis.EMPLOYEE emp
        on HL.emp_id = emp.EMP_ID
    LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
    LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
    LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID
    LEFT JOIN BUILDING_LIST BL
        ON bl.BUILDING_ID = H.BUILDING_ID
    LEFT JOIN HA_ADV_MST HAM
        ON HAM.ADV_ID = HL.ADVERTISEMENT_ID
     LEFT JOIN L_COLONY C
         ON H.COLONY_ID = C.COLONY_ID
     LEFT JOIN L_HOUSE_TYPE HT
         ON H.HOUSE_TYPE_ID = HT.HOUSE_TYPE_ID
     LEFT JOIN TAKE_OVER T
         ON T.TAKE_OVER_ID = HL.TAKE_OVER_ID

WHERE
    AL.ALLOT_LETTER_ID = :allot_letter_no
    AND emp.emp_status_id = :emp_status_id
    AND HL.cancel_yn = 'N'
QUERY;
        //emp.EMP_ID = '1912011709027753' , emp.EMP_STATUS_ID

        $employee = DB::selectOne($query, ['allot_letter_no' => $allotmentNo, 'emp_status_id' => Statuses::ON_ROLE]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    public function findReplacedEmployeeBasicInformationWithAllocatedHouseWithLetterByAllotmentNo($allotmentNo)
    {
        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
       emp.EMP_JOIN_DATE emp_join_date,
       emp.EMP_LPR_DATE emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       H.HOUSE_CODE, H.HOUSE_NAME, H.HOUSE_NAME_BNG,
       BL.BUILDING_NAME,BL.BUILDING_NAME_BNG,
       HL.APPROVAL_DATE,
       HL.APPLICATION_ID,
       HL.ADVERTISEMENT_ID,
       AL.allot_letter_date,
       AL.allot_letter_id,
       AL.allot_letter_no,
       H.FLOOR_NUMBER,
       H.HOUSE_SIZE,
        H.WATER_TAP,
       C.COLONY_NAME,
       HT.HOUSE_TYPE,
       T.HOUSE_DETAILS,
       T.SANITARY_FITTINGS,
       T.ELECTRICAL_FITTINGS,
       T.TAKE_OVER_ID


FROM
     REPLACEMENT_APPLICATION RA
    left JOIN ALLOT_LETTER AL
    ON AL.REPLACE_APP_ID = RA.REPLACE_APP_ID
    LEFT JOIN HOUSE_ALLOTTMENT HL
        ON HL.ALLOT_ID = RA.ALLOT_ID
    LEFT JOIN HOUSE_LIST H
        ON h.house_id = RA.REPLACE_HOUSE_ID
    LEFT JOIN pmis.EMPLOYEE emp
        on HL.emp_id = emp.EMP_ID
    LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
    LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
    LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID
    LEFT JOIN BUILDING_LIST BL
        ON bl.BUILDING_ID = H.BUILDING_ID
     LEFT JOIN L_COLONY C
         ON H.COLONY_ID = C.COLONY_ID
     LEFT JOIN L_HOUSE_TYPE HT
         ON H.HOUSE_TYPE_ID = HT.HOUSE_TYPE_ID
     LEFT JOIN TAKE_OVER T
         ON T.TAKE_OVER_ID = HL.TAKE_OVER_ID

WHERE
    -- T.TAKE_OVER_TYPE_ID != '1'    AND
    AL.ALLOT_LETTER_NO = :allot_letter_no
    AND emp.emp_status_id = :emp_status_id
    AND HL.cancel_yn = 'N'
QUERY;
        //emp.EMP_ID = '1912011709027753' , emp.EMP_STATUS_ID

        $employee = DB::selectOne($query, ['allot_letter_no' => $allotmentNo, 'emp_status_id' => Statuses::ON_ROLE]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }


    public function findReplacedEmployeeBasicInformationWithAllocatedHouseWithLetterByEmpCode($empCode)
    {
        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
       emp.EMP_JOIN_DATE emp_join_date,
       emp.EMP_LPR_DATE emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       H.HOUSE_ID,H.HOUSE_CODE, H.HOUSE_NAME, H.HOUSE_NAME_BNG,
       BL.BUILDING_NAME,BL.BUILDING_NAME_BNG,
       HL.APPROVAL_DATE,
       HL.APPLICATION_ID,
       HL.ADVERTISEMENT_ID,
        HL.old_entry_yn,
       AL.allot_letter_date,
       AL.allot_letter_id,
       AL.allot_letter_no,
       H.FLOOR_NUMBER,
       H.HOUSE_SIZE,
       C.COLONY_NAME,
       HT.HOUSE_TYPE,
       T.HOUSE_DETAILS,
       T.SANITARY_FITTINGS,
       T.ELECTRICAL_FITTINGS,
       T.TAKE_OVER_ID

FROM
    ALLOT_LETTER AL
    inner JOIN REPLACEMENT_APPLICATION RA
    ON AL.REPLACE_APP_ID = RA.REPLACE_APP_ID
    inner JOIN HOUSE_ALLOTTMENT HL
        ON RA.ALLOT_ID  = HL.ALLOT_ID
    LEFT JOIN HOUSE_LIST H
        ON h.house_id = HL.HOUSE_ID
    LEFT JOIN pmis.EMPLOYEE emp
        on HL.emp_id = emp.EMP_ID
    LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
    LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
    LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID
    LEFT JOIN BUILDING_LIST BL
        ON bl.BUILDING_ID = H.BUILDING_ID
     LEFT JOIN L_COLONY C
         ON H.COLONY_ID = C.COLONY_ID
     LEFT JOIN L_HOUSE_TYPE HT
         ON H.HOUSE_TYPE_ID = HT.HOUSE_TYPE_ID
     LEFT JOIN TAKE_OVER T
         ON T.TAKE_OVER_ID = HL.TAKE_OVER_ID

WHERE
     T.TAKE_OVER_TYPE_ID = '1'
    AND emp.emp_code = :emp_code
    AND emp.emp_status_id = :emp_status_id
    AND HL.cancel_yn = 'N'
QUERY;
        //emp.EMP_ID = '1912011709027753' , emp.EMP_STATUS_ID

        $employee = DB::selectOne($query, ['emp_code' => $empCode, 'emp_status_id' => Statuses::ON_ROLE]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    public function findEmployeeBasicInformationWithAllocatedOldHouseWithLetterByEmpCode($empId)
    {


        $query = <<<QUERY
SELECT emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (   TRUNC (MONTHS_BETWEEN (SYSDATE, emp.emp_dob) / 12)
        || ' years '
        || TRUNC (MOD (MONTHS_BETWEEN (SYSDATE, emp.emp_dob), 12))
        || ' months '
        || TRUNC (
                SYSDATE
              - ADD_MONTHS (
                   emp.emp_dob,
                     TRUNC (MONTHS_BETWEEN (SYSDATE, emp.emp_dob) / 12) * 12
                   + TRUNC (MOD (MONTHS_BETWEEN (SYSDATE, emp.emp_dob), 12))))
        || ' days')
          age,
       emp.EMP_JOIN_DATE emp_join_date,
       emp.EMP_LPR_DATE emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id)
          payscale,
          H.HOUSE_ID,
       H.HOUSE_CODE,
       H.HOUSE_NAME,
       H.HOUSE_NAME_BNG,
       H.DORMITORY_YN,
       BL.BUILDING_NAME,
       BL.BUILDING_NAME_BNG,
       HL.APPROVAL_DATE,
       HL.APPLICATION_ID,
       HL.ADVERTISEMENT_ID,
       HAM.ADV_NUMBER,
       H.FLOOR_NUMBER,
       H.HOUSE_SIZE,
       C.COLONY_NAME,
       HT.HOUSE_TYPE,
       HL.old_entry_yn
  FROM HOUSE_ALLOTTMENT HL
       --     LEFT JOIN HOUSE_ALLOTTMENT HL
       --         ON AL.APPLICATION_ID = HL.APPLICATION_ID
       LEFT JOIN HOUSE_LIST H ON h.house_id = HL.HOUSE_ID
       LEFT JOIN pmis.EMPLOYEE emp ON HL.emp_id = emp.EMP_ID
       LEFT JOIN pmis.L_DESIGNATION des
          ON emp.DESIGNATION_ID = des.DESIGNATION_ID
       LEFT JOIN pmis.L_DEPARTMENT dep
          ON emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
       LEFT JOIN pmis.L_DPT_SECTION sec
          ON emp.SECTION_ID = sec.DPT_SECTION_ID
       LEFT JOIN pmis.L_GENDER gen ON emp.EMP_GENDER_ID = gen.GENDER_ID
       LEFT JOIN pmis.L_EMP_GRADE empgrade
          ON emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID
       LEFT JOIN BUILDING_LIST BL ON bl.BUILDING_ID = H.BUILDING_ID
       LEFT JOIN HA_ADV_MST HAM ON HAM.ADV_ID = HL.ADVERTISEMENT_ID
       LEFT JOIN L_COLONY C ON H.COLONY_ID = C.COLONY_ID
       LEFT JOIN L_HOUSE_TYPE HT ON H.HOUSE_TYPE_ID = HT.HOUSE_TYPE_ID
 WHERE     emp.emp_id = :emp_id
       -- AND emp.emp_status_id = :emp_status_id
       AND HL.cancel_yn = 'N'
       AND HL.old_entry_yn = 'Y'
QUERY;


        //emp.EMP_ID = '1912011709027753' , emp.EMP_STATUS_ID

        $employee = DB::selectOne($query, ['emp_id' => $empId]); //, 'emp_status_id' => Statuses::ON_ROLE
//dd($employee);
        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    public function findEmployeeBasicInformationWithAllocatedHouseWithLetterByEmpCode($empId)
    {


        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
       emp.EMP_JOIN_DATE emp_join_date,
       emp.EMP_LPR_DATE emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       H.HOUSE_CODE, H.HOUSE_NAME, H.HOUSE_NAME_BNG, H.DORMITORY_YN,
       BL.BUILDING_NAME,BL.BUILDING_NAME_BNG,
       HL.APPROVAL_DATE,
       HL.APPLICATION_ID,
       HL.ADVERTISEMENT_ID,
       HAM.ADV_NUMBER,
       AL.allot_letter_date,
       AL.allot_letter_id,
       AL.allot_letter_no,
       H.FLOOR_NUMBER,
       H.HOUSE_SIZE,
       C.COLONY_NAME,
       HT.HOUSE_TYPE,
       T.HOUSE_DETAILS,
       T.SANITARY_FITTINGS,
       T.ELECTRICAL_FITTINGS,
       T.TAKE_OVER_ID,
       TO_CHAR(T.TAKE_OVER_DATE,'YYYY-MM-DD') as TAKE_OVER_DATE,
       T.CIVIL_ENG_COMMENT,
       T.ELEC_ENG_COMMENT,
       HL.old_entry_yn

FROM
    ALLOT_LETTER AL
    inner JOIN HOUSE_ALLOTTMENT HL
        ON AL.allot_letter_id = HL.allot_letter_id
--     LEFT JOIN HOUSE_ALLOTTMENT HL
--         ON AL.APPLICATION_ID = HL.APPLICATION_ID
    LEFT JOIN HOUSE_LIST H
        ON h.house_id = HL.HOUSE_ID
    LEFT JOIN pmis.EMPLOYEE emp
        on HL.emp_id = emp.EMP_ID
    LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
    LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
    LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID
    LEFT JOIN BUILDING_LIST BL
        ON bl.BUILDING_ID = H.BUILDING_ID
    LEFT JOIN HA_ADV_MST HAM
        ON HAM.ADV_ID = HL.ADVERTISEMENT_ID
     LEFT JOIN L_COLONY C
         ON H.COLONY_ID = C.COLONY_ID
     LEFT JOIN L_HOUSE_TYPE HT
         ON H.HOUSE_TYPE_ID = HT.HOUSE_TYPE_ID
     LEFT JOIN TAKE_OVER T
         ON HL.TAKE_OVER_ID = T.TAKE_OVER_ID

WHERE
    emp.emp_id = :emp_id
    -- AND emp.emp_status_id = :emp_status_id
    AND HL.cancel_yn = 'N'
    AND HL.old_entry_yn = 'N'
QUERY;


        //emp.EMP_ID = '1912011709027753' , emp.EMP_STATUS_ID

        $employee = DB::selectOne($query, ['emp_id' => $empId]);//, 'emp_status_id' => Statuses::ON_ROLE

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    public function findEmployeeListWhoHasHouseAllocated($searchTerm)
    {

        $query = <<<QUERY
            SELECT
            emp.emp_code emp_code,
            emp.emp_name emp_name,
            emp.EMP_STATUS_ID emp_status_id
            FROM
            HOUSE_ALLOTTMENT HL
            LEFT JOIN HOUSE_LIST H
            ON h.house_id = HL.HOUSE_ID
            LEFT JOIN pmis.EMPLOYEE emp
            on HL.emp_id = emp.EMP_ID

            WHERE
                emp.emp_code LIKE '$searchTerm%'
                AND emp.emp_status_id = :emp_status_id
                AND HL.CANCEL_YN = 'N'
QUERY;

        return DB::select($query, ['emp_status_id' => Statuses::ON_ROLE]);
    }


    public function employeeInfoWithAllottedHouse($employeeCode)
    {
// Previous query
//        SELECT
//  emp.emp_id emp_id,
//  emp.emp_code emp_code,
//  emp.emp_name emp_name,
//  emp.emp_grade_id emp_grade_id,
//  des.DESIGNATION designation,
//  dep.DEPARTMENT_NAME department,
//  sec.DPT_SECTION section,
//  hl.house_name house_name,
//  ha.ALLOT_ID allotment_id,
//  b.building_name
//FROM
//  pmis.EMPLOYEE emp
//    LEFT JOIN pmis.L_DESIGNATION des
//              on emp.DESIGNATION_ID = des.DESIGNATION_ID
//    LEFT JOIN pmis.L_DEPARTMENT dep
//              on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
//    LEFT JOIN pmis.L_DPT_SECTION sec
//              on emp.SECTION_ID = sec.DPT_SECTION_ID
//    INNER JOIN has.HOUSE_ALLOTTMENT ha ON emp.emp_id = ha.emp_id
//    INNER JOIN has.HOUSE_LIST hl ON ha.house_id = hl.house_id
//    left join building_list b on b.building_id = hl.building_id
//WHERE
//    emp.emp_code = :emp_code
//    AND emp.EMP_STATUS_ID = :emp_status_id
//    AND ha.allot_yn = 'Y'
//    AND ha.cancel_yn = 'N'

        /* Formatted on 10-04-2022 11:10:43 AM (QP5 v5.326) */
        $query = <<<QUERY
SELECT emp.emp_id              emp_id,
       emp.emp_code            emp_code,
       emp.emp_name            emp_name,
       emp.emp_grade_id        emp_grade_id,
       des.DESIGNATION         designation,
       dep.DEPARTMENT_NAME     department,
       sec.DPT_SECTION         section,
       ha.ALLOT_ID             allotment_id,
       hlt.house_name          house_name,
       ht.HOUSE_TYPE,
       b.building_name,
       b.BUILDING_ROAD_NO,
       lc.COLONY_NAME          residential_area
  FROM pmis.EMPLOYEE         emp,
       pmis.L_DESIGNATION    des,
       pmis.L_DEPARTMENT     dep,
       pmis.L_DPT_SECTION    sec,
       has.HOUSE_ALLOTTMENT  ha,
       has.HOUSE_LIST        hlt,
       HAS.L_HOUSE_TYPE      ht,
       has.building_list     b,
       HAS.L_COLONY          lc
 WHERE     emp.emp_id = ha.emp_id(+)
       AND emp.DESIGNATION_ID = des.DESIGNATION_ID(+)
       AND emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID(+)
       AND emp.SECTION_ID = sec.DPT_SECTION_ID(+)
       AND ha.house_id = hlt.house_id(+)
       AND hlt.HOUSE_TYPE_ID = ht.HOUSE_TYPE_ID(+)
       AND hlt.building_id = b.building_id(+)
       AND b.COLONY_ID = lc.COLONY_ID(+)
       AND emp.emp_code = :emp_code
       AND emp.EMP_STATUS_ID = :emp_status_id
       AND ha.allot_yn = 'Y'
       AND ha.cancel_yn = 'N'
QUERY;

        $employee = DB::selectOne($query, ['emp_code' => $employeeCode, 'emp_status_id' => Statuses::ON_ROLE]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }

    public function employeesWithAllottedHouses($employeeCode, $excludeEmpCodes = '')
    {
        $excludeEmpQuery = '';
        if ($excludeEmpCodes) {
            $inClauseValue = "'" . implode("', '", $excludeEmpCodes) . "'";
            $excludeEmpQuery = <<<EXCLUDED_EMP_CLAUSE
    AND emp.emp_code NOT IN ($inClauseValue)
EXCLUDED_EMP_CLAUSE;
        }

        $query = <<<QUERY
SELECT
  emp.emp_code emp_code
FROM
  pmis.EMPLOYEE emp
    LEFT JOIN pmis.L_DESIGNATION des
              on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
              on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
              on emp.SECTION_ID = sec.DPT_SECTION_ID
    INNER JOIN has.HOUSE_ALLOTTMENT ha ON emp.emp_id = ha.emp_id
    INNER JOIN has.TAKE_OVER tkovr ON ha.TAKE_OVER_ID = tkovr.TAKE_OVER_ID
    INNER JOIN has.HOUSE_LIST hl ON ha.house_id = hl.house_id
WHERE
    emp.emp_code LIKE :emp_code
  AND emp.EMP_STATUS_ID = :emp_status_id
  AND ha.allot_yn = 'Y'
  AND ha.cancel_yn = 'N'
  AND tkovr.TAKE_OVER_DATE < :old_take_over_date
  $excludeEmpQuery
  AND ROWNUM <= 10
ORDER BY emp.emp_code
QUERY;
        $oldTakeoverDate = new \DateTime();
        $oldTakeoverDate->sub(new \DateInterval('P3Y'));

        $employees = DB::select($query, ['emp_code' => $employeeCode . '%', 'emp_status_id' => Statuses::ON_ROLE, 'old_take_over_date' => $oldTakeoverDate]);

        if ($employees) {
            $jsonEncodedEmployees = json_encode($employees);
            $employeesArray = json_decode($jsonEncodedEmployees, true);

            return $employeesArray;
        }

        return [];
    }

    public function employeesWithAllottedHousesExcept($employeeCode, $exceptEmployeeCode)
    {
        $query = <<<QUERY
SELECT
  emp.emp_code emp_code
FROM
  pmis.EMPLOYEE emp
    LEFT JOIN pmis.L_DESIGNATION des
              on emp.DESIGNATION_ID = des.DESIGNATION_ID
    LEFT JOIN pmis.L_DEPARTMENT dep
              on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
    LEFT JOIN pmis.L_DPT_SECTION sec
              on emp.SECTION_ID = sec.DPT_SECTION_ID
    INNER JOIN has.HOUSE_ALLOTTMENT ha ON emp.emp_id = ha.emp_id
    INNER JOIN has.TAKE_OVER tkovr ON (ha.TAKE_OVER_ID = tkovr.TAKE_OVER_ID AND ha.emp_id = tkovr.emp_id)
    INNER JOIN has.HOUSE_LIST hl ON ha.house_id = hl.house_id
WHERE
    emp.emp_code LIKE :emp_code
  AND emp.emp_code != :except_emp_code
  AND emp.EMP_STATUS_ID = :emp_status_id
  AND ha.allot_yn = 'Y'
  AND ha.cancel_yn = 'N'
  AND tkovr.TAKE_OVER_DATE < :old_take_over_date
  AND ROWNUM <= 10
ORDER BY emp.emp_code
QUERY;
        $oldTakeoverDate = new \DateTime();
        $oldTakeoverDate->sub(new \DateInterval('P3Y'));

        $employees = DB::select($query, ['emp_code' => $employeeCode . '%', 'except_emp_code' => $exceptEmployeeCode, 'emp_status_id' => Statuses::ON_ROLE, 'old_take_over_date' => $oldTakeoverDate]);

        if ($employees) {
            $jsonEncodedEmployees = json_encode($employees);
            $employeesArray = json_decode($jsonEncodedEmployees, true);

            return $employeesArray;
        }

        return [];
    }

    public function findDepartmentalEmployees($department, $searchTerm)
    {
        $simpleWhereClause = [
            ['emp_status_id', '=', Statuses::ON_ROLE],
            ['emp_active_yn', '=', YesNoFlag::YES],
            ['dpt_department_id', '=', $department],
        ];

        return $this->employee->where($simpleWhereClause)
            ->where(function ($query) use ($searchTerm) {
                $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                    ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
            })
            ->limit(10)
            ->get(['emp_id', 'emp_code', 'emp_name']);
    }


    public function findDeptWiseEmployeeCodesBy($searchTerm, $empDept = null)
    {
        $empDeptArr = explode(',', $empDept);

        if (isset($empDept)) {   // department wise show employee code

            return $this->employee->where(
                [
                    ['emp_code', 'like', '' . $searchTerm . '%'],
                    ['emp_status_id', '=', Statuses::ON_ROLE],
                ]
            )->whereIn('dpt_department_id', $empDeptArr)->orWhere(
                [
                    ['emp_name', 'like', '' . $searchTerm . '%'],
                    ['emp_status_id', '=', Statuses::ON_ROLE],
                ]
            )->whereIn('dpt_department_id', $empDeptArr)->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);

        } else {  // to show all employee code

            return $this->employee->where(
                [
                    ['emp_code', 'like', '' . $searchTerm . '%'],
                    ['emp_status_id', '=', Statuses::ON_ROLE],
                ]
            )->orWhere(
                [
                    ['emp_name', 'like', '' . $searchTerm . '%'],
                    ['emp_status_id', '=', Statuses::ON_ROLE],
                ]
            )->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);

        }
    }

    public function findEmployeeInformationForEligible($employeeCode)
    {
        $query = <<<QUERY
SELECT
       emp.emp_id emp_id,
       emp.emp_code emp_code,
       emp.emp_name emp_name,
       emp.emp_type_id emp_type_id,
       emp.POST_TYPE_ID post_type_id,
       emp.EMP_STATUS_ID emp_status_id,
       emp.emp_confirmation_date emp_confirmation_date,
       des.DESIGNATION designation,
       des.DESIGNATION_ID,
       emp.DPT_DIVISION_ID dpt_division_id,
       dep.DEPARTMENT_NAME department,
       dep.department_id,
       sec.DPT_SECTION section,
       sec.DPT_SECTION_ID,
       emp.EMP_FATHER_NAME emp_father_name,
       emp.EMP_MOTHER_NAME emp_mother_name,
       emp.emp_dob emp_dob,
       (trunc(months_between(sysdate,emp.emp_dob)/12) || ' years ' || trunc(mod(months_between(sysdate,emp.emp_dob),12)) || ' months ' || trunc(sysdate-add_months(emp.emp_dob,trunc(months_between(sysdate, emp.emp_dob)/12)*12+trunc(mod(months_between(sysdate, emp.emp_dob),12)))) || ' days') age,
      to_char( emp.emp_join_date, 'dd-Mon-YYYY') as emp_join_date ,
       trunc(emp.EMP_LPR_DATE) emp_lpr_date,
       gen.GENDER_ID gender_id,
       gen.gender_name gender_name,
       mar.MARITIAL_STATUS_ID maritial_status_id,
       gradesteps.BASIC_AMT current_basic,
       empgrade.GRADE_RANGE grade_range,
       empgrade.EMP_GRADE_ID grade_id,
       (empgrade.GRADE_RANGE || ' - Grade ' || empgrade.emp_grade_id) payscale,
       house.house_type_id house_type_id,
       house.house_category_id,
       house_type.house_type house_type_name,
       (house_type.house_type || ' - Type') eligible_for,
       (house_type.house_type_id ) eligible_id,
       (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =1 AND EMP_ID = emp.EMP_ID AND ROWNUM = 1)  emp_email,
       (SELECT EMP_CONTACT_INFO FROM PMIS.EMP_CONTACTS WHERE EMP_CONTACT_TYPE_ID =2 AND EMP_ID = emp.EMP_ID AND ROWNUM = 1)  emp_mbl
FROM
     pmis.EMPLOYEE emp
     LEFT JOIN pmis.L_DESIGNATION des
       on emp.DESIGNATION_ID = des.DESIGNATION_ID
     LEFT JOIN pmis.L_DEPARTMENT dep
        on emp.DPT_DEPARTMENT_ID = dep.DEPARTMENT_ID
     LEFT JOIN pmis.L_DPT_SECTION sec
        on emp.SECTION_ID = sec.DPT_SECTION_ID
     LEFT JOIN pmis.L_GENDER gen
        on emp.EMP_GENDER_ID = gen.GENDER_ID
     LEFT JOIN pmis.L_MARITIAL_STATUS mar
        on emp.EMP_MARITIAL_STATUS_ID = mar.MARITIAL_STATUS_ID
     --LEFT JOIN pmis.L_EMP_GRADE empgrade
        --on emp.EMP_GRADE_ID = empgrade.EMP_GRADE_ID,
        LEFT JOIN pmis.L_EMP_GRADE empgrade
        on emp.ACTUAL_GRADE_ID = empgrade.EMP_GRADE_ID,
  pmis.L_GRADE_STEPS gradesteps,
  has.L_HOUSE_EMP_GRADE_MAP house,
  has.L_HOUSE_TYPE house_type
WHERE
  emp.emp_code = :emp_code
  AND emp.EMP_STATUS_ID IN (1,11,13)
  AND emp.EMP_GRADE_ID = gradesteps.grade_id
  AND emp.GRADE_STEP_ID = gradesteps.GRADE_STEPS_ID
  AND emp.actual_grade_id = house.emp_grade_id
  AND house.house_type_id = house_type.house_type_id
QUERY;

//        $employee = DB::selectOne($query, ['emp_code' => $employeeCode, 'emp_status_id' => Statuses::ON_ROLE]);
        $employee = DB::select($query, ['emp_code' => $employeeCode]);

        if ($employee) {
            $jsonEncodedEmployee = json_encode($employee);
            $employeeArray = json_decode($jsonEncodedEmployee, true);

            return $employeeArray;
        }

        return [];
    }


    public function findInterchangeFirstEmp($searchTerm)
    {
//        $query = DB::table("interchange_request as i")
//            ->leftJoin('house_allottment as h', 'h.emp_id', '=', 'i.req_from_allot_id')
//            ->leftJoin('pmis.employee as e', 'e.emp_id', '=', 'h.emp_id' )
//           ->where(
//                [
//                    [DB::raw('LOWER(e.emp_code)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
//                ]
//            )
//            ->select('e.emp_id','e.emp_code','e.emp_name')
//        ->get();


        $query = <<<QUERY
SELECT H.EMP_ID, E.EMP_CODE, E.EMP_NAME
  FROM INTERCHANGE_REQUEST I, HOUSE_ALLOTTMENT H, PMIS.EMPLOYEE E
 WHERE  NOT EXISTS (SELECT HI.FIRST_ALLOT_ID FROM  HAS.INTERCHANGE_APPLICATION HI
 WHERE  HI.FIRST_ALLOT_ID = REQ_FROM_ALLOT_ID)
 AND  I.REQ_FROM_ALLOT_ID = H.ALLOT_ID AND H.EMP_ID = E.EMP_ID
and e.emp_code LIKE :emp_code
QUERY;
        $employees = DB::select("$query",['emp_code' => $searchTerm .'%']);
        return $employees;
    }



    public function findInterchangesecondEmp($searchTerm)
    {

        $query = <<<QUERY
SELECT H.EMP_ID, E.EMP_CODE, E.EMP_NAME
  FROM INTERCHANGE_REQUEST I, HOUSE_ALLOTTMENT H, PMIS.EMPLOYEE E
 WHERE NOT EXISTS (select HI.SECOND_ALLOT_ID from  HAS.INTERCHANGE_APPLICATION hi
 where  HI.SECOND_ALLOT_ID = REQ_TO_ALLOT_ID)
 and I.REQ_TO_ALLOT_ID = H.ALLOT_ID AND H.EMP_ID = E.EMP_ID
and e.emp_code LIKE :emp_code
QUERY;
        $employees = DB::select("$query",['emp_code' => $searchTerm .'%']);
        return $employees;
    }

    public function allotedDepartmentEmployee($searchTerm,$department)
    {

        return $this->employee->where(
            [
               ['emp_status_id', '=', Statuses::ON_ROLE],
                ['dpt_department_id', '=', $department],
            ]
        )->where(function ($query) use ($searchTerm) {
            $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);


    }
    public function allotedAllEmployee($searchTerm)
    {

        return $this->employee->where(
            [
                ['emp_status_id', '=', Statuses::ON_ROLE],

            ]
        )->where(function ($query) use ($searchTerm) {
            $query->where(DB::raw('LOWER(employee.emp_name)'), 'like', strtolower('%' . trim($searchTerm) . '%'))
                ->orWhere('employee.emp_code', 'like', '' . trim($searchTerm) . '%');
        })->orderBy('emp_code', 'ASC')->limit(10)->get(['emp_id', 'emp_code', 'emp_name']);


    }
}
