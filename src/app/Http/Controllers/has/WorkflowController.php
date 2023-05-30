<?php

namespace App\Http\Controllers\has;

use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Advertisement\AdvertisementMst;
use App\Entities\HouseAllotment\Acknowledgemnt;
use App\Entities\HouseAllotment\AllotPoint;
use App\Entities\HouseAllotment\DeptAcknowledgement;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Pmis\LApprovalWorkflows;
use App\Entities\Pmis\WorkFlowProcess;
use App\Entities\Pmis\WorkFlowStep;
use App\Entities\Security\Role;
use App\Entities\Security\User;
use App\Entities\Security\UserRole;
use App\Enums\ModuleInfo;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Managers\FlashMessageManager;
use App\Managers\AdvertisementManager;

class WorkflowController extends Controller
{
    private $flashMessageManager;
    private $employeeManager;

    public function __construct(FlashMessageManager $flashMessageManager,EmployeeContract $employeeManager,      AdvertisementManager $advertisementManager)
    {
        $this->advertisementManager = $advertisementManager;
        $this->flashMessageManager = $flashMessageManager;
        $this->employeeManager = $employeeManager;
    }

    public function status(Request $request, $workflowId_out = null, $object_id_out = null)
    {
        if ($workflowId_out && $object_id_out) {
            $workflowId = $workflowId_out;
            $object_id = $object_id_out;
        } else {
            $workflowId = $request->get("workflowId");
            $object_id = $request->get("objectid");
        }
        $progressBarData = WorkFlowStep::where('approval_workflow_id', $workflowId)->orderby('process_step')->get();
        $current_step = [];
        $previous_step = [];
        $workflowProcess = WorkFlowProcess::with('workflowStep')
            ->where('workflow_object_id', $object_id)
            ->orderBy('workflow_process_id', 'DESC')
            ->whereHas('workflowStep', function ($query) use ($workflowId) {
                $query->where('approval_workflow_id', $workflowId);
            })->get();

        $option = [];
        if (!count($workflowProcess)) {
            $next_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->orderBy('process_step', 'asc')->first();
        } else {
            if ($workflowProcess) {
                $current_step = $workflowProcess[0]->workflowStep;
                $sql = 'select e.emp_code, e.emp_name, d.designation
                       from cpa_security.sec_users u
                         inner join pmis.employee e on (e.emp_id = u.emp_id)
                         left join pmis.L_DESIGNATION d  on (d.designation_id = e.designation_id)
                         where user_id=:userId';
                $user = db::selectOne($sql, ['userId' => $workflowProcess[0]->insert_by]);
                $current_step->user = $user;
                $current_step->note = $workflowProcess[0]->note;
            }

            $next_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->where('process_step', '>', $current_step->process_step)->orderBy('process_step', 'asc')->first();
            $previous_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->where('process_step', '<', $current_step->process_step)->orderBy('process_step', 'asc')->get();
        }

        if (!empty($previous_step)) {
            foreach ($previous_step as $previous) {
                $option[] = [
                    'text' => $previous->backward_title,
                    'value' => $previous->workflow_step_id,
                ];
            }
        }

        if (!empty($current_step)) {
            $option[] = [
                'text' => $current_step->forward_title,
                'value' => $current_step->workflow_step_id,
                'disabled' => true
            ];
        }

        if (!empty($next_step)) {
            $option[] = [
                'text' => $next_step->forward_title,
                'value' => $next_step->workflow_step_id,
                'selected' => true,
            ];
        }

        $process = [];
        foreach ($workflowProcess as $wp) {
            $sql = 'select e.emp_code, e.emp_name, d.designation
                       from cpa_security.sec_users u
                         inner join pmis.employee e on (e.emp_id = u.emp_id)
                         left join pmis.L_DESIGNATION d  on (d.designation_id = e.designation_id)
                         where user_id=:userId';
            $user = db::selectOne($sql, ['userId' => $wp->insert_by]);
            $wp->user = $user;
            $process[] = $wp;
        }

        $msg = '';
        $ids = array_column($option, 'value');
        $value = $ids ? max($ids) : 0;
        $prev_val = $value;
        foreach ($option as $data) {
            $disabeld = (isset($data['disabled']) && $data['disabled']) ? 'disabled' : '';
            $selected = (isset($data['selected']) && $data['selected']) ? 'selected' : '';
            $msg .= '<option value="' . $data['value'] . '" ' . $disabeld . ' ' . $selected . '>'
                . $data['text'] . '</option>';
        }

        $is_approved = WorkFlowProcess::where('workflow_object_id', $object_id)->where('workflow_step_id', null)->first();

        return response(
            [
                'workflowProcess' => $process,
                'progressBarData' => $progressBarData,
                'next_step' => $next_step,
                'previous_step' => $previous_step,
                'current_step' => $current_step,
                'options' => $msg,
                'is_approved' => $is_approved,
            ]
        );
    }

    public function store(Guard $auth, Request $request)
    {
        $workflowId = $request->get("workflow_id");
        $object_id = $request->get("prefix") . $request->get("object_id");

//        $object_id = $request->get("object_id");
        /*if($workflowId=='10'){

        }else if($workflowId=='11'){
            $object_id = 'iaa'.$request->get("object_id");
        }else if($workflowId=='12'){
            $object_id = 'raa'.$request->get("object_id");
        }dd($workflowId);*/


        $whichWorkflowIsActive = $request->get("workflow") ? $request->get("workflow") : 0;

        $is_approved = WorkFlowProcess::where('workflow_object_id', $object_id)->where('workflow_step_id', null)->first();

        DB::beginTransaction();
        if ($request->approve_btn) {
            $status_id = '';
        } else {
            $status_id = $request->get("status_id");
        }

        try {
            $workflow_process_id = '';
            $status_code = sprintf("%4000s", "");
            $status_message = sprintf("%4000s", "");
            $params = [
                "p_workflow_process_id" => $workflow_process_id,
//                "p_workflow_object_id"  => $request->get("object_id"),
                "p_workflow_object_id" => $object_id,
                "p_workflow_step_id" => $status_id,//$request->get("workflow_step_id"),
                "p_note" => $request->get("note"),
                "p_insert_by" => auth()->id(),
                "o_status_code" => &$status_code,
                "o_status_message" => &$status_message,
            ];

            DB::executeProcedure("pmis.workflow_Process_entry", $params);

            if (empty($workflow_process_id)) {

                $o_status_message_array = explode(',', $params['o_status_message']);
                $status_message = $o_status_message_array[0];
                $workflow_final_process_id = $o_status_message_array[1];
            } else {
                $workflow_final_process_id = '';
            }

            if ($params['o_status_code'] == 1) {
                $status_code2 = sprintf("%4000s", "");
                $status_message2 = sprintf("%4000s", "");
                $params2 = [
                    "p_reference_table" => $request->get("reference_table"),
                    "p_referance_key" => $request->get("referance_key"),
                    "p_referance_id" => $request->get("object_id"),
                    "p_workflow_final_process_id" => $workflow_final_process_id,
                    "p_workflow_approved_yn" => $status_id ? '' : 'Y',
                    "p_insert_by" => auth()->id(),
                    "o_status_code" => &$status_code2,
                    "o_status_message" => &$status_message2
                ];

                DB::executeProcedure("has.workflow_initiat_table_update", $params2);

            } else {
                DB::rollback();
                return ["exception" => true, "status" => false, "o_status_code" => 99, "o_status_message" => $params['o_status_message']];
            }

        } catch (\Exception $e) {
            DB::rollback();
            return ["exception" => true, "status" => false, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        $step = WorkFlowStep::where('workflow_step_id', $request->get("status_id"))->get();

        $workflowName = LApprovalWorkflows::where('APPROVAL_WORKFLOW_ID', $workflowId)->first('WORK_FLOW_NAME');

        $ha_app = HaApplication::where('application_id', $request->get("object_id"))->first();

        // dd($request->get("object_id"),$ha_app,$workflowName,$step,$request->get("status_id"),$workflowId);
        if (isset($ha_app)) {

            $adv_name = AdvertisementMst::where('adv_id', $ha_app->advertisement_id)->pluck('adv_number')->first();

            if (isset($adv_name)) {
                if ($request->get("note")) {
                    $notification_msg = 'Pending ' . $workflowName['work_flow_name'] . ' Workflow Approval Need to Review. (Employee Code: ' . $ha_app->emp_code . ' & Advertisement Name: ' . $adv_name . ') Note: ' . $request->get("note");
                } else {
                    $notification_msg = 'Pending ' . $workflowName->work_flow_name . ' Workflow Approval Need to Review. (Employee Code: ' . $ha_app->emp_code . ' & Advertisement Name: ' . $adv_name . ')';
                }
            } else {
                if ($request->get("note")) {
                    $notification_msg = 'Pending ' . $workflowName['work_flow_name'] . ' Workflow Approval Need to Review. (Employee Code: ' . $ha_app->emp_code . ' ) Note: ' . $request->get("note");
                } else {
                    $notification_msg = 'Pending ' . $workflowName->work_flow_name . ' Workflow Approval Need to Review. (Employee Code: ' . $ha_app->emp_code . ')';
                }
            }
        } else {

            if ($request->get("note")) {
                $notification_msg = 'Pending ' . $workflowName['work_flow_name'] . ' Workflow Approval Need to Review. Note: ' . $request->get("note");
            } else {
                $notification_msg = 'Pending ' . $workflowName->work_flow_name . ' Workflow Approval Need to Review. ';
            }
        }

        if ($status_id) {
            if ($step[0]->role_yn == 'N') {
                $controller_user_notification = [
                    "p_notification_to" => $step[0]->user_id,
                    "p_insert_by" => Auth::id(),
                    "p_note" => $notification_msg,
                    "p_priority" => null,
                    "p_module_id" => 14,
                    "p_target_url" => $request->get("get_url")
                ];
                try {
                    DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                } catch (\Exception $e) {
                    DB::rollback();
                    session()->flash('m-class', 'alert-danger');
                    return redirect()->back()->with('message',$e->getMessage());
                }
            } else {
                $role_id = Role::where('role_key', $step[0]->user_role)->value('role_id');
                $user_roles = UserRole::where('role_id', $role_id)->get();

                if ($whichWorkflowIsActive == 1) {
                    $user_dpt = HaApplication::where('application_id', $request->get("object_id"))->pluck('dpt_department_id')->first();
                } elseif ($whichWorkflowIsActive == 3) {
                    $user_dpt = DeptAcknowledgement::where('dept_ack_id', $request->get("object_id"))->pluck('dpt_department_id')->first();
                } elseif ($whichWorkflowIsActive == 4) {
                    $user_dpt = AdvertisementMst::where('ADV_ID', $request->get("object_id"))->pluck('dpt_department_id')->first();
                } else {
                    $user_dpt = HaApplication::where('application_id', $request->get("object_id"))->pluck('dpt_department_id')->first();
                }

                foreach ($user_roles as $user_role) {
                    $role_emp_id = User::where('user_id', $user_role->user_id)->pluck('emp_id')->first();
                    $role_dpt = Employee::where('emp_id', $role_emp_id)->pluck('dpt_department_id')->first();
                    if ($whichWorkflowIsActive == 3 || ($whichWorkflowIsActive == 4 && $user_dpt == null)) {
                        if ($role_dpt == 5) {//only civil department
                            $controller_user_notification = [
                                "p_notification_to" => $user_role->user_id,
                                "p_insert_by" => Auth::id(),
                                "p_note" => $notification_msg,
                                "p_priority" => null,
                                "p_module_id" => 14,
                                "p_target_url" => $request->get("get_url")
                            ];
                            try {
                                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
//                                dd($controller_user_notification);
                            } catch (\Exception $e) {
                                DB::rollback();
                                session()->flash('m-class', 'alert-danger');
                                return redirect()->back()->with('message',$e->getMessage());
                            }
                        }
                    } elseif ($whichWorkflowIsActive == 0) {
                        $controller_user_notification = [
                            "p_notification_to" => $user_role->user_id,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $notification_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => $request->get("get_url")
                        ];
                        try {
                            DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
//                            dd($controller_user_notification);
                        } catch (\Exception $e) {
                            DB::rollback();
                            session()->flash('m-class', 'alert-danger');
                            return redirect()->back()->with('message',$e->getMessage());
                        }
                    } else if ($user_dpt == $role_dpt) {
                        $controller_user_notification = [
                            "p_notification_to" => $user_role->user_id,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $notification_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => $request->get("get_url")
                        ];
                        try {
                            DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
//                            dd($controller_user_notification);
                        } catch (\Exception $e) {
                            DB::rollback();
                            session()->flash('m-class', 'alert-danger');
                            return redirect()->back()->with('message',$e->getMessage());                        }
                    }
                }
            }
        } else {

            if ($params['o_status_code'] == 1) {
                if (isset($request->approve_btn) && $request->approve_btn == 'Y') {
                    if ($request->prefix == 'acknowledge') {

//                        $ack_id = $object_id;
                        $ack_id = $request->get("object_id");

                        $ack_info = Acknowledgemnt::where('dept_ack_id', $ack_id)->first();

                        $hod_query = <<<QUERY
SELECT user_id
  FROM cpa_security.sec_users
 WHERE emp_id in
       (SELECT E.emp_id
          FROM PMIS.EMPLOYEE  E
               JOIN CPA_SECURITY.SEC_USERS SU ON SU.EMP_ID = E.EMP_ID
               JOIN CPA_SECURITY.SEC_USER_ROLES SUR
                   ON SUR.USER_ID = SU.USER_ID
               JOIN CPA_SECURITY.SEC_ROLE SR ON SR.ROLE_ID = SUR.ROLE_ID
         WHERE SR.ROLE_KEY = 'HOD' AND E.DPT_DEPARTMENT_ID = $ack_info->dpt_department_id)
QUERY;

                        $hod = DB::select($hod_query);

                        $total = DB::selectOne('SELECT COUNT (hl.house_id)     AS total_house
  FROM house_list hl, dept_acknowledgement da
 WHERE hl.DEPT_ACK_ID = da.DEPT_ACK_ID AND da.dept_ack_id = ' . $ack_id);

                        $types_query = <<<QUERY
SELECT LISTAGG (house_type, ', ') WITHIN GROUP (ORDER BY house_type)
           "house_type"
  FROM has.l_house_type
 WHERE house_type_id IN
           (SELECT DISTINCT hl.house_type_id
              FROM house_list hl, dept_acknowledgement da
             WHERE     hl.DEPT_ACK_ID = da.DEPT_ACK_ID
                   AND da.dept_ack_id = $ack_id)
QUERY;
                        $types = DB::selectOne($types_query);

                        $msg = 'New Acknowledgement (ACK NO: ' . $ack_info->dept_ack_no . ') has been Assigned. ' . $total->total_house . ' Houses has been Allotted to Your Department of ' . $types->house_type . ' Types.';

                        foreach ($hod as $head) {
                            try {
                                $controller_user_notification = [
                                    "p_notification_to" => $head->user_id,
                                    "p_insert_by" => Auth::id(),
                                    "p_note" => $msg,
                                    "p_priority" => null,
                                    "p_module_id" => 14,
                                    "p_target_url" => url('/advertisements')
                                ];
                                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                            } catch (\Exception $e) {
                                DB::rollBack();
                                session()->flash('m-class', 'alert-danger');
                                return redirect()->back()->with('message',$e->getMessage());
                            }
                        }
                    } elseif ($request->prefix == 'advertisement') {
                        $adv_id = $object_id;
                        $adv_id = $request->get("object_id");

                        $adv_dtl = AdvertisementMst::where('adv_id', $adv_id)->first();
                        $adv_no = $adv_dtl->adv_number;
                        $ad_msg = 'A House Advertisement Has Been Published (' . $adv_no . '). You Are Eligible to Apply for a House.';
                        $dpt_id = HaAdvMst::where('ADV_ID', $adv_id)->pluck('dpt_department_id')->first();

                        $employees = $this->advertisementManager->notifyEmp($adv_id, $dpt_id);

                        if ($employees) {
                            foreach ($employees as $emp) {
                                $controller_user_notification = [
                                    "p_notification_to" => $emp->user_id,
                                    "p_insert_by" => Auth::id(),
                                    "p_note" => $ad_msg,
                                    "p_priority" => null,
                                    "p_module_id" => 14,
                                    "p_target_url" => url('/report/render?xdo=/~weblogic/HAS/RPT_ADVERTISEMENT_DETAILS_REPORT.xdo&p_advertise_id=' . $adv_id . '&type=pdf&filename=advertisement_report')
                                ];
                                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                            }
                        }
                    }
                    elseif ($request->prefix == 'haa') {

                        $ha_applicant = $request->object_id;

                        $haApplication = HaApplication::with(['allot_point', 'houseallotment'])->find($ha_applicant);

                        //Generates Letter

                        if ($haApplication->houseallotment) {
                            $p_id = '';
                            $statusCode = sprintf("%4000s", "");
                            $statusMessage = sprintf('%4000s', '');
                            try {
                                $params_letter = [
                                    "p_ALLOT_LETTER_ID" => [
                                        "value" => &$p_id,
                                        "type" => \PDO::PARAM_INPUT_OUTPUT,
                                        "length" => 255
                                    ],
                                    "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                                    "p_ALLOT_LETTER_NO" => $haApplication->houseallotment->board_decision_number,
                                    "p_APPLICATION_ID" => $ha_applicant,
                                    "p_HOUSE_ADV_ID" => $haApplication->advertisement_id,
                                    "p_DELIVERY_YN" => 'N',
                                    "p_DELIVERY_DATE" => '',
                                    "p_DELIVERED_BY" => '',
                                    //"p_RECEIVED_BY"       => '',
                                    "p_MEMO_NO" => '',
                                    "p_MEMO_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                                    //"p_REMARKS"           => $request->get('remarks'),
                                    "p_insert_by" => Auth()->ID(),
                                    "o_status_code" => &$statusCode,
                                    "o_status_message" => &$statusMessage
                                ];

                                DB::executeProcedure('allotment.allot_letter_entry', $params_letter);

                            } catch (\Exception $e) {
                                DB::rollBack();
                                session()->flash('m-class', 'alert-danger');
                                return redirect()->back()->with('message',$e->getMessage());
                                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
                            }
                            if ($params_letter['o_status_code'] == 1)//Trying to get property 'emp_id' of non-object
                            {

                              DB::update("update  HAS.ALLOT_POINT  set final_approve_yn = 'Y' where APPLICATION_ID = '$ha_applicant'");

                                //Sends Notification
                                $houseId = $haApplication->houseallotment->house_id;
                                $employeeInfo = $this->employeeManager->findEmployeeInformation($haApplication->emp_code);

                                //To Applicant
                                $houseInfo = HouseList::where('house_id', $houseId)->with('colonylist', 'buildinglist')->first();

                                $name = isset($houseInfo->house_code) ? $houseInfo->house_name . ' (' . $houseInfo->house_code . ')' : $houseInfo->house_name;
                                $notification_msg = 'Congratulations! A flat has been allotted to you. Colony: ' . $houseInfo->colonylist->colony_name . ', Building: ' . $houseInfo->buildinglist->building_name . ', Flat: ' . $name;
                                $coUserId = DB::table('cpa_security.sec_users')->where('emp_id', $employeeInfo['emp_id'])->pluck('user_id')->first();
                                if ($coUserId) {
                                    try {
                                        $controller_user_notification = [
                                            "p_notification_to" => $coUserId,
                                            "p_insert_by" => Auth::id(),
                                            "p_note" => $notification_msg,
                                            "p_priority" => null,
                                            "p_module_id" => 14,
                                            "p_target_url" => url('/take-over-application')
                                        ];

                                        DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

                                    } catch (\Exception $e) {
                                        DB::rollBack();
                                        return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                                        //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
                                    }
                                }

                                if ($employeeInfo['emp_email']) {
                                    //Sends Email
                                    //Mail::to($employeeInfo['emp_email'])->send(new HouseApprove($employeeInfo['emp_name'], $employeeInfo['emp_code']));
                                    try {
                                        //Updates Letter Delivery
                                        $p_id = $params_letter['p_ALLOT_LETTER_ID']['value'];
//                                        dd($p_id);
                                        $statusCode = sprintf("%4000s", "");
                                        $statusMessage = sprintf('%4000s', '');
                                        $params_upd_letter = [
                                            "p_ALLOT_LETTER_ID" => [
                                                "value" => &$p_id,
                                                "type" => \PDO::PARAM_INPUT_OUTPUT,
                                                "length" => 255
                                            ],
                                            "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                                            "p_ALLOT_LETTER_NO" => $haApplication->houseallotment->board_decision_number,
                                            "p_APPLICATION_ID" => $ha_applicant,
                                            "p_HOUSE_ADV_ID" => $haApplication->advertisement_id,
                                            "p_DELIVERY_YN" => 'Y',
                                            "p_DELIVERY_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                                            "p_DELIVERED_BY" => '',
                                            //"p_RECEIVED_BY"       => '',
                                            "p_MEMO_NO" => '',
                                            "p_MEMO_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                                            //"p_REMARKS"           => $request->get('remarks'),
                                            "p_insert_by" => Auth()->ID(),
                                            "o_status_code" => &$statusCode,
                                            "o_status_message" => &$statusMessage
                                        ];

                                        DB::executeProcedure('allotment.allot_letter_entry', $params_upd_letter);

                                    } catch (\Exception $e) {
                                        DB::rollBack();
                                        return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                                        //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
                                    }

                                }
                                DB::commit();
                                session()->flash('m-class', 'alert-success');
                                session()->flash('message', $status_message);
                                return redirect()->back()->with('message', $status_message);
                            } else {
                                DB::rollBack();
                                session()->flash('m-class', 'alert-danger');
                                return redirect()->back()->with('message',$params_letter['o_status_message']);
                            }
                        }else{
                            DB::rollBack();
                            session()->flash('m-class', 'alert-danger');
                            session()->flash('message', $status_message);
                            return redirect()->back()->with('message','Please House Assign Before Final Approve !');

                        }
                    }
                }
            }
        }
        DB::commit();

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $status_message);

        return redirect()->back()->with('message', $status_message);

    }

    public function load_workflow()
    {
        $msg = '';
        $module_id = ModuleInfo::MODULE_ID;

        $dpt_id = Auth::user()->employee->dpt_department_id;
        $option = LApprovalWorkflows::where('module_id', $module_id)->where('department_id', $dpt_id)->orderBy('approval_workflow_id', 'asc')->get();

        foreach ($option as $data) {
            $msg .= '<option value="' . $data['approval_workflow_id'] . '">' . $data['work_flow_name'] . '</option>';
        }
        return response(
            [
                'options' => $msg,
            ]
        );
    }

    public function assign_workflow(Request $request)
    {
//        dd($request);
        $application_id = $request->get('application_id_flow');
        $prefix = $request->get('prefix');
        $workFlowId = $request->get('status_id');
        $table = $request->get('t_name');
        $column = $request->get('c_name');

        DB::beginTransaction();
        if ($application_id) {
            $application = DB::table($table)->where($column, $application_id)->update(array('workflow_process' => $workFlowId));

            if ($application == 1) {
                $step = WorkFlowStep::where('APPROVAL_WORKFLOW_ID', $workFlowId)->orderBy('process_step', 'asc')->first();

                $status_code = sprintf("%4000s", "");
                $status_message = sprintf("%4000s", "");
                $params = [
                    "p_workflow_process_id" => $workFlowId,//$request->get("workflow_process_id"),
                    "p_workflow_object_id" => $prefix . $application_id,
                    "p_workflow_step_id" => $step->workflow_step_id,//Todo first step
                    "p_note" => "System Assigned",
                    "p_insert_by" => auth()->id(),
                    "o_status_code" => &$status_code,
                    "o_status_message" => &$status_message,
                ];

                try {
                    DB::executeProcedure("pmis.workflow_Process_entry", $params);
//
//                    //send notification
//                    if($params['o_status_code'] == 1 && $prefix == 'acknowledge') //only for acknoledge workflow
//                    {
//                        $users = DB::select('SELECT su.user_id, e.emp_name
//  FROM CPA_SECURITY.SEC_USERS  su
//       LEFT JOIN CPA_SECURITY.SEC_USER_ROLES sur ON sur.user_id = su.user_id
//       LEFT JOIN pmis.employee e ON e.EMP_ID = su.emp_id
//       LEFT JOIN CPA_SECURITY.sec_role sr ON sr.role_id = sur.role_id
//       LEFT JOIN pmis.WORKFLOW_STEPS ws ON ws.USER_ROLE = sr.role_key
// WHERE     ws.APPROVAL_WORKFLOW_ID = '.$workFlowId.'
//       AND e.dpt_department_id = (SELECT dpt_department_id
//                                    FROM has.DEPT_ACKNOWLEDGEMENT
//                                   WHERE DEPT_ACK_ID = '.$application_id.')');
//
//                        if ($users)
//                        {
//                            $ack_no = DeptAcknowledgement::where('dept_ack_id', $application_id)->pluck('dept_ack_no')->first();
//                            $msg = 'A new workflow needs your approval for department acknowledgment. ACK No.: '.$ack_no;
//                            foreach ($users as $user)
//                            {
//                                try {
//                                    $controller_user_notification = [
//                                        "p_notification_to" => $user->user_id,
//                                        "p_insert_by" => Auth::id(),
//                                        "p_note" => $msg,
//                                        "p_priority" => null,
//                                        "p_module_id" => 14,
//                                        "p_target_url" => url('/allocate-flat')
//                                    ];
//                                    DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
//                                } catch (\Exception $e) {
//                                    //DB::rollBack();
//                                    return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
//                                }
//                            }
//                        }
//                    }
//                    //end notification

                } catch (\Exception $e) {
                    DB::rollback();
                    return ["exception" => true, "status" => false, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
                }

                DB::commit();
                $status_message = 'Workflow Assigned Successfully.';
                session()->flash('m-class', 'alert-success');
                session()->flash('message', $status_message);

                return redirect()->back()->with('message', $status_message);
            } else {
                DB::rollback();
                $status_message = 'Something went wrong.';
                session()->flash('m-class', 'alert-danger');
                session()->flash('message', $status_message);

                return redirect()->back()->with('message', $status_message);
            }
        }
    }

    public function get_workflow(Request $request)
    {
        $row_id = $request->get('row_id');
        $table = $request->get('t_name');
        $column = $request->get('c_name');
        $option = DB::table($table)->where($column, $row_id)->first();
        $workflow_process = $option->workflow_process;
        return $workflow_process;
    }

    // Multi point approval 13-06-2022
    public function multiApprovalStore(Guard $auth, Request $request)
    {
        $prefix = 'haa';
        $table = 'ha_application';
        $column = 'application_id';
        $multicheckbox = $request->get("multicheckbox");
        $multnote = $request->get("note");
        DB::beginTransaction();

        if (isset($multicheckbox)) {
            if (count($multicheckbox) > 0) {
                $params = [];
                foreach ($multicheckbox as $key => $value) {
                    $option = DB::table($table)->where($column, $value)->first();
                    $workflowId = $option->workflow_process;
                    $workflow_process_id = $option->workflow_process_id;
                    $object_id = $prefix . $value;
                    $application_id = $value;
                    $approved_yn = $option->approved_yn;

                    $option = [];
                    $current_step = [];
                    $previous_step = [];

                    $workflowProcess = WorkFlowProcess::with('workflowStep')
                        ->where('workflow_object_id', $object_id)
                        ->orderBy('workflow_process_id', 'DESC')
                        ->whereHas('workflowStep', function ($query) use ($workflowId) {
                            $query->where('approval_workflow_id', $workflowId);
                        })->get();

                    if (!count($workflowProcess)) {
                        $next_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->orderBy('process_step', 'asc')->first();
                    } else {
                        if ($workflowProcess) {
                            $current_step = $workflowProcess[0]->workflowStep;
                            $sql = 'select e.emp_code, e.emp_name, d.designation
                         from cpa_security.sec_users u
                         inner join pmis.employee e on (e.emp_id = u.emp_id)
                         left join pmis.L_DESIGNATION d  on (d.designation_id = e.designation_id)
                         where user_id=:userId';
                            $user = db::selectOne($sql, ['userId' => $workflowProcess[0]->insert_by]);
                            $current_step->user = $user;
                            $current_step->note = $workflowProcess[0]->note;
                        }

                        $next_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->where('process_step', '>', $current_step->process_step)->orderBy('process_step', 'asc')->first();
                        $previous_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->where('process_step', '<', $current_step->process_step)->orderBy('process_step', 'asc')->get();
                    }

                    if (!empty($previous_step)) {
                        foreach ($previous_step as $previous) {
                            $option[] = [
                                'text' => $previous->backward_title,
                                'value' => $previous->workflow_step_id,
                            ];
                        }
                    }

                    if (!empty($current_step)) {
                        $option[] = [
                            'text' => $current_step->forward_title,
                            'value' => $current_step->workflow_step_id,
                            'disabled' => true
                        ];
                    }

                    if (!empty($next_step)) {
                        $option[] = [
                            'text' => $next_step->forward_title,
                            'value' => $next_step->workflow_step_id,
                            'selected' => true,
                        ];
                        $next_forward_title = $next_step->forward_title;
                        $next_workflow_step_id = $next_step->workflow_step_id;
                    } else {
                        $next_forward_title = '';
                        $next_workflow_step_id = '';
                    }

                    /*
                    if(empty($workflow_process_id) && ($approved_yn =='N'))
                    {
                        $next_workflow_step_id = '';
                    }
                    else
                    {
                        $next_workflow_step_id = $next_step->workflow_step_id;
                    }
                    */

                    if (isset($next_workflow_step_id) || ($approved_yn == 'N')) {
                        try {
                            $workflow_process_id = ($next_workflow_step_id ? $workflowId : ''); // null for final approval
                            $status_code = sprintf("%4000s", "");
                            $status_message = sprintf("%4000s", "");
                            $params = [
                                "p_workflow_process_id" => $workflow_process_id, //$workflowId,//$request->get("workflow_process_id"),
                                "p_workflow_object_id" => $object_id,
                                "p_workflow_step_id" => $next_workflow_step_id ? $next_workflow_step_id : null,
                                "p_note" => $multnote,//$request->get("note"),
                                "p_insert_by" => auth()->id(),
                                "o_status_code" => &$status_code,
                                "o_status_message" => &$status_message,
                            ];

                            DB::executeProcedure("pmis.workflow_Process_entry", $params);

                            // Final Workflow Approval Start
                            if (empty($workflow_process_id)) {
                                $o_status_message_array = explode(',', $params['o_status_message']);
                                $status_message = $o_status_message_array[0];
                                $workflow_final_process_id = $o_status_message_array[1];
                            } else {
                                $workflow_final_process_id = '';
                            }

                            if ($params['o_status_code'] && ($approved_yn == 'N') && ($next_workflow_step_id == null)) {
                                /*checks if houses assigned for all applications*/
                                $is_house_assigned = HouseAllotment::where('application_id', $value)->exists();
                                if (!$is_house_assigned) {
                                    $emp_id = HaApplication::where('application_id', $value)->pluck('emp_id')->first();
                                    $emp_info = Employee::where('emp_id', $emp_id)->first();
                                    DB::rollBack();
                                    return redirect()->back()->with('message', 'Please, assign house for '. $emp_info->emp_name . ' (' . $emp_info->emp_code . ')!');
                                }
                                /**/
                                $status_code2 = sprintf("%4000s", "");
                                $status_message2 = sprintf("%4000s", "");
                                $params2 = [
                                    "p_reference_table" => $table,
                                    "p_referance_key" => $column,
                                    "p_referance_id" => $application_id,
                                    "p_workflow_final_process_id" => $workflow_final_process_id,
                                    "p_workflow_approved_yn" => 'Y',
                                    "p_insert_by" => auth()->id(),
                                    "o_status_code" => &$status_code2,
                                    "o_status_message" => &$status_message2
                                ];
                                DB::executeProcedure("has.workflow_initiat_table_update", $params2);

                                if($params2['o_status_code'] == 1) {
                                    $send_notification = $this->send_house_allot_notification($value);
                                    if($send_notification == 99) {
                                        DB::rollBack();
                                        return redirect()->back()->with('message', $send_notification['o_status_message']);
                                    }
                                }
                            }
                            // Final Workflow Approval End


                        } catch (\Exception $e) {
                            DB::rollback();
                            session()->flash('m-class', 'alert-danger');
                            session()->flash('message', $e->getMessage());
                            return redirect()->back()->with('message', $e->getMessage());
                        }

                        //Start notification

                        $workflowName = LApprovalWorkflows::where('APPROVAL_WORKFLOW_ID', $workflowId)->first('WORK_FLOW_NAME');

                        $ha_app = HaApplication::where('application_id', $application_id)->first(); //
                        $adv_name = AdvertisementMst::where('adv_id', $ha_app->advertisement_id)->pluck('adv_number')->first();
                        if ($multnote) {
                            $notification_msg = 'Pending ' . $workflowName['work_flow_name'] . ' Workflow Approval Need to Review. (Employee Code: ' . $ha_app->emp_code . ' & Advertisement Name: ' . $adv_name . ') Note: ' . $request->get("note");
                        } else {
                            $notification_msg = 'Pending ' . $workflowName->work_flow_name . ' Workflow Approval Need to Review. (Employee Code: ' . $ha_app->emp_code . ' & Advertisement Name: ' . $adv_name . ')';
                        }

                        if ($next_workflow_step_id) {
                            $step = WorkFlowStep::where('workflow_step_id', $next_workflow_step_id)->get();

                            if ($step[0]->role_yn == 'N') {
                                $controller_user_notification = [
                                    "p_notification_to" => $step[0]->user_id,
                                    "p_insert_by" => Auth::id(),
                                    "p_note" => $notification_msg,
                                    "p_priority" => null,
                                    "p_module_id" => 14,
                                    "p_target_url" => $request->get("get_url") ? $request->get("get_url") : ''
                                ];
                                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

                            } else {
                                $role_id = Role::where('role_key', $step[0]->user_role)->value('role_id');
                                $user_roles = UserRole::where('role_id', $role_id)->get();
                                $user_dpt = HaApplication::where('application_id', $application_id)->pluck('dpt_department_id')->first();
                                foreach ($user_roles as $user_role) {
                                    $role_emp_id = User::where('user_id', $user_role->user_id)->pluck('emp_id')->first();
                                    $role_dpt = Employee::where('emp_id', $role_emp_id)->pluck('dpt_department_id')->first();
                                    if ($user_dpt == $role_dpt) {
                                        try {

                                            $controller_user_notification = [
                                                "p_notification_to" => $user_role->user_id,
                                                "p_insert_by" => Auth::id(),
                                                "p_note" => $notification_msg,
                                                "p_priority" => null,
                                                "p_module_id" => 14,
                                                "p_target_url" => $request->get("get_url") ? $request->get("get_url") : ''
                                            ];
                                            DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

                                        } catch (\Exception $e) {
                                            DB::rollback();
                                            session()->flash('m-class', 'alert-danger');
                                            session()->flash('message', $e->getMessage());
                                            return redirect()->back()->with('message', $e->getMessage());
                                        }                                        //echo 'notify_add';
                                        //print_r($controller_user_notification);
                                    }
                                }
                            }
                        }
                        //End notification
                    }

                } // END: foreach

            } else {
                $status_message = 'Something went wrong.';
                session()->flash('m-class', 'alert-danger');
                session()->flash('message', $status_message);
                return redirect()->back()->with('message', $status_message);
            }
        } else {
            $status_message = 'Please, select at least one application to process!';
            session()->flash('m-class', 'alert-danger');
            session()->flash('message', $status_message);
            return redirect()->back()->with('message', $status_message);
        }

        DB::commit();
        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        session()->flash('m-class', 'alert-success');
        session()->flash('message', $flashMessageContent['message']);
        return redirect()->back()->with('message', $flashMessageContent['message']);
    }

    public function multi_assign_workflow(Request $request)
    {
        $prefix = 'haa';
        $table = 'ha_application';
        $column = 'application_id';

        //$prefix = $request->get('prefix');
        $workFlowId = $request->get('workflow_assign_id');
        //$table = $request->get('t_name');
        //$column = $request->get('c_name');
        $multicheckbox = $request->get("multicheckbox");

        if (isset($multicheckbox)) {
            if (count($multicheckbox) > 0) {
                DB::beginTransaction();
                foreach ($multicheckbox as $key => $value) {
                    $application_id = $value;
                    $object_id = $prefix . $application_id;
                    if ($application_id) {
                        $application = DB::table($table)->where($column, $application_id)->update(array('workflow_process' => $workFlowId));
                        if ($application == 1) {
                            try {
                                $step = WorkFlowStep::where('APPROVAL_WORKFLOW_ID', $workFlowId)->orderBy('process_step', 'asc')->first();
                                $status_code = sprintf("%4000s", "");
                                $status_message = sprintf("%4000s", "");
                                $params = [
                                    "p_workflow_process_id" => $workFlowId,//$request->get("workflow_process_id"),
                                    "p_workflow_object_id" => $object_id,
                                    "p_workflow_step_id" => $step->workflow_step_id,//Todo first step
                                    "p_note" => "System Assigned",
                                    "p_insert_by" => auth()->id(),
                                    "o_status_code" => &$status_code,
                                    "o_status_message" => &$status_message,
                                ];

                                DB::executeProcedure("pmis.workflow_Process_entry", $params);
                            } catch (\Exception $e) {
                                DB::rollback();
                                $status_message = $e->getMessage();
                                session()->flash('m-class', 'alert-danger');
                                session()->flash('message', $status_message);
                                return redirect()->back()->with('message', $status_message);
                            }
                        } else {
                            DB::rollback();
                            $status_message = 'Something went wrong.';
                            session()->flash('m-class', 'alert-danger');
                            session()->flash('message', $status_message);
                            return redirect()->back()->with('message', $status_message);
                        }
                    }

                } //End: foreach
                DB::commit();
                $status_message = 'Workflow Assigned Successfully.';
                session()->flash('m-class', 'alert-success');
                session()->flash('message', $status_message);
                return redirect()->back()->with('message', $status_message);
            }
        }
    }

    public function send_house_allot_notification($applicationId)
    {
        $ha_applicant = $applicationId;
        $haApplication = HaApplication::with(['allot_point', 'houseallotment'])->find($ha_applicant);

        //Generates Letter

        $p_id = '';
        $statusCode = sprintf("%4000s", "");
        $statusMessage = sprintf('%4000s', '');
        try {
            $params_letter = [
                "p_ALLOT_LETTER_ID" => [
                    "value" => &$p_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],
                "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                "p_ALLOT_LETTER_NO" => $haApplication->houseallotment->board_decision_number,
                "p_APPLICATION_ID" => $ha_applicant,
                "p_HOUSE_ADV_ID" => $haApplication->advertisement_id,
                "p_DELIVERY_YN" => 'N',
                "p_DELIVERY_DATE" => '',
                "p_DELIVERED_BY" => '',
                //"p_RECEIVED_BY"       => '',
                "p_MEMO_NO" => '',
                "p_MEMO_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                //"p_REMARKS"           => $request->get('remarks'),
                "p_insert_by" => Auth()->ID(),
                "o_status_code" => &$statusCode,
                "o_status_message" => &$statusMessage
            ];

            DB::executeProcedure('allotment.allot_letter_entry', $params_letter);

        } catch (\Exception $e) {
            DB::rollBack();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
        }
        if ($params_letter['o_status_code'] == 1)//Trying to get property 'emp_id' of non-object
        {
            DB::update("update  HAS.ALLOT_POINT  set final_approve_yn = 'Y' where APPLICATION_ID = '$ha_applicant'");

            //Sends Notification
            $houseId = $haApplication->houseallotment->house_id;
            $employeeInfo = $this->employeeManager->findEmployeeInformation($haApplication->emp_code);

            //To Applicant
            $houseInfo = HouseList::where('house_id', $houseId)->with('colonylist', 'buildinglist')->first();

            $name = isset($houseInfo->house_code) ? $houseInfo->house_name . ' (' . $houseInfo->house_code . ')' : $houseInfo->house_name;
            $notification_msg = 'Congratulations! A flat has been allotted to you. Colony: ' . $houseInfo->colonylist->colony_name . ', Building: ' . $houseInfo->buildinglist->building_name . ', Flat: ' . $name;
            $coUserId = DB::table('cpa_security.sec_users')->where('emp_id', $employeeInfo['emp_id'])->pluck('user_id')->first();
            if ($coUserId) {
                try {
                    $controller_user_notification = [
                        "p_notification_to" => $coUserId,
                        "p_insert_by" => Auth::id(),
                        "p_note" => $notification_msg,
                        "p_priority" => null,
                        "p_module_id" => 14,
                        "p_target_url" => url('/take-over-application')
                    ];

                    DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                }
            }

            if ($employeeInfo['emp_email']) {
                //Sends Email
                //Mail::to($employeeInfo['emp_email'])->send(new HouseApprove($employeeInfo['emp_name'], $employeeInfo['emp_code']));
                try {
                    //Updates Letter Delivery
                    $p_id = $params_letter['p_ALLOT_LETTER_ID']['value'];
//                                        dd($p_id);
                    $statusCode = sprintf("%4000s", "");
                    $statusMessage = sprintf('%4000s', '');
                    $params_upd_letter = [
                        "p_ALLOT_LETTER_ID" => [
                            "value" => &$p_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                        "p_ALLOT_LETTER_NO" => $haApplication->houseallotment->board_decision_number,
                        "p_APPLICATION_ID" => $ha_applicant,
                        "p_HOUSE_ADV_ID" => $haApplication->advertisement_id,
                        "p_DELIVERY_YN" => 'Y',
                        "p_DELIVERY_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                        "p_DELIVERED_BY" => '',
                        //"p_RECEIVED_BY"       => '',
                        "p_MEMO_NO" => '',
                        "p_MEMO_DATE" => date("Y-m-d", strtotime(Carbon::now())),
                        //"p_REMARKS"           => $request->get('remarks'),
                        "p_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure('allotment.allot_letter_entry', $params_upd_letter);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                }
            }
            DB::commit();
            return ['exception' => true, 'o_status_code' => 1, 'o_status_message' => 'Successfully letter generated and notification sent!'];
        }
        DB::rollBack();
        return $params_letter;
    }
}

