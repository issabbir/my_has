<?php

namespace App\Http\Controllers\has;

use App\entities\houseallotment\LTakeOver;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\Security\HasPermission;

class HandOverController extends Controller
{
    use HasPermission;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    public function electricIndex()
    {
        $user = Auth();
        $logUser = $user->user()->emp_id;
        $empList = DB::select("SELECT distinct a.hand_over_request_id, a.emp_id, b.emp_code
  FROM HAS.HAND_OVER_REQUEST a, PMIS.EMPLOYEE b, house_list h, user_wise_colony u
 WHERE a.emp_id = b.emp_id AND nvl(a.elec_approve_yn,'N') = 'N'
 and A.HOUSE_ID = H.HOUSE_ID
 and H.COLONY_ID = U.COLONY_ID
and U.EMP_ID = $logUser");

        $data = [
            'takeOverType' => $this->loadTakeOverType(),
            'empList' => $empList,
            // 'civilEmployeeList'     => $this->loadEmpCivilEng(), // SELECT2 element is added by commenting out this! No business rule changed here.
            // 'electricEmployeeList' => $this->loadEmpElectricalEng(), // SELECT2 element is added by commenting out this! No business rule changed here.
        ];
        //return view('take-over.allHandOver',compact('data'));
        return view('take-over.allHandOverElectric', compact('data', 'user'));
    }

    public function civilIndex()
    {
        $user = Auth();

        $logUser = $user->user()->emp_id;

        $empList = DB::select("SELECT distinct a.hand_over_request_id, a.emp_id, b.emp_code
  FROM HAS.HAND_OVER_REQUEST a,
       PMIS.EMPLOYEE b,
       house_list h,
       user_wise_colony U
 WHERE     a.emp_id = b.emp_id
       AND nvl(a.civil_approve_yn,'N') = 'N'
       AND A.HOUSE_ID = H.HOUSE_ID
       AND U.COLONY_ID = H.COLONY_ID
       AND U.EMP_ID = $logUser");

        $data = [
            'takeOverType' => $this->loadTakeOverType(),
            'empList' => $empList,
            // 'civilEmployeeList'     => $this->loadEmpCivilEng(), // SELECT2 element is added by commenting out this! No business rule changed here.
            // 'electricEmployeeList' => $this->loadEmpElectricalEng(), // SELECT2 element is added by commenting out this! No business rule changed here.
        ];

        return view('take-over.allHandOverCivil', compact('data', 'user'));
    }

    public function loadTakeOverType($takeOverType_selected = null)
    {
        $takeOverTypeList = LTakeOver::select('take_over_type_id', 'take_over_type')->where('ACTIVE_YN', '=', 'Y')->where('take_over_type_id', '=', '2')->get();
        $takeOverTypeOption = [];
        //$takeOverTypeOption[] = "<option value=''>Please select an option</option>";
        foreach ($takeOverTypeList as $item) {
            $takeOverTypeOption[] = "<option value='" . $item->take_over_type_id . "'" . ($takeOverType_selected == $item->take_over_type_id ? 'selected' : '') . ">" . $item->take_over_type . "</option>";
        }
        return $takeOverTypeOption;
    }

    public function loadEmpCivilEng($emp_manger_selected = null)
    {
        $empEmpCivilEngList = Employee::select('EMP_ID', 'EMP_CODE', 'EMP_NAME')->where('EMP_ACTIVE_YN', '=', 'Y')->where('DPT_DEPARTMENT_ID', '=', '5')->where('EMP_ID', '=', Auth()->user()->emp_id)->get();
        $empOption = [];
        $empOption[] = "<option value=''>Please select an option</option>";
        foreach ($empEmpCivilEngList as $item) {
            $empOption[] = "<option value='" . $item->emp_id . "'" . ($emp_manger_selected == $item->emp_id ? 'selected' : '') . ">" . $item->emp_code . " : " . $item->emp_name . "</option>";
        }
        return $empOption;
    }

    public function loadEmpElectricalEng($emp_manger_selected = null)
    {
        $empEmpElectricalEngList = Employee::select('EMP_ID', 'EMP_CODE', 'EMP_NAME')->where('EMP_ACTIVE_YN', '=', 'Y')->where('DPT_DEPARTMENT_ID', '=', '4')->where('EMP_ID', '=', Auth()->user()->emp_id)->get();
        $empOption = [];
        $empOption[] = "<option value=''>Please select an option</option>";
        foreach ($empEmpElectricalEngList as $item) {
            $empOption[] = "<option value='" . $item->emp_id . "'" . ($emp_manger_selected == $item->emp_id ? 'selected' : '') . ">" . $item->emp_code . " : " . $item->emp_name . "</option>";
        }
        return $empOption;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function civilStore(Request $request)
    {


        $attachment = $request->file('civilAttachment');
        if (!isset($attachment)) {

            $attachmentFileName = '';
            $attachmentFileType = '';
            $attachmentFileContent = '';
        } else {
            $attachmentFileName = $attachment->getClientOriginalName();
            $attachmentFileType = $attachment->getMimeType();
            $attachmentFileContent = base64_encode(file_get_contents($attachment->getRealPath()));

        }

        $letter = $request->get('allot_letter_id');

        $allot_letter = isset($letter) ? $letter : '';

		DB::beginTransaction();
        $params = [];
        try {
            //$p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $hand_over_id = '';
            if (isset($request->old_yn)) {
                if ($request->old_yn == 'N') {

                    $params = [
                        "P_HAND_OVER_ID" => [
                            "value" => &$hand_over_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        "p_TAKE_OVER_TYPE_ID" => $request->get('takeOverType'),
                        "p_ALLOT_LETTER_ID" => $allot_letter,
                        "p_HAND_OVER_CIVIL_EMP" => $request->get("civil_eng"),
                        "P_CIVIL_HAND_OVER_DATE" => (($request->get("hand_over_date")) ? date("Y-m-d", strtotime($request->get("hand_over_date"))) : ''),
                        "p_CIVIL_ENG_COMMENT" => $request->get("civil_eng_comment"),
                        "p_CIVIL_ENG_AUTH_DOC" => [
                            'value' => $attachmentFileContent,
                            'type' => SQLT_CLOB,
                        ],
                        "p_CIVIL_AUTH_DOC_NAME" => $attachmentFileName,
                        "p_CIVIL_AUTH_DOC_TYPE" => $attachmentFileType,
                        "P_CIVIL_ENG_REMARKS" => $request->get("remarks"),
                        "p_SANITARY_FITTINGS" => $request->get("sanitary_fittings"),
                        "p_EMP_ID" => $request->get('emp_id'),
                        "p_OLD_YN" => $request->get('old_yn'),
                        //"p_SANITARY_FITTINGS" => $request->get("sanitary_fittings"),
                        "p_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure('has.hand_over_to_civil', $params);

                } else {

                    $params = [
                        "P_HAND_OVER_ID" => [
                            "value" => &$hand_over_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        "p_HAND_OVER_TYPE_ID" => $request->get('takeOverType'),
                        "p_HOUSE_ID" => $request->get('house_id'),
                        "p_HAND_OVER_CIVIL_EMP" => $request->get("civil_eng"),
                        "P_CIVIL_HAND_OVER_DATE" => (($request->get("hand_over_date")) ? date("Y-m-d", strtotime($request->get("hand_over_date"))) : ''),
                        "p_CIVIL_ENG_COMMENT" => $request->get("civil_eng_comment"),
                        "p_CIVIL_ENG_AUTH_DOC" => [
                            'value' => $attachmentFileContent,
                            'type' => SQLT_CLOB,
                        ],
                        "p_CIVIL_AUTH_DOC_NAME" => $attachmentFileName,
                        "p_CIVIL_AUTH_DOC_TYPE" => $attachmentFileType,
                        "P_CIVIL_ENG_REMARKS" => $request->get("remarks"),
                        "p_SANITARY_FITTINGS" => $request->get("sanitary_fittings"),
                        "p_EMP_ID" => $request->get('emp_id'),
                        "p_OLD_YN" => $request->get('old_yn'),
                        "p_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure('has.hand_over_to_civil_old_data', $params);
                }
            }

			DB::commit();
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            //}
        } catch (\Exception $e) {
			DB::rollback();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function electricStore(Request $request)
    {

        $attachment = $request->file('elecAttachment');
        if (!isset($attachment)) {

            $attachmentFileName = '';
            $attachmentFileType = '';
            $attachmentFileContent = '';
        } else {
            $attachmentFileName = $attachment->getClientOriginalName();
            $attachmentFileType = $attachment->getMimeType();
            $attachmentFileContent = base64_encode(file_get_contents($attachment->getRealPath()));

        }

        $params = [];
		DB::beginTransaction();
        try {
            //$p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $hand_over_id = '';
            if (isset($request->old_yn)) {
                if ($request->old_yn == 'N') {
                    $params = [
                        "P_HAND_OVER_ID" => [
                            "value" => &$hand_over_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        "p_TAKE_OVER_TYPE_ID" => $request->get('takeOverType'),
                        "p_ALLOT_LETTER_ID" => $request->get('allot_letter_id'),
                        "P_HAND_OVER_ELEC_EMP" => $request->get("electrical_eng"),
//                "P_ELEC_HAND_OVER_DATE" => (($request->get("take_over_date")) ? date("Y-m-d", strtotime($request->get("take_over_date"))) : ''),
                        "P_ELEC_HAND_OVER_DATE" => (($request->get("hand_over_date")) ? date("Y-m-d", strtotime($request->get("hand_over_date"))) : ''),
                        "p_ELEC_ENG_COMMENT" => $request->get("electrical_eng_comment"),
                        "p_ELEC_ENG_AUTH_DOC" => [
                            'value' => $attachmentFileContent,
                            'type' => SQLT_CLOB,
                        ],
                        "p_ELEC_AUTH_DOC_NAME" => $attachmentFileName,
                        "p_ELEC_AUTH_DOC_TYPE" => $attachmentFileType,
                        "P_ELEC_ENG_REMARKS" => $request->get("remarks"),
                        "p_ELECTRICAL_FITTINGS" => $request->get("remarks"),
                        "p_EMP_ID" => $request->get('emp_id'),
                        "p_OLD_YN" => $request->get('old_yn'),
                        "p_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure('has.hand_over_to_elec', $params);
                } else {
                    $params = [
                        "P_HAND_OVER_ID" => [
                            "value" => &$hand_over_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        "P_HAND_OVER_TYPE_ID" => $request->get('takeOverType'),
                        "p_HOUSE_ID" => $request->get('house_id'),
                        "P_HAND_OVER_ELEC_EMP" => $request->get("electrical_eng"),
                        "P_ELEC_HAND_OVER_DATE" => (($request->get("hand_over_date")) ? date("Y-m-d", strtotime($request->get("hand_over_date"))) : ''),
                        "p_ELEC_ENG_COMMENT" => $request->get("electrical_eng_comment"),
                        "p_ELEC_ENG_AUTH_DOC" => [
                            'value' => $attachmentFileContent,
                            'type' => SQLT_CLOB,
                        ],
                        "p_ELEC_AUTH_DOC_NAME" => $attachmentFileName,
                        "p_ELEC_AUTH_DOC_TYPE" => $attachmentFileType,
                        "P_ELEC_ENG_REMARKS" => $request->get("remarks"),
                        "p_ELECTRICAL_FITTINGS" => $request->get("remarks"),
                        "p_EMP_ID" => $request->get('emp_id'),
                        "p_OLD_YN" => $request->get('old_yn'),
                        "p_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure('has.hand_over_to_elec_OLD_DATA', $params);
                }
            }


//            if($id){
//                return $params;
//            }else{
			DB::commit();
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            //}
        } catch (\Exception $e) {
			DB::rollback();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //->where('cancel_yn', 'N')
    }

    public function civilDatatableList()
    {
        //DB::enableQueryLog(); // Enable query log
        $register_data = DB::select('SELECT distinct "ALLOT_LETTER"."ALLOT_LETTER_ID",
       "ALLOT_LETTER"."ALLOT_LETTER_DATE",
       "ALLOT_LETTER"."ALLOT_LETTER_NO",
       "ALLOT_LETTER"."MEMO_DATE",
       "ALLOT_LETTER"."MEMO_NO",
       "ALLOT_LETTER"."DELIVERY_YN",
       "ALLOT_LETTER"."ACK_YN",
       "PMIS"."EMPLOYEE"."EMP_NAME",
       "PMIS"."EMPLOYEE"."EMP_CODE",
       "HOUSE_ALLOTTMENT"."TAKE_OVER_DATE"     AS "TAKE_OVER_DATE",
       "HAND_OVER"."CIVIL_HAND_OVER_DATE"      AS "HAND_OVER_DATE",
       "HAND_OVER"."EMP_ID",
       "PMIS"."EMPLOYEE"."EMP_CODE",
       "HAND_OVER"."HAND_OVER_ID",
       "HOUSE_ALLOTTMENT"."ALLOT_ID"
  FROM "HAND_OVER"
       LEFT JOIN "ALLOT_LETTER"
           ON "ALLOT_LETTER"."ALLOT_LETTER_ID" =
              "HAND_OVER"."ALLOT_LETTER_ID"
       LEFT JOIN "PMIS"."EMPLOYEE"
           ON "PMIS"."EMPLOYEE"."EMP_ID" = "HAND_OVER"."EMP_ID"
       LEFT JOIN "HOUSE_ALLOTTMENT"
           ON "HOUSE_ALLOTTMENT"."HOUSE_ID" = "HAND_OVER"."HOUSE_ID"
 WHERE "HAND_OVER"."CIVIL_HAND_OVER_DATE" IS NOT NULL');
//dd($register_data);
        /*
         ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date',
                'allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no',
                'allot_letter.delivery_yn','allot_letter.ack_yn',
                'pmis.employee.emp_name','pmis.employee.emp_code',
                'take_over.TAKE_OVER_DATE as take_over_date','hand_over.TAKE_OVER_DATE as hand_over_date')
            ->join('allot_letter', 'allot_letter.allot_letter_id','=','house_allottment.allot_letter_id')
            ->join('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
            ->join('take_over', 'take_over.take_over_id','=','house_allottment.take_over_id')
            ->leftJoin('take_over as hand_over', 'house_allottment.hand_over_id','=','hand_over.take_over_id')
            ->get();
        */
//        dd($register_data);
        return datatables()->of($register_data)
            ->addColumn('allot_letter_date', function ($register_data) {
                return Carbon::parse($register_data->allot_letter_date)->format('Y-m-d');
            })
            ->addColumn('take_over_date', function ($register_data) {
                return Carbon::parse($register_data->take_over_date)->format('Y-m-d');
            })
            ->addColumn('hand_over_date', function ($register_data) {
                return ($register_data->hand_over_date ? Carbon::parse($register_data->hand_over_date)->format('Y-m-d') : '');
            })
            ->addColumn('action', function ($query) {

                //return '<a target="_blank" title="Takeover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_Take_Over_Report.xdo&p_allot_id='.$query->allot_letter_id.'&type=pdf&filename=takeover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>&nbsp;'.(isset($query->hand_over_date)?'|&nbsp;<a target="_blank" title="Handover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_General_Handover_Report.xdo&p_emp_code='.$query->emp_code.'&type=pdf&filename=handover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>':'');
                return '<a target="_blank" title="Takeover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/RPT_Take_Over_Report.xdo&p_allot_id=' . $query->allot_letter_id . '&type=pdf&filename=takeover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>&nbsp;' . (isset($query->hand_over_date) ? '|&nbsp;<a target="_blank" title="Handover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/Copy+of+RPT_General_Handover_Report.xdo&p_hand_over_id=' . $query->hand_over_id . '&type=pdf&filename=handover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>' : '');
            })
            ->make(true);

    }

    public function electricDatatableList()
    {
        /* $register_data = DB::table('house_allottment')
             ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date',
                 'allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no',
                 'allot_letter.delivery_yn','allot_letter.ack_yn',
                 'pmis.employee.emp_name','pmis.employee.emp_code',
                 'take_over.TAKE_OVER_DATE as take_over_date','hand_over.TAKE_OVER_DATE as hand_over_date')
             ->join('allot_letter', 'allot_letter.allot_letter_id','=','house_allottment.allot_letter_id')
             ->join('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
             ->join('take_over', 'take_over.take_over_id','=','house_allottment.take_over_id')
             ->leftJoin('take_over as hand_over', 'house_allottment.hand_over_id','=','hand_over.take_over_id')
             ->get();*/

        $register_data = DB::select('SELECT "ALLOT_LETTER"."ALLOT_LETTER_ID",
       "ALLOT_LETTER"."ALLOT_LETTER_DATE",
       "ALLOT_LETTER"."ALLOT_LETTER_NO",
       "ALLOT_LETTER"."MEMO_DATE",
       "ALLOT_LETTER"."MEMO_NO",
       "ALLOT_LETTER"."DELIVERY_YN",
       "ALLOT_LETTER"."ACK_YN",
       "PMIS"."EMPLOYEE"."EMP_NAME",
       "PMIS"."EMPLOYEE"."EMP_CODE",
       "HOUSE_ALLOTTMENT"."TAKE_OVER_DATE"     AS "TAKE_OVER_DATE",
       "HAND_OVER"."ELEC_HAND_OVER_DATE"       AS "HAND_OVER_DATE",
       "HAND_OVER"."HAND_OVER_ID",
        "HOUSE_ALLOTTMENT"."ALLOT_ID"
  FROM "HAND_OVER"
       LEFT JOIN "ALLOT_LETTER"
           ON "ALLOT_LETTER"."ALLOT_LETTER_ID" =
              "HAND_OVER"."ALLOT_LETTER_ID"
       LEFT JOIN "PMIS"."EMPLOYEE"
           ON "PMIS"."EMPLOYEE"."EMP_ID" = "HAND_OVER"."EMP_ID"
       LEFT JOIN "HOUSE_ALLOTTMENT"
           ON "HOUSE_ALLOTTMENT"."HOUSE_ID" = "HAND_OVER"."HOUSE_ID"
 WHERE "HAND_OVER"."ELEC_HAND_OVER_DATE" IS NOT NULL');

        return datatables()->of($register_data)
            ->addColumn('allot_letter_date', function ($register_data) {
                return Carbon::parse($register_data->allot_letter_date)->format('Y-m-d');
            })
            ->addColumn('take_over_date', function ($register_data) {
                return Carbon::parse($register_data->take_over_date)->format('Y-m-d');
            })
            ->addColumn('hand_over_date', function ($register_data) {
                return ($register_data->hand_over_date ? Carbon::parse($register_data->hand_over_date)->format('Y-m-d') : '');
            })
            ->addColumn('action', function ($query) {
                return '<a target="_blank" title="Takeover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/RPT_Take_Over_Report.xdo&p_allot_id=' . $query->allot_letter_id . '&type=pdf&filename=takeover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>&nbsp;' . (isset($query->hand_over_date) ? '|&nbsp;<a target="_blank" title="Handover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/RPT_General_Handover_Report.xdo&p_hand_over_id=' . $query->hand_over_id . '&type=pdf&filename=handover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>' : '');
            })
            ->make(true);

    }
}
