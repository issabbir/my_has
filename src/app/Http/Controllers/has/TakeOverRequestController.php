<?php

namespace App\Http\Controllers\has;

use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Datatables;
use App\Traits\Security\HasPermission;

class TakeOverRequestController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    private $employeeManager;

    public function __construct(FlashMessageManager $flashMessageManager, EmployeeContract $employeeManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->employeeManager = $employeeManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $auth_user = Auth::user()->emp_id;
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
       c.house_code,
       C.HOUSE_TYPE_ID,
       HT.HOUSE_TYPE,
       col.COLONY_NAME,
       al.TAKEOVER_REQUEST_YN,
       al.ALLOT_LETTER_NO,
       al.ALLOT_LETTER_DATE,
       al.allot_letter_id,c.HOUSE_CODE
  FROM ALLOT_LETTER al,
       HA_APPLICATION ha,
       HOUSE_ALLOTTMENT hal,
       PMIS.EMPLOYEE d,
       HAS.HOUSE_LIST c,
       HAS.BUILDING_LIST bld,
       HAS.L_COLONY col,
       PMIS.L_DEPARTMENT dpt,
       PMIS.L_DESIGNATION des,
       HAS.L_HOUSE_TYPE ht
 WHERE     ha.APPLICATION_ID = al.APPLICATION_ID
       AND ha.EMP_ID = d.EMP_ID
       AND hal.EMP_ID = ha.EMP_ID
       AND hal.HOUSE_ID = c.HOUSE_ID
       AND c.BUILDING_ID = bld.BUILDING_ID
       AND bld.COLONY_ID = col.COLONY_ID
       and HT.HOUSE_TYPE_ID = C.HOUSE_TYPE_ID
       AND d.DPT_DEPARTMENT_ID = dpt.DEPARTMENT_ID
       AND d.DESIGNATION_ID = des.DESIGNATION_ID
       AND al.TAKEOVER_REQUEST_YN = 'N'
       AND d.EMP_ID =  '$auth_user'
       AND al.DELIVERY_YN = 'Y'
QUERY;

        $data = DB::select($query);

        //return view('handOverApplication.h-index',compact('data'));

        return view('takeoverrequest.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function takeOverRequest(Request $request)
    {
        $allot_letter_id = $request->get('allot_letter_id');
        DB::beginTransaction();
        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $params = [
                "p_ALLOT_LETTER_ID" => $allot_letter_id,
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];
            DB::executeProcedure('HAS.TAKE_OVER_REQUEST_ENTRY', $params);

            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            if ($params['o_status_code'] != 1) {
                DB::rollBack();
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            }
            DB::commit();

            //Send Notification
            //To Electric and Civil
            $house_id = $request->house_id;
            $query = <<<QUERY
SELECT hl.house_name, lc.colony_name, bl.building_name
     FROM HOUSE_LIST hl, BUILDING_LIST bl, L_COLONY lc
    WHERE     hl.building_id = bl.building_id
          AND hl.colony_id = lc.colony_id
          AND hl.house_id = $house_id
QUERY;
            $house_data = DB::select($query);

            $emp = Employee::where('emp_id', $request->employee_id)->first();

            $notificatn_msg = 'User: '.$emp->emp_code.' Name: '.$emp->emp_name.' has been granted a house for takeover. Please, handover House: '.$house_data[0]->house_name.', Building: '.$house_data[0]->building_name.' and Colony: '.$house_data[0]->colony_name.'. Please, take handover of this house. (User: '.$emp->emp_code.' Name: '.$emp->emp_name.')';

            $send_to = DB::table('cpa_security.sec_users u')
                ->select('u.user_id', 'uwc.role_id')
                ->leftJoin('has.user_wise_colony uwc', 'uwc.emp_id', 'u.emp_id')
                ->leftJoin('has.house_list hl', 'hl.colony_id', 'uwc.colony_id')
                ->where('hl.house_id', $house_id)
                ->distinct()
                ->get();

            if ($send_to)
            {
                foreach ($send_to as $user) {
                    if ($user->role_id == 115) { //for civil
                        $controller_user_notification = [
                            "p_notification_to" => $user->user_id,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $notificatn_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => url('/take-over-civil')
                        ];
                        DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                    }
                    else //for electric
                    {
                        $controller_user_notification = [
                            "p_notification_to" => $user->user_id,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $notificatn_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => url('/take-over-elec')
                        ];
                        DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                    }
                }
            }
            /*$emp_code = Employee::where('emp_id', $request->post('employee_id'))->pluck('emp_code')->first();
            $handover_msg = 'A New Handover Application Needs to Review. ('.$emp_code.')';
            //Notification to Civil Eng.
            $controller_user_notification = [
                "p_notification_to" => 2002090172,
                "p_insert_by" => Auth::id(),
                "p_note" => $handover_msg,
                "p_priority" => null,
                "p_module_id" => 14,
                "p_target_url" => url('/civil-allottee_informationhand-over')
            ];
            DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

            //Notification to Electric Eng.
            $controller_user_notification = [
                "p_notification_to" => 2003160217,
                "p_insert_by" => Auth::id(),
                "p_note" => $handover_msg,
                "p_priority" => null,
                "p_module_id" => 14,
                "p_target_url" => url('/electric-hand-over')
            ];
            DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);*/

            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
