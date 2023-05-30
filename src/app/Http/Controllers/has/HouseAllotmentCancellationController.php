<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 5:33 PM
 */

namespace App\Http\Controllers\has;

use App\entities\houseAllotment\AllotLetter;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\HouseAllotment\LHouseStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;

use App\Entities\Pmis\Employee\Employee;
use Illuminate\Support\Facades\Auth;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;

class HouseAllotmentCancellationController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    public function index(Request $request)
    {
//        $houseAllotments = HouseAllotment::select('*')->where([['cancel_yn', 'N'],['allot_yn', 'Y']])->with(['employee', 'house']);
//
////        $houseAllotments = HouseAllotment::all()->where([['cancel_yn', 'N'],['allot_yn', 'Y']])->with(['employee', 'house']);
//
//        dd($houseAllotments);

        return view('houseallotmentcancellation.index');
    }

    public function edit(Request $request, $allotmentId)
    {
        $houseAllotment = HouseAllotment::with(['employee', 'house'])->find($allotmentId);
        $houseStatuses = LHouseStatus::whereIn('house_status_id', [1, 3, 5])->get();

        $with = [
            'houseAllotment' => $houseAllotment,
            'houseStatuses' => $houseStatuses
        ];

        return view('houseallotmentcancellation.form')->with($with)->render();
    }

    public function cancelRequest(Request $request, $allotmentId)
    {
//        $users= DB::table('pmis.employee emp')
//            ->select('emp.emp_code')
//            ->leftJoin('cpa_security.sec_users su', 'su.user_name', '=', 'emp.emp_code')
//            ->leftJoin('cpa_security.sec_user_roles sur', 'sur.user_id', '=', 'su.user_id')
//            ->leftJoin('cpa_security.sec_role sr', 'sr.role_id', '=', 'sur.role_id')
//            ->leftJoin('pmis.workflow_steps ws', 'ws.user_role', '=', 'sr.role_key')
//            ->leftJoin('has.ha_application ha', 'ha.workflow_process', '=', 'ws.approval_workflow_id')
////            ->leftJoin('pmis.l_approval_workflows law','law.approval_workflow_id', '=','ha.workflow_process')
//            ->leftJoin('has.house_allottment hal','ha.application_id', '=','hal.application_id')
//            ->where('emp.dpt_department_id', '=',16)
//            ->where('hal.allot_id', $allotmentId)
//            ->distinct()
//            ->get();

        $params = $this->houseAllotmentCancelRequest($request, $allotmentId);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        //send notification
        $query = <<<QUERY
SELECT  distinct su.user_id
  FROM PMIS.EMPLOYEE  emp
       LEFT JOIN CPA_SECURITY.SEC_USERS su
           ON SU.USER_NAME = EMP.EMP_CODE
       LEFT JOIN CPA_SECURITY.SEC_USER_ROLES sur
           ON SUR.USER_ID = SU.USER_ID
       LEFT JOIN CPA_SECURITY.SEC_ROLE sr
           ON SR.ROLE_ID = SUR.ROLE_ID
       LEFT JOIN PMIS.WORKFLOW_STEPS ws
           ON WS.USER_ROLE = SR.ROLE_KEY
       LEFT JOIN HAS.HA_APPLICATION ha
           ON HA.WORKFLOW_PROCESS = WS.APPROVAL_WORKFLOW_ID
       LEFT JOIN PMIS.L_APPROVAL_WORKFLOWS law
           ON LAW.APPROVAL_WORKFLOW_ID = HA.WORKFLOW_PROCESS
       LEFT JOIN HAS.HOUSE_ALLOTTMENT hal
           ON HA.APPLICATION_ID = HAL.APPLICATION_ID
 WHERE     EMP.DPT_DEPARTMENT_ID = law.department_id
       AND HAL.ALLOT_ID = $allotmentId
QUERY;
        $users = DB::select($query);

        if($users) {
            $name = Employee::where('emp_id', auth()->user()->emp_id)->pluck('emp_name')->first();
            $query_2 = <<<QUERY
SELECT hl.house_name, lc.colony_name, bl.building_name
     FROM HOUSE_LIST hl, BUILDING_LIST bl, L_COLONY lc, HOUSE_ALLOTTMENT ha
    WHERE     hl.building_id = bl.building_id
          AND hl.colony_id = lc.colony_id
          AND hl.house_id = ha.house_id
          AND ha.allot_id = $allotmentId
QUERY;
            $house_data = DB::select($query_2);

            foreach ($users as $user) {
                $notification_msg = 'User: '.auth()->user()->user_name.' Name: '.$name.' has cancelled allotment for House: '.$house_data[0]->house_name.', Building: '.$house_data[0]->building_name.' and Colony: '.$house_data[0]->colony_name;
                $controller_user_notification = [
                    "p_notification_to" => $user->user_id,
                    "p_insert_by" => Auth::id(),
                    "p_note" => $notification_msg,
                    "p_priority" => null,
                    "p_module_id" => 14,
                    "p_target_url" => '/point-assessments'
                ];
                try {
                    DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                } catch (\Exception $e) {
                    DB::rollback();
                    $error =  ["exception" => true, "status" => false, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
                    $flashMessageContent = $this->flashMessageManager->getMessage($error);
                    return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
                }
            }
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function houseAllotmentCancelRequest(Request $request, $allotmentId)
    {

        $houseAllotmentCancelRequest = $request->post();

//        $houseAllotment = HouseAllotment::find($allotmentId);

        if ($allotmentId) {
            try {
                $statusCode = sprintf("%4000s", "");
                $statusMessage = sprintf('%4000s', '');
                $params = [
                    'p_ALLOT_ID' => $allotmentId,
//                    'p_HOUSE_STATUS_ID' => $houseAllotmentCancelRequest['house_status_id'],
                    'p_CANCEL_REASON' => $houseAllotmentCancelRequest['reason'],
                    'p_INSERT_BY' => auth()->id(),
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];

//                dd($params);

                DB::executeProcedure('cancel_allot_request', $params);

//                dd($params);
                return $params;
            } catch (\Exception $e) {
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
            }
        } else {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => 'Application not found!'];
            //return [  "exception" => true, "class" => 'error', "message" => 'Application not found!'];
        }
    }

    public function update(Request $request, $allotmentId)
    {
        $params = $this->cancelHouseAllotment($request, $allotmentId);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function cancelHouseAllotment(Request $request, $allotmentId)
    {

//        dd($allotmentId);
        $cancelHouseAllotment = $request->post();
//        $houseAllotment = HouseAllotment::get($allotmentId);

//dd($houseAllotment);

        if ($allotmentId) {
            try {
                $statusCode = sprintf("%4000s", "");
                $statusMessage = sprintf('%4000s', '');
                $params = [
                    'p_ALLOT_ID' => $allotmentId,
                    'p_INSERT_BY' => auth()->id(),
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];

                DB::executeProcedure('allot_cancel', $params);

//                dd($params);

                return $params;
            } catch (\Exception $e) {
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
            }
        } else {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => 'Application not found!'];
            //return [  "exception" => true, "class" => 'error', "message" => 'Application not found!'];
        }
    }

    public function datatableList(Request $request)
    {
        $auth_user = Auth::user()->emp_id;

//        dd($auth_user);

        if (Auth::user()->hasPermission('CAN_ALLOTMENT_CANCEL')) {

            $houseAllotments = HouseAllotment::select('*')->where([['cancel_req_yn', 'Y'],['cancel_yn', 'N']])
                ->where('take_over_id', null)->with(['employee', 'house']); // Cancel requested data

        } else {

            $houseAllotments = HouseAllotment::where('house_allottment.emp_id', $auth_user)
                ->where('cancel_req_yn', 'N')
                ->where('cancel_yn', 'N')
                ->where('take_over_id', null)
                ->with(['employee', 'house'])
                ->get();

            //checks if allot letter is delivered
            $allot_letter = '';
            if($houseAllotments && isset($houseAllotments[0]->application_id)) {
                $allot_letter = AllotLetter::where('application_id', $houseAllotments[0]->application_id)->first();

                if(!$allot_letter || $allot_letter->delivery_yn != 'Y') {
                    $houseAllotments = [];
                }
            }



        }
//dd($houseAllotments[0]);
//        $houseAllotments = HouseAllotment::select('*')->where([['cancel_req_yn', 'N'],['cancel_yn', 'N'],['take_over_id', null]])->with(['employee', 'house']);


        return datatables()->of($houseAllotments)
            ->addColumn('action', function ($query) {
                return '<a data-allotment-id="' . $query->allot_id . '" data-toggle="modal" data-target="#houseAllotmentCancellationModal" href="#" data-backdrop="static" data-keyboard="false" title="Populate Information"><i class="bx bx-detail cursor-pointer"></i></a>';
            })
            ->make(true);
    }



    public function cancelList(Request $request){

        $data = DB::select("SELECT hc.APPLICATION_ID,
       HC.EMP_ID,
       e.emp_code,
       e.emp_name,
       DP.DEPARTMENT_NAME,
       DS.DESIGNATION,
       HC.CANCEL_YN,
       HC.CANCEL_REASON,
       HC.INSERT_DATE,
       HC.HOUSE_ID,
       H.HOUSE_NAME
  FROM HAS.HIST_HOUSE_ALLOTTMENT hc,
       HAS.HOUSE_LIST h,
       PMIS.EMPLOYEE e,
       PMIS.L_DEPARTMENT dp,
       PMIS.L_DESIGNATION ds
 WHERE     HC.CANCEL_YN = 'Y'
       AND HC.HOUSE_ID = H.HOUSE_ID
       AND E.EMP_ID = HC.EMP_ID
       AND E.DESIGNATION_ID = DS.DESIGNATION_ID
       AND E.DPT_DEPARTMENT_ID = DP.DEPARTMENT_ID");

        return datatables()->of($data)
            ->make(true);

    }


}
