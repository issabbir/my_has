<?php

namespace App\Http\Controllers\has;

use App\Entities\Admin\LDepartment;
use App\Entities\Admin\SecUser;
use App\Entities\HouseAllotment\Acknowledgemnt;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\Pmis\Employee\Employee;
use App\Enums\Department;
use App\Enums\HouseStatus;
use App\Http\Controllers\Controller;
use App\Managers\AdvertisementManager;
use App\Managers\FlashMessageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HouseDeptChangeController extends Controller
{
    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    public function index()
    {
//        $acknowledgments = Acknowledgemnt::where('active_yn', 'Y')->get();
        $departments = DB::select('select department_id, department_name from pmis.l_department');

        return view('housedeptchange.index', compact( 'departments'));
    }

    public function store(Request $request)
    {



        $prev_ack = HouseList::where('house_id', $request->houses_id)->pluck('dept_ack_id')->first();

        $attachment = $request->file('change_doc');
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
        $params = [];
        try {

            if (isset($request->dep_emp_change)){
                if ($request->dep_emp_change == 'E'){ //employee

                    $statusCode = sprintf("%4000s", "");
                    $statusMessage = sprintf('%4000s', '');

                    $params = [

                        "P_emp_id" => $request->emp_id,
                        "p_HOUSE_ID" => $request->houses_id,
                        "p_PREV_ACK_ID" => $prev_ack,
                        "P_CURRENT_ACK_ID" => $request->chng_ack,
                        "P_PREV_DEPARTMENT_ID" => $request->prev_dept,
                        "p_CURRENT_DEPARTMENT_ID" => $request->chng_dept,
                        "p_REASON" => $request->reason,
                        "p_CHANGE_DATE" => $request->change_date,
                        "p_ATTACHMENT" => [
                            'value' => $attachmentFileContent,
                            'type' => SQLT_CLOB,
                        ],
                        "p_ATTACHMENT_NAME" => $attachmentFileName,
                        "p_ATTACHMENT_TYPE" => $attachmentFileType,
                        "p_INSERT_BY" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure("HOUSE_EMP_DEP_CHANGE_LOG_ENTRY", $params);
                }else { //previous department
                    foreach ($request->house as $house) {

                        $prev_ack = HouseList::where('house_id', $house)->pluck('dept_ack_id')->first();

                        $statusCode = sprintf("%4000s", "");
                        $statusMessage = sprintf('%4000s', '');
                        $params = [
                            "p_HOUSE_ID" => $house,
                            "p_PREV_ACK_ID" => $prev_ack,
                            "P_CURRENT_ACK_ID" => $request->chng_ack,
                            "P_PREV_DEPARTMENT_ID" => $request->prev_dept,
                            "p_CURRENT_DEPARTMENT_ID" => $request->chng_dept,
                            "p_REASON" => $request->reason,
                            "p_CHANGE_DATE" => $request->change_date,
                            "p_ATTACHMENT" => [
                                'value' => $attachmentFileContent,
                                'type' => SQLT_CLOB,
                            ],
                            "p_ATTACHMENT_NAME" => $attachmentFileName,
                            "p_ATTACHMENT_TYPE" => $attachmentFileType,
                            "p_INSERT_BY" => Auth()->ID(),
                            "o_status_code" => &$statusCode,
                            "o_status_message" => &$statusMessage
                        ];



                        DB::executeProcedure("house_dep_change_log_entry", $params);
//                        dd($params);
                        DB::commit();
                        if($params['o_status_code'] != 1)
                        {
                            DB::rollBack();
                            $flashMessageContent = $this->flashMessageManager->getMessage($params);
                            return redirect()->route('house-dept-change.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
                        }
                        else {
                            if ($request->dep_emp_change == 'E') { //employee

                                //to employee
                                $user = SecUser::where('emp_id', $request->emp_id)->pluck('user_id')->first();


                                $chng_dpt = LDepartment::where('department_id', $request->chng_dept)->first();
                                $notificatn_msg = 'Your house has been transferred to '.$chng_dpt->department_name.' department.';

                                $controller_user_notification = [
                                    "p_notification_to" => $user->user_id,
                                    "p_insert_by" => Auth::id(),
                                    "p_note" => $notificatn_msg,
                                    "p_priority" => null,
                                    "p_module_id" => 14,
                                    "p_target_url" => url('/dashboard')
                                ];
                                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

                                //to department head
                                $users = DB::select('SELECT distinct su.user_id, su.user_name, e.emp_name, e.dpt_department_id
  FROM CPA_SECURITY.SEC_USERS  su
       LEFT JOIN CPA_SECURITY.SEC_USER_ROLES sur ON sur.user_id = su.user_id
       LEFT JOIN pmis.employee e ON e.EMP_ID = su.emp_id
       LEFT JOIN CPA_SECURITY.sec_role sr ON sr.role_id = sur.role_id
 WHERE     sr.ROLE_ID in (147, 13)
 and e.dpt_department_id = '.$request->chng_dept);

                                foreach ($users as $user) {
                                    $emp_info = Employee::where('emp_id', $request->emp_id)->first();
                                    $prev_dpt = LDepartment::where('department_id', $request->dept_id)->first();
                                    $houseInfo = HouseList::where('house_id', $request->houses_id)->with('colonylist', 'buildinglist')->first();
                                    $name = isset($houseInfo->house_code) ? $houseInfo->house_name . ' ('. $houseInfo->house_code .')' : $houseInfo->house_name;
                                    $notification_msg = 'An employee (User: '.$emp_info->emp_code.' Name: '.$emp_info->emp_name.') has been transferred to your department from ' . $prev_dpt->department_name . ' department. House info - Colony: '. $houseInfo->colonylist->colony_name .', Building: '. $houseInfo->buildinglist->building_name .', Flat: '. $name ;

                                    $controller_user_notification = [
                                        "p_notification_to" => $user->user_id,
                                        "p_insert_by" => Auth::id(),
                                        "p_note" => $notification_msg,
                                        "p_priority" => null,
                                        "p_module_id" => 14,
                                        "p_target_url" => url('/report-generators')
                                    ];
                                    DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

                                }

                            }
                            else //previous department
                            {
                                $prev_dpt = LDepartment::where('department_id', $request->prev_dept)->first();
                                $users = DB::select('SELECT distinct su.user_id, su.user_name, e.emp_name, e.dpt_department_id
  FROM CPA_SECURITY.SEC_USERS  su
       LEFT JOIN CPA_SECURITY.SEC_USER_ROLES sur ON sur.user_id = su.user_id
       LEFT JOIN pmis.employee e ON e.EMP_ID = su.emp_id
       LEFT JOIN CPA_SECURITY.sec_role sr ON sr.role_id = sur.role_id
 WHERE     sr.ROLE_ID in (147, 13)
 and e.dpt_department_id = '.$request->chng_dept);

                                foreach ($users as $user) {
                                        $noOfHouse = count($request->house);
                                        if ($noOfHouse > 1) {
                                            $notification_msg = 'Congratulations! '.$noOfHouse.' flats has been transferred to your department from ' . $prev_dpt->department_name . ' department.';

                                        }
                                        else
                                        {
                                            $notification_msg = 'Congratulations! A flat has been transferred to your department from ' . $prev_dpt->department_name . ' department.';
                                        }
                                        $controller_user_notification = [
                                            "p_notification_to" => $user->user_id,
                                            "p_insert_by" => Auth::id(),
                                            "p_note" => $notification_msg,
                                            "p_priority" => null,
                                            "p_module_id" => 14,
                                            "p_target_url" => url('/report-generators')
                                        ];
                                        DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);

                                }
                            }
                        }
                    }
                }
            }
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->route('house-dept-change.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
        }
        catch (\Exception $e) {
            return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }


    public function gethousebyemp($empID){

       $data = DB::selectOne("SELECT e.emp_id,
       DS.DESIGNATION_ID,
       DS.DESIGNATION,
       D.DEPARTMENT_ID,
       D.DEPARTMENT_name,
       E.ACTUAL_GRADE_ID,
       C.COLONY_ID,
       C.COLONY_NAME,
       B.BUILDING_ID,
       B.BUILDING_NAME,
       B.BUILDING_NO,
       B.BUILDING_ROAD_NO,
       HP.HOUSE_TYPE_ID,
       HP.HOUSE_TYPE,
       H.HOUSE_ID,
       CASE
          WHEN h.DORMITORY_YN = 'N' THEN HL.HOUSE_NAME
          ELSE hl.house_code
       END
          AS HOUSE_NAME,
       CASE WHEN h.DORMITORY_YN = 'N' THEN 'No' ELSE 'Yes' END AS DORMITORY
  FROM house_allottment h,
       house_list hl,
       PMIS.EMPLOYEE e,
       PMIS.L_DEPARTMENT d,
       l_colony c,
       BUILDING_LIST b,
       l_house_type hp,
       PMIS.L_DESIGNATION ds
 WHERE     h.emp_id = $empID
       AND H.HOUSE_ID = HL.HOUSE_ID
       AND H.EMP_ID = E.EMP_ID
       AND D.DEPARTMENT_ID = E.DPT_DEPARTMENT_ID
       AND HL.COLONY_ID = C.COLONY_ID
       AND HL.BUILDING_ID = B.BUILDING_ID
       AND HL.HOUSE_TYPE_ID = HP.HOUSE_TYPE_ID
       AND DS.DESIGNATION_ID = E.DESIGNATION_ID");
       return response()->json($data);
    }



    public function datatable()
    {
        $change_data = DB::select('SELECT HC.HOUSE_DEP_CHANGE_LOG_ID,
       HC.HOUSE_NAME,
       HC.HOUSE_CODE,
       C.COLONY_NAME,
       B.BUILDING_NAME,
       HC.DORMITORY_YN,
       D.DEPARTMENT_NAME AS PREV_DEPARTMENT,
       DN.DEPARTMENT_NAME AS CURRENT_DEPARTMENT
  FROM HOUSE_DEP_CHANGE_LOG HC
       LEFT JOIN PMIS.L_DEPARTMENT D
          ON D.DEPARTMENT_ID = HC.PREV_DEPARTMENT_ID
       LEFT JOIN PMIS.L_DEPARTMENT DN
          ON DN.DEPARTMENT_ID = HC.CURRENT_DEPARTMENT_ID
       LEFT JOIN L_COLONY C ON C.COLONY_ID = HC.COLONY_ID
       LEFT JOIN BUILDING_LIST B ON B.BUILDING_ID = HC.BUILDING_ID
       ORDER BY HC.HOUSE_DEP_CHANGE_LOG_ID DESC');
//dd($change_data[1]);
        return datatables()->of($change_data)
            ->addIndexColumn()
//            ->addColumn('attachment', function ($query) {
//                return '<a target="_blank" title="Takeover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_Take_Over_Report.xdo&p_allot_id='.$query->allot_letter_id.'&type=pdf&filename=takeover_letter"  class="m-2" ><i class="bx bx-download cursor-pointer"></i></a>&nbsp;'.(isset($register_data->hand_over_date)?'|&nbsp;<a target="_blank" title="Handover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_General_Handover_Report.xdo&p_emp_code='.$query->emp_code.'&type=pdf&filename=handover_letter"  class="m-2"><i class="bx bx-download cursor-pointer"></i></a>':'');
//            })
            ->make(true);
    }
}
