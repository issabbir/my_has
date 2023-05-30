<?php

namespace App\Http\Controllers\has;

use App\Contracts\AdvertisementContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Admin\LDesignation;
use App\Entities\Admin\LFamilyMemberStatus;
use App\Entities\Admin\LGender;
use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Admin\LGeoThana;
use App\Entities\Admin\LMaritalStatus;
use App\Entities\Admin\LRelationType;
use App\Entities\Admin\SecUser;
use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaAppEmpFamily;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\HouseAllotment\LHouseStatus;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Pmis\Employee\EmpFamily;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Pmis\LApprovalWorkflows;
use App\Entities\Security\User;
use App\Enums\ModuleInfo;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;

class HandOverApplicationController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    private $employeeManager;

    private $advertisementManager;

    public function __construct(FlashMessageManager $flashMessageManager, EmployeeContract $employeeManager, AdvertisementContract $advertisementManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->employeeManager = $employeeManager;
        $this->advertisementManager = $advertisementManager;
    }

    public function index()
    {
        $auth_user = Auth::user()->emp_id;

        $old_chk = DB::selectOne("select H.OLD_ENTRY_YN from HAS.HOUSE_ALLOTTMENT h  where H.EMP_ID = $auth_user");
        $data = '';
        if (isset($old_chk->old_entry_yn)){
            if ($old_chk->old_entry_yn == 'N')
            {
                $query = <<<QUERY
SELECT d.EMP_CODE,
       d.EMP_ID,
       d.EMP_NAME,
       des.DESIGNATION,
       dpt.DEPARTMENT_NAME,
       bld.BUILDING_NAME,
       c.HOUSE_ID,
       c.HOUSE_NAME,
       c.dormitory_yn,
       c.FLOOR_NUMBER,
       c.house_size,
       col.COLONY_NAME,
       b.OLD_ENTRY_YN
  FROM HAS.TAKE_OVER         a,
       HAS.HOUSE_ALLOTTMENT  b,
       HAS.HOUSE_LIST        c,
       HAS.BUILDING_LIST     bld,
       HAS.L_COLONY          col,
       PMIS.EMPLOYEE         d,
       PMIS.L_DEPARTMENT     dpt,
       PMIS.L_DESIGNATION    des
 WHERE     a.ALLOT_LETTER_ID = b.ALLOT_LETTER_ID
       AND a.EMP_ID = b.EMP_ID
       AND b.HOUSE_ID = c.HOUSE_ID
       AND c.BUILDING_ID = bld.BUILDING_ID
       AND c.COLONY_ID = col.COLONY_ID
       AND a.EMP_ID = d.EMP_ID
       AND d.DPT_DEPARTMENT_ID = dpt.DEPARTMENT_ID
       AND d.DESIGNATION_ID = des.DESIGNATION_ID
       AND b.ALLOT_YN = 'Y'
       AND B.OLD_ENTRY_YN = 'N'
       AND d.EMP_ID = '$auth_user'
QUERY;
            }else{
                $query =    <<<Query
SELECT d.EMP_CODE,
       d.EMP_ID,
       d.EMP_NAME,
       des.DESIGNATION,
       dpt.DEPARTMENT_NAME,
       bld.BUILDING_NAME,
       c.HOUSE_ID,
       c.HOUSE_NAME,
       c.dormitory_yn,
       c.FLOOR_NUMBER,
       c.house_size,
       col.COLONY_NAME,
       B.OLD_ENTRY_YN
  FROM
       HAS.HOUSE_ALLOTTMENT  b,
       HAS.HOUSE_LIST        c,
       HAS.BUILDING_LIST     bld,
       HAS.L_COLONY          col,
       PMIS.EMPLOYEE         d,
       PMIS.L_DEPARTMENT     dpt,
       PMIS.L_DESIGNATION    des
 WHERE  b.HOUSE_ID = c.HOUSE_ID
       AND c.BUILDING_ID = bld.BUILDING_ID
       AND c.COLONY_ID = col.COLONY_ID
       AND b.EMP_ID = d.EMP_ID
       AND d.DPT_DEPARTMENT_ID = dpt.DEPARTMENT_ID
       AND d.DESIGNATION_ID = des.DESIGNATION_ID
       AND b.ALLOT_YN = 'Y'
       and B.OLD_ENTRY_YN = 'Y'
       AND d.EMP_id = '$auth_user'

Query;
            }
            $data = DB::select($query);
        }

        return view('handOverApplication.h-index',compact('data'));

    }

    public function handOverRequest(Request $request) {


        $attachment = $request->file('applicaton_doc');
        if (!isset($attachment)) {

            $attachmentFileName = '';
            $attachmentFileType = '';
            $attachmentFileContent = '';
        } else {
            $attachmentFileName = $attachment->getClientOriginalName();
            $attachmentFileType = $attachment->getMimeType();
            $attachmentFileContent = base64_encode(file_get_contents($attachment->getRealPath()));

        }

        DB::beginTransaction();
        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $params = [
                "p_HAND_OVER_REQUEST_ID" => [
                    "value" => '',
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],
                'p_EMP_ID' => $request->post('employee_id'),
                'p_HOUSE_ID' => $request->post('house_id'),
                'p_HAND_OVER_REASON' => $request->post('handover_reason'),
                'p_CPA_REQUEST_YN' => $request->post('cparequest_yn'),
                'p_APPLICATION_DATE' => $request->post('application_date'),
                "p_ATTACHMENT" => [
                    'value' => $attachmentFileContent,
                    'type' => SQLT_CLOB,
                ],
                "p_ATTACHMENT_NAME" => $attachmentFileName,
                "p_ATTACHMENT_TYPE" => $attachmentFileType,
                'p_INSERT_BY' => Auth::id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];

            DB::executeProcedure('hand_over_request_entry', $params);


            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            }
            DB::commit();

            $emp = Employee::where('emp_id', $request->post('employee_id'))->first();
            if($request->request_from == 'cpa')
            {
                //Notification to User for Cancellation
                $user_id = User::where('user_name', $request->employee_code)->pluck('user_id')->first();
                $cancel_msg = 'Your allotment has been canceled. Please, handover your allotted house!';
                $controller_user_notification = [
                    "p_notification_to" => $user_id,
                    "p_insert_by" => Auth::id(),
                    "p_note" => $cancel_msg,
                    "p_priority" => null,
                    "p_module_id" => 14,
                    "p_target_url" => url('/dashboard')
                ];
                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);


                $query = <<<QUERY
SELECT hl.house_name, lc.colony_name, bl.building_name
     FROM HOUSE_LIST hl, BUILDING_LIST bl, L_COLONY lc
    WHERE     hl.building_id = bl.building_id
          AND hl.colony_id = lc.colony_id
          AND hl.house_id = $request->house_id
QUERY;
                $house_data = DB::select($query);

                $handover_msg = 'CPA has cancelled allotment of House: '.$house_data[0]->house_name.', Building: '.$house_data[0]->building_name.' and Colony: '.$house_data[0]->colony_name.'. Please, take handover of this house. (User: '.$emp->emp_code.' Name: '.$emp->emp_name.')';
            }
            else
            {
                $handover_msg = 'A New Handover Application Needs to Review. ('.$emp->emp_code.')';
            }

            $send_to = DB::table('cpa_security.sec_users u')
                ->select('u.user_id', 'uwc.role_id')
                ->leftJoin('has.user_wise_colony uwc', 'uwc.emp_id', 'u.emp_id')
                ->leftJoin('has.house_list hl', 'hl.colony_id', 'uwc.colony_id')
                ->where('hl.house_id', $request->house_id)
                ->distinct()
                ->get();

            if ($send_to)
            {
                foreach ($send_to as $user) {
                    if ($user->role_id == 115) {
                        $controller_user_notification = [
                            "p_notification_to" => $user->user_id,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $handover_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => url('/civil-allottee_informationhand-over')
                        ];
                        DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                    }
                    else
                    {
                        $controller_user_notification = [
                            "p_notification_to" => $user->user_id,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $handover_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => url('/electric-hand-over')
                        ];
                        DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                    }
                }
            }

            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }



    public function view(){}

    public function store(Request $request)
    {
        $params = $this->haApplicationEntry($request);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function haApplicationEntry(Request $request, $id='')
    {
        $haApplication = $request->post();

        $haApplicationFamilies = isset($haApplication['family']) ? $haApplication['family'] : [];
        $preferenceFlat = isset($haApplication['avl_flat_id']) ? $haApplication['avl_flat_id'] : [];

        $employeeInformation = $this->employeeManager->findEmployeeInformation($haApplication['employee_code']);
        $information = $this->prepareData($haApplication, $employeeInformation);


        DB::beginTransaction();
        try {
            $p_application_id = $id ? $id : '';

            if($p_application_id) {
                $where = [
                    ['application_id', '=', $p_application_id]
                ];

                $availableHaApplicationFamilies = HaAppEmpFamily::where($where)->get();
                if($availableHaApplicationFamilies) {
                    foreach($availableHaApplicationFamilies as $availableHaApplicationFamily) {
                        $deleteFamilyStatusCode = sprintf("%4000s", "");
                        $deleteFamilyStatusMessage = sprintf('%4000s', '');
                        $deleteFamilyParams = [
                            'p_APP_FAMILY_ID' => $availableHaApplicationFamily->app_family_id,
                            'o_status_code' => &$deleteFamilyStatusCode,
                            'o_status_message' => &$deleteFamilyStatusMessage
                        ];

                        DB::executeProcedure('allotment.delete_app_family', $deleteFamilyParams);

                        if($deleteFamilyParams['o_status_code'] != 1) {
                            DB::rollBack();
                            return $deleteFamilyParams;
                        }
                    }
                }
            }

            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            $params = [
                "p_APPLICATION_ID" => [
                    "value" => &$p_application_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],
                'p_APPLICATION_DATE' => $information['p_APPLICATION_DATE'],
                'p_EMP_ID' => $information['p_EMP_ID'],
                'p_EMP_CODE' => $information['p_EMP_CODE'],
                'p_EMP_JOIN_DATE' => $information['p_EMP_JOIN_DATE'],
                'p_EMP_LPR_DATE' => $information['p_EMP_LPR_DATE'],
                'p_EMP_GENDER_ID' => $information['p_EMP_GENDER_ID'],
                'p_EMP_MARITIAL_STATUS_ID' => $information['p_EMP_MARITIAL_STATUS_ID'],
                'p_EMP_GRADE_ID' => $information['p_EMP_GRADE_ID'],
                'p_CURRENT_BASIC' => $information['p_CURRENT_BASIC'],
                'p_APPLIED_HOUSE_TYPE_ID' => $information['p_APPLIED_HOUSE_TYPE_ID'],
                'p_EMP_CONFIRMATION_DATE' => $information['p_EMP_CONFIRMATION_DATE'],
                'p_DPT_DIVISION_ID' => $information['p_DPT_DIVISION_ID'],
                'p_DPT_DEPARTMENT_ID' => $information['p_DPT_DEPARTMENT_ID'],
                'p_SECTION_ID' => $information['p_SECTION_ID'],
                'p_DESIGNATION_ID' => $information['p_DESIGNATION_ID'],
                'p_EMP_TYPE_ID' => $information['p_EMP_TYPE_ID'],
                'p_POST_TYPE_ID' => $information['p_POST_TYPE_ID'],
                'p_EMP_STATUS_ID' => $information['p_POST_TYPE_ID'],
                'p_HUSBAND_NAME' => $information['p_HUSBAND_NAME'],
                'p_HUSBAND_OCCUPATION' => $information['p_HUSBAND_OCCUPATION'],
                'p_HUSBAND_OCCUPATION_TYPE' => $information['p_HUSBAND_OCCUPATION_TYPE'],
                'p_HUSBAND_ORGANIZATION' => $information['p_HUSBAND_ORGANIZATION'],
                'p_HUSBAND_DESIGNATION' => $information['p_HUSBAND_DESIGNATION'],
                'p_HUSBAND_ORG_ADDRESS' => $information['p_HUSBAND_ORG_ADDRESS'],
                'p_HUSBAND_ORG_DIVISION' => $information['p_HUSBAND_ORG_DIVISION'],
                'p_HUSBAND_ORG_DISTRICT' => $information['p_HUSBAND_ORG_DISTRICT'],
                'p_HUSBAND_ORGA_THANA' => $information['p_HUSBAND_ORGA_THANA'],
                'p_HUSBAND_SALARY' => $information['p_HUSBAND_SALARY'],
                'p_HUSBAND_HOUSE_STATUS' => $information['p_HUSBAND_HOUSE_STATUS'],
                'p_INSERT_BY' => $information['p_INSERT_BY'],
                'p_HUSBAND_CPA_YN' => $information['p_HUSBAND_CPA_YN'],
                'p_HUSBAND_EMP_CODE' => $information['p_HUSBAND_EMP_CODE'],
                'p_ADVERTISEMENT_ID' => $information['p_ADVERTISEMENT_ID'],
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];
//dd($params);
            DB::executeProcedure('allotment.new_ha_app_entry', $params);

            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return $params;
            }

            if($haApplicationFamilies) {
                foreach($haApplicationFamilies as $haApplicationFamily) {
                    $p_family_id = '';
                    $insertFamilyStatusCode = sprintf("%4000s", "");
                    $insertFamilyStatusMessage = sprintf('%4000s', '');

                    $insertFamilyParams = [
                        'p_APP_FAMILY_ID' => [
                            "value" => &$p_family_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        'p_APPLICATION_ID' => $params['p_APPLICATION_ID'],
                        'p_MEMBER_NAME' => $haApplicationFamily['name'],
                        'p_MEMBER_NAME_BNG' => $haApplicationFamily['name_bng'],
                        'p_RELATION_TYPE_ID' => $haApplicationFamily['relation_type_id'],
                        'p_MEMBER_DOB' => $haApplicationFamily['dob'] ? date("Y-m-d", strtotime($haApplicationFamily['dob'])) : '',
                        'p_MEMBER_MOBILE' => $haApplicationFamily['mobile'],
                        'p_MEMBER_PHOTO'=> [
                            'value' => isset($haApplicationFamily['photo']) ? $haApplicationFamily['photo'] : '',
                            'type' => SQLT_CLOB,
                        ],
                        'p_INSERT_BY' => auth()->id(),
                        'p_EMP_ID' => $information['p_EMP_ID'],
                        'o_status_code' => &$insertFamilyStatusCode,
                        'o_status_message' => &$insertFamilyStatusMessage
                    ];

                    DB::executeProcedure('allotment.new_app_family_entry', $insertFamilyParams);

                    if($insertFamilyParams['o_status_code'] != 1) {
                        DB::rollBack();
                        return $insertFamilyParams;
                    }
                }
            }

            if($preferenceFlat) {

                foreach($preferenceFlat as $key => $haApplicationFlat) {

                    $p_Flat_id = '';
                    $insertFlatStatusCode = sprintf("%4000s", "");
                    $insertFLatStatusMessage = sprintf('%4000s', '');

                    $insertFlatParams = [
                        'p_APP_Flat_ID' => [
                            "value" => &$p_Flat_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        'p_APPLICATION_ID' => $params['p_APPLICATION_ID'],
                        'p_ADVERTISEMENT_ID' => $information['p_ADVERTISEMENT_ID'],
                        'p_HOUSE_ID' => $haApplicationFlat,
                        'p_EMP_ID' => $information['p_EMP_ID'],
                        'p_SEQ_ID' => $key+1,
                        'p_INSERT_BY' => auth()->id(),
                        'p_EMP_ID' => $information['p_EMP_ID'],
                        'o_status_code' => &$insertFlatStatusCode,
                        'o_status_message' => &$insertFLatStatusMessage
                    ];

                    DB::executeProcedure('PREFERENCE_HOUSE_ENTRY', $insertFlatParams);
//                    dd($insertFlatParams);
                    if($insertFlatParams['o_status_code'] != 1) {
                        DB::rollBack();
                        return $insertFlatParams;
                    }
                }
            }

            DB::commit();
            return $params;
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $exception->getMessage()];
            //return ["exception" => true, "class" => 'error', "message" => $exception->getMessage()];
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $this->haApplications($id);


        $loggedUserCode = (string) auth()->user()->user_name;

        return view('haapplication.index',compact('data','loggedUserCode','appliationData'));
    }

    public function update(Request $request, $id)
    {
        $params = $this->haApplicationEntry($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('ha-application.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function haApplications($id=null)
    {
        $haApplication = null;
        $employeeInformation = null;
        $advertisements = [];
        $maritalStatuses = LMaritalStatus::all();
        $relationships = LRelationType::all();
        $divisions = LGeoDivision::all();
        $districts = null;
        $thanas = null;
        $genders = LGender::all();
        $appliation = [];

        $familyDetails = EmpFamily::select('*')
            ->leftJoin('pmis.l_relation_type', 'pmis.l_relation_type.relation_type_id', '=', 'pmis.emp_family.relation_type_id')
            ->get();

        if($id) {

            $haApplication = HaApplication::find($id);

            $employeeInformation = $this->employeeManager->findEmployeeInformation($haApplication->emp_code);
            $advertisements = $this->advertisementManager->getAdvertisementByHouseType($employeeInformation['house_type_id']);
            $appliation = $this->advertisementManager->getapplicationHouse($haApplication->application_id);

            if($haApplication->husband_org_division) {
                $districts = LGeoDistrict::where('geo_division_id', $haApplication->husband_org_division)->get();
            }
            if($haApplication->husband_org_district) {
                $thanas = LGeoThana::where('geo_district_id', $haApplication->husband_org_district)->get();
            }
        }

        $data = [
            'haApplication' => $haApplication ? $haApplication : new HaApplication(),
            'employeeInformation' => $employeeInformation,
            'advertisements' => $advertisements,
            'marital_statuses' => $maritalStatuses,
            'relationships' => $relationships,
            'divisions' => $divisions,
            'genders' => $genders,
            'familyDetails' => $familyDetails,
            'application' => $appliation
        ];

        if($districts) {
            $data['districts'] = $districts;
        } else {
            $data['districts'] = null;
        }

        if($thanas) {
            $data['thanas'] = $thanas;
        } else {
            $data['thanas'] = null;
        }

        return  $data;
    }

    public function prepareData($haApplication, $employeeInformation)
    {
        $information = [];
        $information['p_APPLICATION_DATE'] = date("Y-m-d");

        $information['p_EMP_ID'] = $employeeInformation['emp_id'];
        $information['p_EMP_CODE'] = $employeeInformation['emp_code'];

        $information['p_EMP_JOIN_DATE'] = $employeeInformation['emp_join_date'] ? date("Y-m-d", strtotime($employeeInformation['emp_join_date'])) : '';
        $information['p_EMP_LPR_DATE'] = $employeeInformation['emp_lpr_date'] ? date("Y-m-d", strtotime($employeeInformation['emp_lpr_date'])) : '';

        $information['p_EMP_GENDER_ID'] = $employeeInformation['gender_id'];
        $information['p_EMP_MARITIAL_STATUS_ID'] = $employeeInformation['maritial_status_id'];
        $information['p_EMP_GRADE_ID'] = $employeeInformation['grade_id'];
        $information['p_CURRENT_BASIC'] = $employeeInformation['current_basic'];
        $information['p_APPLIED_HOUSE_TYPE_ID'] = $employeeInformation['house_type_id'];

        $information['p_EMP_CONFIRMATION_DATE'] = $employeeInformation['emp_confirmation_date'] ? date("Y-m-d", strtotime($employeeInformation['emp_confirmation_date'])) : '';

        $information['p_DPT_DIVISION_ID'] = $employeeInformation['dpt_division_id'];
        $information['p_DPT_DEPARTMENT_ID'] = $employeeInformation['department_id'];
        $information['p_SECTION_ID'] = $employeeInformation['dpt_section_id'];
        $information['p_DESIGNATION_ID'] = $employeeInformation['designation_id'];
        $information['p_EMP_TYPE_ID'] = $employeeInformation['emp_type_id'];
        $information['p_POST_TYPE_ID'] = $employeeInformation['post_type_id'];
        $information['p_EMP_STATUS_ID'] = $employeeInformation['emp_status_id'];

        $information['p_HUSBAND_NAME'] = isset($haApplication['husband_name']) ? $haApplication['husband_name'] : '';
        $information['p_HUSBAND_OCCUPATION'] = isset($haApplication['husband_occupation']) ? $haApplication['husband_occupation'] : '';
        $information['p_HUSBAND_OCCUPATION_TYPE'] = isset($haApplication['husband_occupation_type']) ? $haApplication['husband_occupation_type'] : '';
        $information['p_HUSBAND_ORGANIZATION'] = isset($haApplication['husband_organization']) ? $haApplication['husband_organization'] : '';
        $information['p_HUSBAND_DESIGNATION'] = isset($haApplication['husband_designation']) ? $haApplication['husband_designation'] : '';
        $information['p_HUSBAND_ORG_ADDRESS'] = isset($haApplication['husband_address']) ? $haApplication['husband_address'] : '';
        $information['p_HUSBAND_ORG_DIVISION'] = isset($haApplication['husband_org_division']) ? $haApplication['husband_org_division'] : '';
        $information['p_HUSBAND_ORG_DISTRICT'] = isset($haApplication['husband_org_district']) ? $haApplication['husband_org_district'] : '';
        $information['p_HUSBAND_ORGA_THANA'] = isset($haApplication['husband_orga_thana']) ? $haApplication['husband_orga_thana'] : '';
        $information['p_HUSBAND_SALARY'] = isset($haApplication['husband_salary']) ? $haApplication['husband_salary'] : '';
        $information['p_HUSBAND_HOUSE_STATUS'] = isset($haApplication['husband_house_status']) ? $haApplication['husband_house_status'] : '';

        $information['p_INSERT_BY'] = auth()->id();
        $information['p_HUSBAND_CPA_YN'] = isset($haApplication['husband_employee_of_cpa']) ? $haApplication['husband_employee_of_cpa'] : YesNoFlag::NO;
        $information['p_HUSBAND_EMP_CODE'] = isset($haApplication['husband_employee_code']) ? $haApplication['husband_employee_code'] : '';
        $information['p_ADVERTISEMENT_ID'] = $haApplication['advertisement_id'];
        $information['p_flat_id'] = $haApplication['avl_flat_id'];

        return $information;
    }

    public function ajaxAdvertisementFlatData(Request $request, $adv_id,$house_type_id){

    //        if($request->house_category_id && $request->house_category_id == 3)
    //        {
    //            $houseList= $this->advertisementManager->findAvailableHousesWithDor($adv_id,$house_type_id);
    //        }
    //        else
    //        {
                $houseList= $this->advertisementManager->findAvailableHouses($adv_id,$house_type_id);
    //        }

    //dd($houseList[0]->building_name);
        $disdata='';

        if(!empty($houseList)){
            $disdata .= '<option value="">--- Choose ---</option>';
            foreach ($houseList as $data){
                if($data->dormitory_yn == 'Y')
                {
                    $disdata.='<option value="' . $data->house_id . '">'. $data->colony_name .' (Colony) '. $data->building_name . ' (Building) ' . $data->house_name . ' (' . $data->house_code . ') (Dormitory)</option>';
                }
                else
                {
                    $disdata.='<option value="' . $data->house_id . '">' . $data->colony_name .' (Colony) '. $data->building_name . ' (Building) ' . $data->house_name . '</option>';
                }
            }
            echo $disdata;die;
        }else{
            echo '<option value="">--- Choose ---</option>';
        }
    }

    public function cpaIndex()
    {

        $auth_user = Auth::user()->emp_id;

        $query = <<<QUERY
SELECT e.emp_code, e.emp_name
  FROM pmis.employee e, has.house_allottment ha
 WHERE ha.EMP_ID = e.EMP_ID  AND ha.ALLOT_YN = 'Y'
 AND ha.HAND_OVER_ID IS NULL
QUERY;
        $data = DB::select($query);
//        dd($data);
        return view('handOverApplication.cpa-index',compact('data'));
    }


    public function datatableList()
    {
        $auth_user = Auth::user()->emp_id;

        $querys = "SELECT HR.*, H.HOUSE_NAME FROM HAND_OVER_REQUEST HR, HOUSE_LIST H
WHERE HR.HOUSE_ID = H.HOUSE_ID
AND HR.CPA_REQUEST_YN = 'N'
AND HR.EMP_ID = $auth_user ";
        $handoverReqs = DB::select($querys); //->with(['advertisement', 'employee', 'house_type']);

        return datatables()->of($handoverReqs)
            ->addColumn('document', function ($query) {
                if (!empty($query->attachment)){
                    return '<a  title="Document" href="' . route('hand-over-application.cpa-hand-over-request-document', $query->hand_over_request_id) .'"><i class="bx bx-download cursor-pointer"></i></a>';

                }else{
                    return 'No Document Found';
                }
            })
            ->escapeColumns('document')
            ->addIndexColumn()
            ->make(true);
    }

    public function cpadatalist()
    {

        $querys = <<<QUERY
SELECT HR.*,
       H.HOUSE_NAME,
       H.HOUSE_CODE,
       H.DORMITORY_YN,
       EI.EMP_CODE,
       BL.BUILDING_NAME,
       HT.HOUSE_TYPE_ID,
       HT.HOUSE_TYPE,
       LC.COLONY_ID,
       LC.COLONY_NAME
  FROM HAND_OVER_REQUEST  HR,
       HOUSE_LIST         H,
       EMPLOYEE_INFO      EI,
       BUILDING_LIST      BL,
       L_COLONY           LC,
       L_HOUSE_TYPE       HT
 WHERE     HR.HOUSE_ID = H.HOUSE_ID
       AND HR.CPA_REQUEST_YN = 'Y'
       AND EI.EMP_ID = HR.EMP_ID
       AND BL.BUILDING_ID = H.BUILDING_ID
       AND LC.COLONY_ID = H.COLONY_ID
       AND HT.HOUSE_TYPE_ID = H.HOUSE_TYPE_ID
QUERY;
        $handoverReqs = DB::select($querys); //->with(['advertisement', 'employee', 'house_type']);

        return datatables()->of($handoverReqs)

            ->addColumn('document', function ($query) {
                if (!empty($query->attachment)){
                    return '<a  title="Document" href="' . route('hand-over-application.cpa-hand-over-request-document', $query->hand_over_request_id) .'"><i class="bx bx-download cursor-pointer"></i></a>';

                }else{
                   return 'No Document Found';
                }
            })
            ->addColumn('house', function ($query) {
                if($query->dormitory_yn == 'Y') {
                    return $query->house_name . ' (' . $query->house_code . ')';
                }
                return $query->house_name;
            })
            ->addIndexColumn()
            ->escapeColumns('document')
            ->make(true);
    }

    public function download(Request $request, $id)
    {

        $data = DB::selectOne("SELECT * FROM hand_over_request  where hand_over_request_id  = $id");


        if($data) {
            if($data->attachment && $data->attachment_type && $data->attachment_name) {
                $content = base64_decode($data->attachment);

                return response()->make($content, 200, [
                    'Content-Type' => $data->attachment_type,
                    'Content-Disposition' => 'attachment; filename="'.$data->attachment_name.'"'
                ]);
            }
        }
    }


}
