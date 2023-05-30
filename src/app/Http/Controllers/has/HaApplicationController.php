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
use App\Entities\Advertisement\AdvertisementMst;
use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaAppEmpFamily;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\HouseAllotment\LHouseStatus;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Pmis\Employee\EmpFamily;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Pmis\LApprovalWorkflows;
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

class HaApplicationController extends Controller
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
        $data = $this->haApplications();

        $loggedUserCode = (string) auth()->user()->user_name;

        return view('haapplication.index',compact('data', 'loggedUserCode'));
    }

    public function datatableList()
    {
        $user = auth()->id();

        $querys = "Select hp.application_id,application_date,emp_name,hp.emp_code,
        hp.emp_grade_id, ht.house_type, lg.grade_range, adv_number
        from ha_application  hp
        left join ha_adv_mst adm on hp.advertisement_id = adm.adv_id
        left join pmis.employee em on em.emp_id = hp.emp_id
        left join pmis.l_emp_grade lg on lg.emp_grade_id = em.EMP_GRADE_ID
        left join l_house_type ht on ht.house_type_id = hp.APPLIED_HOUSE_TYPE_ID
        where hp.insert_by = $user";
        $haApplications = DB::select($querys); //->with(['advertisement', 'employee', 'house_type']);

        return datatables()->of($haApplications)
            ->addColumn('application_date', function($query) {
                return Carbon::parse($query->application_date)->format('d-m-Y');
            })
            ->addColumn('action', function ($query) {
                /* if($query->workflow_process!=null){
                     $actionBtn = '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Approve"><i class="bx bx-check-circle cursor-pointer"></i></a>';
                     return $actionBtn;
                 }else{
                     $actionBtn = '<a href="javascript:void(0)" class="show-receive-modal workflowBtn" style="border: 1px solid #0D6AAD" title="Workflow">Workflow</a>';
                     return $actionBtn;
                 }*/
                // this line is used insted of of above commented code
                $actionBtn = '<a href="' . route('ha-application.edit', $query->application_id) . '"><i class="bx bx-show cursor-pointer"></i></a> ';
                return $actionBtn;
            })
            ->addIndexColumn()
            ->make();
    }

    public function view()
    {
    }

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

            $attachment = $request->file('attach_eligible');

            if (!isset($attachment)) {
                $attachmentFileName = '';
                $attachmentFileType = '';
                $attachmentFileContent = '';
            } else {
                $attachmentFileName = $attachment->getClientOriginalName();
                $attachmentFileType = $attachment->getMimeType();
                $attachmentFileContent = base64_encode(file_get_contents($attachment->getRealPath()));
                $eligible_promotion_date = date('Y-m-d',strtotime($information['p_ELIGABLE_PROMOTION_DATE']));
            }

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
                'p_ELIGABLE_PROMOTION_DATE' => isset($eligible_promotion_date)? $eligible_promotion_date :'',
                'p_ELIGABLE_EMP_GRADE_ID' => $information['p_ELIGABLE_EMP_GRADE_ID'],
                'p_ELIGABLE_ATTACHMENT' => [
                    'value' =>  $attachmentFileContent,
    //                        'type'  => \PDO::PARAM_LOB,
                    'type'  => SQLT_CLOB,
                ],
                'p_ELIGABLE_ATTACHMENT_TYPE' => $attachmentFileType,
                'p_ELIGABLE_ATTACHMENT_NAME' => $information['p_EMP_CODE'].' - '.$attachmentFileName,
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];

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
//                        'p_EMP_ID' => $information['p_EMP_ID'],
                        'o_status_code' => &$insertFlatStatusCode,
                        'o_status_message' => &$insertFLatStatusMessage
                    ];

                   DB::executeProcedure('PREFERENCE_HOUSE_ENTRY', $insertFlatParams);


                    if($insertFlatParams['o_status_code'] != 1) {
                        DB::rollBack();
                        return $insertFlatParams;
                    }
                }
            }

            DB::commit();

            //Send Notification
            $adv_name = AdvertisementMst::where('adv_id', $information['p_ADVERTISEMENT_ID'])->pluck('adv_number')->first();
            $notification_msg = 'A New Application for House Allotment has been Submitted. Please Review. (Employee Code: '.Auth::user()->user_name.' & Advertisement Name: '.$adv_name.')';
            $coEmpId = Employee::where('emp_code', Auth::user()->user_name)->pluck('reporting_officer_id')->first();
            $coUserId = DB::table('cpa_security.sec_users')->where('emp_id', $coEmpId)->pluck('user_id')->first();
            if($coUserId) {
                $controller_user_notification = [
                    "p_notification_to" => $coUserId,
                    "p_insert_by" => Auth::id(),
                    "p_note" => $notification_msg,
                    "p_priority" => null,
                    "p_module_id" => 14,
                    "p_target_url" => url('/point-assessments')
                ];
                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
            }

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

        $appliationData = '';
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
        $house_types = [];

        $familyDetails = '' ;//EmpFamily::select('*')
//            ->leftJoin('pmis.l_relation_type', 'pmis.l_relation_type.relation_type_id', '=', 'pmis.emp_family.relation_type_id')
//            ->get();

        if($id) {

            $haApplication =  HaApplication::find($id);

            $employeeInformation = $this->employeeManager->findEmployeeInformation($haApplication->emp_code);

            $advertisements = $this->advertisementManager->getAdvertisementByHouseType($employeeInformation['house_type_id'], $employeeInformation['department_id']);

            $house_types = LHouseType::where('house_type_id', $employeeInformation['eligible_id'])->get();

            if($employeeInformation['house_category_id'] == 3)
            {
                $advertisements = array_merge($advertisements, $this->advertisementManager->getAdvertisementByHouseType('11', $employeeInformation['department_id']));
                $house_types = LHouseType::where('house_type_id', '11')->orWhere('house_type_id', $employeeInformation['eligible_id'])->get();
            }
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
            'application' => $appliation,
            'house_types' => $house_types
        ];
//dd($data);
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
//        $information['p_APPLIED_HOUSE_TYPE_ID'] = $employeeInformation['house_type_id'];
        $information['p_APPLIED_HOUSE_TYPE_ID'] = $haApplication['house_type_id'];

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

        $information['p_ELIGABLE_PROMOTION_DATE'] = $haApplication['promo_date'];
        $information['p_ELIGABLE_EMP_GRADE_ID'] = $haApplication['eligible_grade_id'];

        return $information;
    }

    public function ajaxAdvertisementFlatData(Request $request, $adv_id,$house_type_id)
    {

        $houseList= $this->advertisementManager->findAvailableHouses($adv_id,$house_type_id);

    //dd($houseList[0]->building_name);
        $disdata='';

        if(!empty($houseList)){
            $disdata .= '<option value="">--- Choose ---</option>';
            foreach ($houseList as $data){
                $disdata.='<option value="'.$data->house_id.'">'.$data->house_name.'</option>';
            }
            echo $disdata;die;
        }else{
            echo '<option value="">--- Choose ---</option>';
        }

    }


}
