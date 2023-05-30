<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/2/20
 * Time: 5:26 PM
 */

namespace App\Http\Controllers\has;

use App\Contracts\AdvertisementContract;
use App\Contracts\HaApplicationContract;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Pmis\WorkFlowProcess;
use App\Entities\Pmis\WorkFlowStep;
use App\Entities\Security\SecUserRoles;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\Security\HasPermission;
use App\Helpers\HelperClass;
use Illuminate\Support\Facades\Log;

class PointAssessmentController extends Controller
{
    use HasPermission;

    private $flashMessageManager;
    private $advertisementManager;
    private $applicationManager;

    public function __construct(FlashMessageManager $flashMessageManager, AdvertisementContract $advertisementManager, HaApplicationContract $applicationManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->advertisementManager = $advertisementManager;
        $this->applicationManager = $applicationManager;
    }

    public function index(Request $request)
    {
        //$loggedUserRoleKeyList = Auth::user()->roles->pluck('role_key');
        $loggedUserEFG_PermissionChecked = Auth::user()->hasPermission('HAS_CAN_APPROVE_EFG');

        $data = $this->pointAssessments($request);

        if($request){
            $checked_advertisement_for_efg = $request->get('is_efg');
            $advertisementId = $request->get('advertisement_id');
            $houseTypeId = $request->get('house_type_id');
            $approvalProcessTypeId = $request->get('approval_process_type_id');
            $multiWorkflowId = $request->get('multi_workflow_id');

            $data['checked_advertisement_for_efg'] = $checked_advertisement_for_efg?$checked_advertisement_for_efg:'';
            $data['advertisement_id'] = $advertisementId?$advertisementId:'';
            $data['house_type_id'] = $houseTypeId?$houseTypeId:'';
            $data['houseTypes'] = null;
            $data['approval_process_type_id'] = $approvalProcessTypeId?$approvalProcessTypeId:'';
            $data['multi_workflow_id'] = $multiWorkflowId?$multiWorkflowId:'';

            if($advertisementId) {
                $data['houseTypes'] = $this->advertisementManager->findHouseTypesByAdvertisementId($advertisementId);
                /*if(Auth::user()->hasPermission('HAS_HOD_CAN_ADVERTISE_A_D')){
                    $alowedHouseTypeArray = (array)json_decode(env('HOD_ALLOWED_HOUSE_TYPE'));
                    if(1!=HelperClass::custom_array_search($houseType->house_type, $alowedHouseTypeArray, 'bool')){
                        continue;
                    }
                }*/
            }
        }
        return view('pointassessment.index',compact('data','loggedUserEFG_PermissionChecked'));
    }

    public function generateAdvertisementOptions($advertisements,$selected=null){
        $options = '<option value="">--Please Select--</option>';
        foreach($advertisements['advertisements'] as $advertisement){
            $options .= "<option value='".$advertisement->adv_id."' ".($selected == $advertisement->adv_id?'selected':'').">".$advertisement->adv_number."</option>";
        }
        return $options;
    }

    public function user_role_approval($application_id, $user_id,$workflow_id)
    {
        // Log::info($application_id .' '. $user_id.' '.$workflow_id);
        $step = WorkFlowProcess::where('workflow_object_id', (string)'haa' . $application_id)->orderBy('workflow_process_id', 'desc')->first();
       // Log::info('Deb#1'.$step);
       /* if ($step != null) {
            $next_step = WorkFlowStep::where('approval_workflow_id', $step->workflowStep->approval_workflow_id)
                ->where('process_step', '>', $step->workflowStep->process_step)
                ->orderBy('process_step', 'asc')->first();
            // Log::info('Deb#2'.'<br>'. $next_step);
        } else {
            $next_step = WorkFlowStep::where('approval_workflow_id', $workflow_id )
                ->where('process_step', '=', '1')->first();
            // Log::info('Deb#3');
        }
        if ($next_step){
            $workflowRole = $next_step->user_role;
            //Log::info('Deb#4');
            $userRole = SecUserRoles::with('role')
                ->where('user_id', $user_id)
                ->whereHas('role', function ($q) use ($workflowRole) {
                    $q->where('role_key', 'like', $workflowRole);
                })->first();
            return ['user_role' => $userRole, 'next_step' => $next_step->process_step];

        }else{
            //Log::info('Deb#5');
            return ['user_role' => null, 'next_step' => null];
        }
        */ //->where('workflow_step_id','<>',null)
        $loggedUserRoleKeyList = Auth::user()->roles->pluck('role_key');
        $loggedUserRoleKeyLowerArray = json_decode(strtolower(json_encode($loggedUserRoleKeyList)));

        if ($step != null) {
            if ($step->workflowStep != null) {
                $workflowRole = $step->workflowStep->user_role?$step->workflowStep->user_role:'';
                $next_step_process = $step->workflowStep->process_step;
            }else{
                $workflowRole = '';
                $next_step_process = 1;
            }
        } else {
            $next_step = WorkFlowStep::where('approval_workflow_id', $workflow_id )->where('process_step', '=', '1')->first();
            $workflowRole = $next_step->user_role;
            $next_step_process = $next_step->process_step;
        }
        if ($workflowRole){
            if(in_array(strtolower($workflowRole),$loggedUserRoleKeyLowerArray)){
                $isUserHasAccess = true;
            }else{
                $isUserHasAccess = false;
            }
            return ['is_user_has_access' => $isUserHasAccess,
                'next_step' => $next_step_process,
                'next_step_role'=>$workflowRole];

        }else{
           //Log::info('Deb#5');
            return ['is_user_has_access' => false, 'next_step' => null, 'next_step_role'=>null];
        }

    }

    public function datatableList(Request $request)
    {
		$advertisementId = $request->get('advertisement_id');
        $houseTypeId = $request->get('house_type_id');
        $approvalProcessTypeId = $request->get('approval_process_type_id');
        $multiWorkflowId = $request->get('multi_workflow_id');
        $loggedUserRoleKey = Auth::user()->roles->pluck('role_key');

        $houseAllotmentApplications = $this->applicationManager->findBy($advertisementId, $houseTypeId,$multiWorkflowId);

		//Log::info(Auth::id(),$houseAllotmentApplications);
        return datatables()->of($houseAllotmentApplications)
            ->addColumn('checkBoxInput', function ($query) use ($approvalProcessTypeId,$multiWorkflowId,$loggedUserRoleKey){
                //$user_role_approval_return =$this->user_role_approval($query->application_id,Auth::id(),$query->workflow_process);
				if(($approvalProcessTypeId==2) && ($query->approved_yn != 'Y')){

					if($multiWorkflowId ==2){
                        $user_role_approval_return =$this->user_role_approval($query->application_id,Auth::id(),$query->workflow_process);
                        $isUserHasAccess = $user_role_approval_return['is_user_has_access'];
                       if($isUserHasAccess) {
                           return '<input type="checkbox" onclick="checkIndividual(this,' . $query->application_id . ')" id="checkbox_' . $query->application_id . '" name="multicheckbox[' . $query->application_id . ']" class="checkboxIdentifier" value="' . $query->application_id . '" />';
                       } else {
                           return '';
                           // return $user_role_approval_return['is_user_has_access'].'#'.$user_role_approval_return['next_step'].'#'.$user_role_approval_return['next_step_role'];
                       }
					}else if($multiWorkflowId ==1){
                        return '<input type="checkbox" onclick="checkIndividual(this,'.$query->application_id.')" id="checkbox_'.$query->application_id.'" name="multicheckbox['.$query->application_id.']" class="checkboxIdentifier" value="'.$query->application_id.'" />';
                    }
                    else{
                        return '';
                    }
                }else{
                    //Log::info($user_role_approval_return);
                    //Log::info($query->application_id);
                    return '';
                }

            })
            ->addColumn('emp_dob', function ($query) {
                if($query->emp_dob)
                {
                    return date('d-m-Y', strtotime($query->emp_dob));
                }
                return '';
            })
            ->addColumn('emp_join_date', function ($query) {
                if($query->emp_join_date)
                {
                    return date('d-m-Y', strtotime($query->emp_join_date));
                }
                return '';
            })
            ->addColumn('eligable_promotion_date', function ($query) {
                if($query->eligable_promotion_date)
                {
                    return date('d-m-Y', strtotime($query->eligable_promotion_date));
                }
                return '';
            })
            ->addColumn('status', function ($query) {
                if($query->approve_yn == 'Y' && $query->final_approve_yn == 'Y'){
                    return '<span class="badge bg-success sts-btn">Approved</span>';
                }else if($query->approve_yn == 'Y'){
                    return '<span class="badge bg-warning sts-btn">Assigned</span>';
                }else if($query->approve_yn == 'D'){
                    return '<span class="badge bg-danger sts-btn">Denied</span>';
                }else{
                    return '';
                }
            })
            ->addColumn('action', function ($query) use ($approvalProcessTypeId){
                /*$data = DB::select("select workflow_step_id from pmis.workflow_process where workflow_object_id = "."'".$query->application_id."'");
                $data = array_column($data, 'workflow_step_id');
                $data = array_unique($data);

                if($query->workflow_process!=null){
                    $compareData = DB::select("select workflow_step_id from pmis.workflow_steps where approval_workflow_id = ".$query->workflow_process);
                    $compareData = array_column($compareData, 'workflow_step_id');
                    $compareData = array_unique($compareData);
                }else{
                    $compareData = [];
                }

                if($query->workflow_process!=null){
                    if(count($data)==count($compareData) && count($compareData)!=0){
                        $actionBtn = '<a data-application-id="'.$query->application_id.'" data-toggle="modal" data-target="#houseAllotmentApprovalModal" href="#" data-backdrop="static" data-keyboard="false"><i class="bx bx-detail cursor-pointer"></i></a> ';
                    }else{
                        $actionBtn = '';
                    }
                    $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Approve"><i class="bx bx-check-circle cursor-pointer"></i></a>';
                    return $actionBtn;
                }else{
                    $actionBtn = '<a href="javascript:void(0)" class="show-receive-modal workflowBtn" title="Assign Workflow"><i class="bx bx-sitemap cursor-pointer"></i></a>';
                    return $actionBtn;
                }*/
                   if ($query->workflow_process != null) {
                        $actionBtn = '<a data-application-id="' . $query->application_id . '" data-toggle="modal" data-target="#houseAllotmentApprovalModal" href="#" data-backdrop="static" data-keyboard="false"><i class="bx bx-detail cursor-pointer"></i></a> ';
                           if($approvalProcessTypeId == 1) {
                                if ($query->approve_yn != 'D') {
                                    $hasWorkflowPermission = HelperClass::hasPermission($query->workflow_process,$query->prefix.$query->application_id);

                                    if($hasWorkflowPermission) {
                                        $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Needs To Approve"><i class="bx bxs-right-arrow-circle"></i></a>';
                                    } else {
                                        $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Approved"><i class="bx bx-check-circle cursor-pointer"></i></a>';
                                    }
                                }
                           }
                        return $actionBtn;
                    } else {
                        $actionBtn = '<a data-application-id="' . $query->application_id . '" data-toggle="modal" data-target="#houseAllotmentApprovalModal" href="#" data-backdrop="static" data-keyboard="false"><i class="bx bx-detail cursor-pointer"></i></a> ';
                           if($approvalProcessTypeId == 1) {
                                if ($query->approve_yn != 'D') {
                                    $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal workflowBtn" title="Assign Workflow"><i class="bx bx-sitemap cursor-pointer"></i></a>';
                                }
                           }
                        return $actionBtn;
                    }

                //return '<a data-application-id="'.$query->application_id.'" data-toggle="modal" data-target="#houseAllotmentApprovalModal" href="#" data-backdrop="static" data-keyboard="false"><i class="bx bx-detail cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->escapeColumns([])
            //->rawColumns(['checkBoxInput'=>'checkBoxInput','status'=>'status','action'=>'action'])
            ->make(true);
    }

    public function pointAssessmentsDropdown(Request $request,$selected=null){
        $advertisementsOptions = $this->pointAssessments($request);
        return $this->generateAdvertisementOptions($advertisementsOptions,$selected);
    }
    public function pointAssessments(Request $request)
    {
        $is_efg = $request->get('is_efg');

        $user_dept = Employee::where('emp_id', Auth::user()->emp_id)->pluck('dpt_department_id')->first();

        if($user_dept == 5)
        {
            $advertisements = DB::select("SELECT DISTINCT HA.ADVERTISEMENT_ID, HM.ADV_ID, HM.ADV_NUMBER
                              FROM ha_application ha, ha_adv_mst hm
                             WHERE HA.ADVERTISEMENT_ID = HM.ADV_ID AND HM.ACTIVE_YN = 'Y'");
        }
        else
        {
            if($is_efg == 1){
                // here department picked from ha_application cause for E/F/G there will be no department into advertisement (ha_adv_mst)
                // Those advertisement will be used where employee will be liable
                $advertisements = DB::select("SELECT DISTINCT HA.ADVERTISEMENT_ID, HM.ADV_ID, HM.ADV_NUMBER
                 FROM ha_application ha, ha_adv_mst hm
                 WHERE HA.ADVERTISEMENT_ID = HM.ADV_ID AND HM.ACTIVE_YN = 'Y' and HA.APPLIED_HOUSE_TYPE_ID in (6,7,9)");
            }else{
                $advertisements = DB::select("SELECT DISTINCT HA.ADVERTISEMENT_ID, HM.ADV_ID, HM.ADV_NUMBER
                 FROM ha_application ha, ha_adv_mst hm
                 WHERE HA.ADVERTISEMENT_ID = HM.ADV_ID AND HM.ACTIVE_YN = 'Y' AND HA.DPT_DEPARTMENT_ID = '$user_dept' and HA.APPLIED_HOUSE_TYPE_ID not in (6,7,9)");
            }
 // here department picked from ha_application cause for E/F/G there will be no department into advertisement (ha_adv_mst)
 // Those advertisement will be used where employee will be liable
        }

        return [
            'advertisements' => $advertisements
        ];
    }

    public function store(Request $request)
    {
        $pointAssessmentParams = $request->post();
        if(isset($pointAssessmentParams['is_efg'])){
            $is_efg = $pointAssessmentParams['is_efg'];
        }else{
            $is_efg = 2;
        }
        try {
            $statusCode = sprintf("%4000s", '');
            $statusMessage = sprintf('%4000s', '');

            $params = [
                'p_ADV_ID' => $pointAssessmentParams['advertisement_id'],
                'p_HOUSE_TYPE_ID' => $pointAssessmentParams['house_type_id'],
                'p_PROCESS_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];

            DB::executeProcedure('ha_point_process', $params);

            return redirect()->route('point-assessment.index', ['advertisement_id' => $pointAssessmentParams['advertisement_id'], 'house_type_id'=>$pointAssessmentParams['house_type_id'],'approval_process_type_id'=>$pointAssessmentParams['approval_process_type_id'],'multi_workflow_id'=>$pointAssessmentParams['multi_workflow_id'],'is_efg'=>$is_efg]);
        } catch(Exception $exception) {
            return [  "exception" => true, "class" => 'error', "message" => $exception->getMessage()];
        }
    }

}
