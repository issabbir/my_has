<?php

namespace App\Http\Controllers\has;

use App\Entities\HouseAllotment\Acknowledgemnt;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaAdvDtl;
use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Pmis\Employee\Employee;
use App\Enums\Department;
use App\Http\Controllers\Controller;
use App\Mail\HouseApprove;
use App\Managers\FlashMessageManager;
use App\Managers\AdvertisementManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\Security\HasPermission;
use App\Helpers\HelperClass;
use Illuminate\Support\Facades\Mail;
use PhpParser\Node\Stmt\DeclareDeclare;

class AdvertisementController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager, AdvertisementManager $advertisementManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->advertisementManager = $advertisementManager;
    }

    public function index()
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
        $employee = DB::selectOne($query, ['emp_code' => '006113']);
//        dd($employee);
//        $emp_id = Auth::user()->emp_id;

//        if(Employee::where('emp_id', $emp_id)->pluck('designation_id')->first() == 30){
//            $ack_id = $this->advertisementManager->getAckCivil($emp_id);
//        }
//        else{
//            $ack_id = $this->advertisementManager->getAckHod($emp_id);
//        }

        return view('advertisement.index');
    }

    public function adAckValidity($ack_id)
    {
        $validity = $this->advertisementManager->getAckValidity($ack_id);

        return $validity;
    }

    public function store(Request $request,$id=null)
    {

        $params =[];
        $checked_house = [];
        $checked_house = $request->get('checked_house');

        if(isset($checked_house) == false) {
            return $params =  [  "exception" => true, "o_status_code" => false, "o_status_message" => 'Please Select a House '];
//            $flashMessageContent = $this->flashMessageManager->getMessage($params);
//            return redirect()->route('advertisement.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
        }
        DB::beginTransaction();

        try {
            $p_adv_mst_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

                    if($id){
                        $haAdvDtlRowCount = HaAdvDtl::where('adv_id','=',$id)->count(); //get()->
                        if($haAdvDtlRowCount > 0){
                            $deleteParams = [
                                "p_adv_id" => [
                                    "value" => &$p_adv_mst_id,
                                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                                    "length" => 255
                                ],
                                "o_status_code"         => &$statusCode,
                                "o_status_message"      => &$statusMessage
                            ];
                            DB::executeProcedure("allotment.delete_adv_detail", $deleteParams);
                        }else{
                            return [  "exception" => true, "o_status_code" => false, "o_status_message" => 'Sorry No House Found'];
                        }
                        if($deleteParams['o_status_code']){
                            $delete_status = true;
                        }else{
                            $delete_status = false;
                            DB::rollBack();
                            return $deleteParams;
                        }
                    }

                    //if(!isset($id)) {
                        $params = [
                            "p_adv_id" => [
                                "value" => &$p_adv_mst_id,
                                "type" => \PDO::PARAM_INPUT_OUTPUT,
                                "length" => 255
                            ],
                            "p_adv_number" => $request->get("advertisement_no"),
                            "p_adv_date" => date('Y-m-d', strtotime($request->get("publish_date"))),
                            "p_app_start_date" => date('Y-m-d', strtotime($request->get("application_start_date"))),
                            "p_app_end_date" => date('Y-m-d', strtotime($request->get("application_end_date"))),
                            "p_description" => $request->get("description"),
                            "p_active_yn" => ($request->get("active_yn")) ? $request->get("active_yn") : 'Y',
                            "p_description_bng" => $request->get("description_bang"),
//                            "p_dept_ack_id" => $request->get("ack_id"),
                            "P_FILE_NO" => $request->get("file_no"),
                            "P_DEPT_ID" => $request->get("department"),
                            "p_insert_by" => Auth()->ID(),
                            "o_status_code" => &$statusCode,
                            "o_status_message" => &$statusMessage
                        ];


                        DB::executeProcedure("allotment.advertise_entry", $params);

                        if ($params['o_status_code']) {
                            $p_adv_id = $params['p_adv_id']['value'];
                            $mst_status = true;
                        } else {
                            $mst_status = false;
                            DB::rollBack();
                            return [  "exception" => true, "o_status_code" => false, "o_status_message" => $params['o_status_message']];
                        }
                   // }

                if($id) {
                    $p_adv_id = $id;
                }

                $buildingList  = $request->get('buildingList');
                $checked_house_list = $request->get('checked_house');

                $emp_id = Auth::user()->emp_id;
                foreach (array_unique($buildingList) as $buildingKey => $buildingValue) {

                    if(Employee::where('emp_id', $emp_id)->pluck('designation_id')->first() == 30){ //for Civil Admin

                        if($request->house_type == 'abcd')
                        {
                            $h_type = LHouseType::whereNotIn('house_type_id', [6, 7, 9])->pluck('house_type_id');
                        }
                        else
                        {
                            $h_type = LHouseType::whereIn('house_type_id', [6, 7, 9])->pluck('house_type_id');
                        }
                    }else{
                        $h_type = LHouseType::whereNotIn('house_type_id', [6, 7, 9])->pluck('house_type_id');
                    }

                    foreach ($h_type as $type) {
                        $type_s = strval($type);

                        $checked_house = isset($checked_house_list[$buildingValue . '-' . $type_s]) ? $checked_house_list[$buildingValue . '-' . $type_s] : array();

                        if (count($checked_house) > 0) {

                            foreach ($checked_house as $checkedKey => $house) {

                                $p_adv_dtl_id = '';
                                $statusCode2 = sprintf("%4000s", "");
                                $statusMessage2 = sprintf('%4000s', '');
                                $params2 = [
                                    "p_adv_dtl_id" => [
                                        "value" => &$p_adv_dtl_id,
                                        "type" => \PDO::PARAM_INPUT_OUTPUT,
                                        "length" => 255
                                    ],
                                    "p_adv_id" => $p_adv_id,
                                    "p_house_id" => $house,
                                    "p_remarks" => '',
                                    "p_insert_by" => Auth()->ID(),
                                    "o_status_code" => &$statusCode2,
                                    "o_status_message" => &$statusMessage2
                                ];

                                DB::executeProcedure('allotment.advertise_detail_entry', $params2);

                                if ($params2['o_status_code'] != 1) {
                                    DB::rollBack();

                                    return [  "exception" => true, "o_status_code" => false, "o_status_message" => $params2['o_status_message']];
                                }

                            }
                        }
                    }
                }

                DB::commit();

                //Advertisement Notification
//                if ($params['o_status_code'] == 1)
//                {
//                    $this->sendNotification($params['p_adv_id']['value'], $params['p_adv_number']);
//                }

                if($id) {
                    return ["exception" => false, "o_status_code" => true, "o_status_message" => 'Update Successful'];
                }else{
                    $flashMessageContent = $this->flashMessageManager->getMessage($params);
                    return redirect()->route('advertisement.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
                    //return $params;
                }       }
        catch (\Exception $e) {
            return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function sendNotification($adv_id, $adv_no)
    {
        $ad_msg = 'A House Advertisement Has Been Published ('.$adv_no.'). You Are Eligible to Apply for a House.';
        $dpt_id = HaAdvMst::where('ADV_ID', $adv_id)->pluck('dpt_department_id')->first();
        $employees = $this->advertisementManager->notifyEmp($adv_id, $dpt_id);

        if($employees)
        {
            foreach ($employees as $emp) {
                $controller_user_notification = [
                    "p_notification_to" => $emp->user_id,
                    "p_insert_by" => Auth::id(),
                    "p_note" => $ad_msg,
                    "p_priority" => null,
                    "p_module_id" => 14,
                    "p_target_url" => url('/report/render?xdo=/~weblogic/HAS/RPT_ADVERTISEMENT_DETAILS_REPORT.xdo&p_advertise_id='.$adv_id.'&type=pdf&filename=advertisement_report')
                ];
                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
            }
        }
    }

//    public function datatableHouse($ack_id, $advMstId = null)
    public function datatableHouse($dpt_id, $house_type = null, $advMstId = null)
    {
//        dd($dpt_id, $house_type, $advMstId);
        $emp_id = Auth::user()->emp_id;

        if($advMstId){

            $houseList = $this->advertisementManager->getMstDatatableHouse($advMstId);
        }
        else{

            // if(Employee::where('emp_id', $emp_id)->pluck('designation_id')->first() == 30){
            if (Employee::where('emp_id', $emp_id)->pluck('dpt_department_id')->first() == 5) {
                if($house_type == 'abcd') //abcds
                {
                    $houseList = $this->advertisementManager->getHodDatatableHouse($dpt_id, $emp_id);
                }
                else
                {
                    $houseList = $this->advertisementManager->getCivilDatatableHouse();
                }

            } else{
                $houseList = $this->advertisementManager->getHodDatatableHouse($dpt_id, $emp_id);
            }

        }
//dd($houseList);
        $check = '';
        $buildingListWithHouse = ' <div class="table-responsive"><table class="table table-sm datatable mdl-data-table dataTable border" >
                                        <thead class="border">
                                            <th>Type</th>
                                            <th>Building Name</th>
                                            <th>Available House</th>
                                            <th>Building Status</th>
                                            <th>Colony Name</th>
                                            <th>Road No</th>
                                        </thead>
                                        <tbody>';

        if($advMstId){
            $buildingFoundStatus = 1;
        }
        else{
            $buildingFoundStatus = 0;
        }

        $allottedAvailableHouse = []; //newly introduced

            foreach ($houseList as $ix => $house){
                if($advMstId){
                    $flatList = $this->advertisementManager->getMstFlatList($house->building_id, $house->house_type_id, $advMstId);

                }
                else {
                    // if (Employee::where('emp_id', $emp_id)->pluck('designation_id')->first() == 30) {
                    if (Employee::where('emp_id', $emp_id)->pluck('dpt_department_id')->first() == 5) {
                        if($house_type == 'abcd') //abcds
                        {
                            $flatList = $this->advertisementManager->getHodFlatList($house->building_id, $house->house_type_id, $dpt_id, $emp_id,$house->building_status_id);

                        }
                        else
                        {
                            $flatList = $this->advertisementManager->getCivilFlatList($house->building_id, $house->house_type_id,$house->building_status_id);
                        }
                    } else {
                        $flatList = $this->advertisementManager->getHodFlatList($house->building_id, $house->house_type_id, $dpt_id, $emp_id,$house->building_status_id);
                    }
                }

                $show = false;
                if($house_type == 'abcd')
                {
                    if(isset($house->approved_yn)){
                        $show = true;
                    }else{
                        $show = false;
                    }
                }else{
                    $show = true;
                }

                if($show) {

                    if (count($flatList) >0) {

                        $buildingListWithHouse .= '<tr id="headingOne-' . $house->building_id . '" class="grayBackground">
                                                                <td>' . $house->house_type . '</td>
                                                                <td>
                                                                 <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne-' . $house->building_id . '" aria-expanded="true" aria-controls="collapseOne-' . $house->building_id . '">
                                                                 ' . $house->building_name . '</button></td>
                                                                <td >
                                                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne-' . $house->building_id . '" aria-expanded="true" aria-controls="collapseOne-' . $house->building_id . '">
                                                                 ' . $house->alloted_house . '</button></td></button></td> <!--$house->alloted_house-->
                                                                <td>' . $house->building_status . '</td>

                                                                <td>' . $house->colony_name . '</td>
                                                                <td>' . $house->building_road_no . '</td>
                                                        </tr>
                                                         <tr>
                                                                <td colspan="12" >
                                                                    <div id="collapseOne-' . $house->building_id . '"
                                                                    class="collapse ' . ($buildingFoundStatus == 1 ? 'show' : 'hide') . '"
                                                                     aria-labelledby="headingOne" data-parent="#accordionExample">';


                        $houseListOption = '<div>';
                        $i = 0;
//echo '&'.count($flatList).'<br>';
                        foreach ($flatList as $flat) {
                            $is_allotted = '';
                            $is_dormitory = '';

                            if ($flat->house_status_id == 2) {
                                $is_allotted = ' - Allotted';
                            }

                            if ($flat->dormitory_yn == 'Y') {
                                $is_dormitory = ' (' . $flat->house_code . ')(Dormitory)';
                            }

                            $showColumn = false;
                            if($house_type == 'abcd'){
                                if (isset($flat->obj) ) {
                                    $dataFlats = $this->advertisementManager->findWorkflowStepLimit($flat->obj);
                                }
                                if(isset($dataFlats[0]->finally_approved) ){ //must take the [0] index cause order by descending are used, only first data is usable
                                    $showColumn = true;

                                }else {
                                    //$houseListOption .= "<label class='text-info'>&nbsp;</label>";
                                    $showColumn = true;
                                    $houseListOption .= "&nbsp;";
                                }
                            }else{
                                $showColumn = true;
                            }

                            if($showColumn){
                                    if (($house->house_status_id == 1 && $flat->house_status_id == 1) || ($house->house_status_id == 2 && $flat->house_status_id == 2)) { //Building 'In Use' and house 'Allotted' in same row
                                        if ($advMstId) {
                                            if (HaAdvDtl::where('house_id', $flat->house_id)->where('adv_id', $advMstId)->exists()) {
                                                $check++;
                                                $houseListOption .= "<label class='btn btn-warning'><input type='checkbox' onclick='house_select_update(this)' checked name='checked_house[" . $house->building_id . "-" . $house->house_type_id . "][" . $i . "]' value='" . $flat->house_id . "' /> &nbsp; &nbsp;" . $flat->house_name . $is_dormitory . $is_allotted . "</label>&nbsp;";
                                            } else {
                                                $houseListOption .= "<label class='btn btn-warning'><input type='checkbox' onclick='house_select_update(this)' name='checked_house[" . $house->building_id . "-" . $house->house_type_id . "][" . $i . "]' value='" . $flat->house_id . "' /> &nbsp; &nbsp;" . $flat->house_name . $is_dormitory . $is_allotted . "</label>&nbsp;";
                                            }
                                        } else {
                                            $houseListOption .= "<label class='btn btn-warning'><input type='checkbox' onclick='house_select_update(this)' name='checked_house[" . $house->building_id . "-" . $house->house_type_id . "][" . $i . "]' value='" . $flat->house_id . "' /> &nbsp; &nbsp;" . $flat->house_name . $is_dormitory . $is_allotted . "</label>&nbsp;";
                                        }
                                        $i++;
                                    }
                            }

                        }
                        $houseListOption .= '</div>';
                        $buildingListWithHouse .= $houseListOption;
                        $buildingListWithHouse .= '</div>
                                                                </td>
                                                        </tr>';
                    }
                }

                $buildingListWithHouse .='<input type="hidden" name="buildingList[]" value="'.$house->building_id.'"  />';

            }

        $buildingListWithHouse .= '</tbody>
                             </table></div>';
        $buildingListWithHouse .='<input type="hidden" name="adv_id" id="adv_id" value="'.$advMstId.'"/><input type="hidden" name="total_checked" id="total_checked" value="'.$check.'"  />';


        return $buildingListWithHouse;
    }

    public function loadHouseListForAdvertisement($buildinId){
        $houseList = HouseList::select('house_id','HOUSE_NAME')
            ->where('HOUSE_STATUS_ID','=','1')
            ->where('BUILDING_ID','=', $buildinId)
            ->where('RESERVE_YN', '=','N')
            //->whereNotIn('')
            ->get();
        $houseListOtpion = '';//[];
        foreach ($houseList as $item) {
            $houseListOtpion .= "<span class='col-sm-2'><input type='checkbox' name='checked_house[]' value='".$item->house_id."' /> &nbsp; &nbsp;".$item->house_name." &nbsp; &nbsp;</span>";
        }
        $houseListOtpion .= "<input type='hidden' name='building' value='".$buildinId."' /> ";
        return ($houseListOtpion);

    }

    public function searchDatatableList($buildinId = null){
         $dpt_id = Employee::where('emp_id',Auth::user()->emp_id)->pluck('dpt_department_id')->first();

         if($dpt_id == 5){
             $register_data = DB::select("select m.adv_id,adv_number, adv_date,
                            app_start_date,
                            app_end_date,
                            (select count(house_id)  from ha_adv_dtl d where d.adv_id = m.adv_id) house_no, m.active_yn,
                            (select department_name from pmis.l_department ld where ld.department_id = m.dpt_department_id) department_name,
                             m.approved_yn,
                             m.workflow_process,
                             'advertisement' as prefix
                            from ha_adv_mst m ORDER BY m.ADV_ID DESC");
         }
         else{

             $register_data = DB::select("select m.adv_id,adv_number, adv_date,
                            app_start_date,
                            app_end_date,
                            (select count(house_id)  from ha_adv_dtl d where d.adv_id = m.adv_id) house_no, m.active_yn,
                            (select department_name from pmis.l_department ld where ld.department_id = m.dpt_department_id) department_name,
                             m.approved_yn,
                             m.workflow_process,
                             'advertisement' as prefix
                            from ha_adv_mst m where dpt_department_id = '$dpt_id' ORDER BY m.ADV_ID DESC");
         }

        return datatables()->of($register_data)
            ->addColumn('adv_date', function($register_data) {
                return Carbon::parse($register_data->adv_date)->format('Y-m-d');
            })
            ->addColumn('app_start_date', function($register_data) {
                return Carbon::parse($register_data->app_start_date)->format('Y-m-d');
            })
            ->addColumn('app_end_date', function($register_data) {
                return Carbon::parse($register_data->app_end_date)->format('Y-m-d');
            })
            ->addColumn('active_yn', function($register_data) {
                if($register_data->active_yn == 'Y'){
                    return 'Active';
                }else{
                    return 'Inactive';
                }
            })
            ->addColumn('department_name', function($register_data) {
                return $register_data->department_name;
            })
            ->addColumn('status', function($register_data) {


                $user = Auth::user();

                $advInactive = DB::selectOne("SELECT SP.*, P.PERMISSION_KEY
  FROM CPA_SECURITY.SEC_ROLE_PERMISSIONS SP,
       CPA_SECURITY.SEC_ROLE SR,
       CPA_SECURITY.SEC_USER_ROLES UR,
       CPA_SECURITY.SEC_PERMISSIONS P
 WHERE     SP.ROLE_ID = SR.ROLE_ID
       AND UR.ROLE_ID = SR.ROLE_ID
       AND P.PERMISSION_ID = SP.PERMISSION_ID
       AND P.PERMISSION_KEY = 'CAN_ADVERTISEMENT_INACTIVE'
       AND UR.USER_ID = '$user->user_id'");
                if (isset($advInactive)){
                    if ($advInactive->permission_key ){
                        if($register_data->active_yn == 'Y'){
                            return '<a href="javascript:void(0)" onclick="changeStatusConfirm('.$register_data->adv_id.')" class="badge badge-success text-white" title="Change Status">Active</a>';
                        }else{
                            return '<a href="javascript:void(0)" class="badge badge-danger" title="Change Status">In-active</a>';
                        }
                    }

                }
                else if($register_data->active_yn == 'Y'){
                    return '<a href="javascript:void(0)" class="badge badge-success text-white" title="Change Status">Active</a>';
                }else{
                    return '<a href="javascript:void(0)" class="badge badge-danger" title="Change Status">In-active</a>';
                }




            })
            ->addColumn('action', function ($register_data) {
                //return '<i class="bx bx-list-ul cursor-pointer buildingRow-'.$query->adv_id.'" onclick="displayPanel('.$query->adv_id.')"></i>';
                //return '<a href="' . route('advertisement.edit', $register_data->adv_id) . '" ><i class="bx bx-show cursor-pointer"></i></a>';

                if ($register_data->workflow_process != null) {
                    $actionBtn = '<a href="' . route('advertisement.edit', $register_data->adv_id) . '" ><i class="bx bx-show cursor-pointer"></i></a>';
                    if ($register_data->approved_yn != 'D') {
                        $hasWorkflowPermission = HelperClass::hasPermission($register_data->workflow_process,$register_data->prefix.$register_data->adv_id);

                        if($hasWorkflowPermission) {
                            $actionBtn .= '<a href="javascript:void(0)" class="ml-1 show-receive-modal approveBtn" title="Needs To Approve"><i class="bx bxs-right-arrow-circle cursor-pointer"></i></a>';
                        } else {
                            $actionBtn .= '<a href="javascript:void(0)" class="ml-1 show-receive-modal approveBtn" title="Approved"><i class="bx bx-check-circle cursor-pointer"></i></a>';
                        }
                    }
                    return $actionBtn;
                } else {
                    $actionBtn = '<a href="' . route('advertisement.edit', $register_data->adv_id) . '" ><i class="bx bx-show cursor-pointer"></i></a>';
                    if ($register_data->approved_yn != 'D') {
                        $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal workflowBtn" title="Assign Workflow"><i class="bx bx-sitemap cursor-pointer"></i></a>';
                    }

                    return $actionBtn;
                }
            })
            ->escapeColumns([])
            ->make(true);

    }

    public function searchAdvertisements(){

        return view('advertisement.search_advertisement');
    }

    public function loadHouseListForAdvertisementToedit($advMstId = null){

        $advMstData = HaAdvMst::select('*')->where('adv_id','=', $advMstId)->get();
        $house_type = $this->advertisementManager->houseTypeChk($advMstId);

        if($advMstData[0]->dpt_department_id)
        {
            $dept = $this->advertisementManager->getDept($advMstData[0]->dpt_department_id);
        }
        else
        {
            $dept = '';
        }

        $htmlForm = $this->datatableHouse($advMstData[0]->dpt_department_id, '', $advMstId);

        $data = [
            'htmlForm'       => $htmlForm,
            'advMstData'      => $advMstData,
//            'ackData'       => $ackData
            'house_type' => $house_type,
            'department' => $dept
        ];

        return view('advertisement.index',compact('data'));
    }

    public function update(Request $request, $id)
    {
        $params = $this->store($request, $id);

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($id){
            return redirect()->route('advertisement.edit',$id)->with($flashMessageContent['class'], $flashMessageContent['message']);
        }
    }

    public function changeStatus (Request $request, $id) {
        $adv_id = $id;

        $statusCode = sprintf("%4000s", "");
        $statusMessage = sprintf('%4000s', '');

        DB::beginTransaction();
        if($adv_id){
            $statusParams = [
                "p_adv_id" => [
                    "value" => &$adv_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],
                "p_INSERT_BY" => Auth()->ID(),
                "o_status_code"         => &$statusCode,
                "o_status_message"      => &$statusMessage
            ];

            DB::executeProcedure("HAS.ADVERTISEMENT_INACTIVE", $statusParams);

            DB::commit();
            $flashMessageContent = $this->flashMessageManager->getMessage($statusParams);
            return $flashMessageContent['message'];
            //return redirect()->route('advertisement.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
        }else{
            DB::rollBack();
            return [  "exception" => true, "o_status_code" => false, "o_status_message" => 'Sorry No advertisement Found'];
        }



    }
}
