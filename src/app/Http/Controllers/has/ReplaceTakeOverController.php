<?php

namespace App\Http\Controllers\has;

use App\entities\houseallotment\LTakeOver;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Security\HasPermission;

class ReplaceTakeOverController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'takeOverType' => $this->loadTakeOverType(),
            // 'civilEmployeeList'     => $this->loadEmpCivilEng(), // SELECT2 element is added by commenting out this! No business rule changed here.
            // 'electricEmployeeList' => $this->loadEmpElectricalEng(),  // SELECT2 element is added by commenting out this! No business rule changed here.
        ];
        return view('take-over.replaceIndex', compact('data'));
    }

    public function loadTakeOverType($takeOverType_selected = null)
    {
        $takeOverTypeList = LTakeOver::select('take_over_type_id', 'take_over_type')->where('ACTIVE_YN', '=', 'Y')->where('take_over_type_id', '=', '1')->get();
        $takeOverTypeOption = [];
        //$takeOverTypeOption[] = "<option value=''>Please select an option</option>";
        foreach ($takeOverTypeList as $item) {
            $takeOverTypeOption[] = "<option value='" . $item->take_over_type_id . "'" . ($takeOverType_selected == $item->take_over_type_id ? 'selected' : '') . ">" . $item->take_over_type . "</option>";
        }
        return $takeOverTypeOption;
    }

    public function loadEmpCivilEng($emp_manger_selected = null)
    {
        $empEmpCivilEngList = Employee::select('EMP_ID', 'EMP_CODE', 'EMP_NAME')->where('EMP_ACTIVE_YN', '=', 'Y')->where('DPT_DEPARTMENT_ID', '=', '5')->get();
        $empOption = [];
        $empOption[] = "<option value=''>Please select an option</option>";
        foreach ($empEmpCivilEngList as $item) {
            $empOption[] = "<option value='" . $item->emp_id . "'" . ($emp_manger_selected == $item->emp_id ? 'selected' : '') . ">" . $item->emp_code . " : " . $item->emp_name . "</option>";
        }
        return $empOption;
    }

    public function loadEmpElectricalEng($emp_manger_selected = null)
    {
        $empEmpElectricalEngList = Employee::select('EMP_ID', 'EMP_CODE', 'EMP_NAME')->where('EMP_ACTIVE_YN', '=', 'Y')->where('DPT_DEPARTMENT_ID', '=', '4')->get();
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
    public function store(Request $request)
    {

        $params = [];
        try {
            //$p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            if (1 == $request->get('takeOverType')) {
                $params = [
                    //"p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                    //"p_EMP_ID"                => $request->get('emp_id'),
                    "p_ALLOT_LETTER_ID" => $request->get('allot_letter_id'),
                    "p_TAKE_OVER_DATE" => (($request->get("take_over_date")) ? date("Y-m-d", strtotime($request->get("take_over_date"))) : ''),
                    "p_TAKE_OVER_CIVIL_EMP" => $request->get("civil_eng"),
                    "p_TAKE_OVER_ELEC_EMP" => $request->get("electrical_eng"),
                    "p_CIVIL_ENG_COMMENT" => $request->get("civil_eng_comment"),
                    "p_ELEC_ENG_COMMENT" => $request->get("electrical_eng_comment"),
                    "p_HOUSE_DETAILS" => $request->get("house_details"),
                    "p_SANITARY_FITTINGS" => $request->get("sanitary_fittings"),
                    "p_ELECTRICAL_FITTINGS" => $request->get("electrical_fittings"),
                    "p_AUTH_DOC" => '',//$request->get("electrical_eng_comment"),
                    "p_REMARKS" => $request->get("remarks"),
                    "p_insert_by" => Auth()->ID(),
                    "o_status_code" => &$statusCode,
                    "o_status_message" => &$statusMessage
                ];
                DB::executeProcedure('has.replace_take_over', $params);
            } else {
                $params = [
                    // "p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                    "p_EMP_ID" => $request->get('emp_id'),
                    //"p_ALLOT_LETTER_ID"       => $request->get('allot_letter_id'),
                    "p_HAND_OVER_DATE" => (($request->get("take_over_date")) ? date("Y-m-d", strtotime($request->get("take_over_date"))) : ''),
                    "p_TAKE_OVER_CIVIL_EMP" => $request->get("civil_eng"),
                    "p_TAKE_OVER_ELEC_EMP" => $request->get("electrical_eng"),
                    "p_CIVIL_ENG_COMMENT" => $request->get("civil_eng_comment"),
                    "p_ELEC_ENG_COMMENT" => $request->get("electrical_eng_comment"),
                    "p_HOUSE_DETAILS" => $request->get("house_details"),
                    "p_SANITARY_FITTINGS" => $request->get("sanitary_fittings"),
                    "p_ELECTRICAL_FITTINGS" => $request->get("electrical_fittings"),
                    "p_AUTH_DOC" => '',//$request->get("electrical_eng_comment"),
                    "p_REMARKS" => $request->get("remarks"),
                    "p_insert_by" => Auth()->ID(),
                    "o_status_code" => &$statusCode,
                    "o_status_message" => &$statusMessage
                ];
                DB::executeProcedure('has.ha_hand_over', $params);
            }

//            if($id){
//                return $params;
//            }else{
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            //}
        } catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function datatableList()
    {
        $register_data = DB::table('house_allottment')
            ->join('replacement_application', 'house_allottment.allot_id', '=', 'replacement_application.allot_id')
            ->join('allot_letter', 'allot_letter.replace_app_id', '=', 'replacement_application.replace_app_id')
            ->join('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
             ->leftJoin('take_over', 'take_over.take_over_id', '=', 'house_allottment.take_over_id')
             ->leftJoin('hand_over', 'hand_over.hand_over_id', '=', 'house_allottment.hand_over_id')
//            ->leftJoin('take_over as hand_over', 'house_allottment.hand_over_id', '=', 'hand_over.take_over_id')
//            ->where('allot_letter.allot_letter_id', '=', 'take_over.allot_letter_id')
            ->whereNotNull('take_over.take_over_civil_emp')
            ->select(
                'allot_letter.allot_letter_id',
                'allot_letter.allot_letter_date',
                'allot_letter.allot_letter_no',
                 'allot_letter.memo_date',
                 'allot_letter.memo_no',
                'allot_letter.delivery_yn',
                 'allot_letter.ack_yn',
                'pmis.employee.emp_name', 'pmis.employee.emp_code',
                'take_over.take_over_id',
                'house_allottment.take_over_date as take_over_date',
            'hand_over.update_date as hand_over_date')


            ->get();

        //return print($register_data[0]->hand_over_date); die();
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
//            ->addColumn('delivery_yn', function($register_data) {
//                if($register_data->delivery_yn == 'Y'){
//                    return 'Delivered';
//                }else{
//                    return 'Not Delivered';
//                }
//            })
            ->addColumn('action', function ($query) {
                return '<a target="_blank" title="Takeover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/RPT_Replacement_takeover.xdo&p_allot_id=' . $query->allot_letter_id . '&type=pdf&filename=takeover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>&nbsp;' . (isset($query->hand_over_date) ? '|&nbsp;<a target="_blank" title="Handover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/RPT_REPLACEMENT_HANDOVER_LETTER.xdo &p_emp_code=' . $query->emp_code . '&type=pdf&filename=replacement_handover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>' : '');
            })
            ->make(true);

    }
    public function elecdatatableList()
    {
        $register_data = DB::table('house_allottment')
            ->join('replacement_application', 'house_allottment.allot_id', '=', 'replacement_application.allot_id')
            ->join('allot_letter', 'allot_letter.replace_app_id', '=', 'replacement_application.replace_app_id')
            ->join('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
             ->leftJoin('take_over', 'take_over.take_over_id', '=', 'house_allottment.take_over_id')
             ->leftJoin('hand_over', 'hand_over.hand_over_id', '=', 'house_allottment.hand_over_id')
//            ->leftJoin('take_over as hand_over', 'house_allottment.hand_over_id', '=', 'hand_over.take_over_id')
//            ->where('allot_letter.allot_letter_id', '=', 'take_over.allot_letter_id')
            ->whereNotNull('take_over.take_over_elec_emp')
            ->select(
                'allot_letter.allot_letter_id',
                'allot_letter.allot_letter_date',
                'allot_letter.allot_letter_no',
                 'allot_letter.memo_date',
                 'allot_letter.memo_no',
                'allot_letter.delivery_yn',
                 'allot_letter.ack_yn',
                'pmis.employee.emp_name', 'pmis.employee.emp_code',
                'take_over.take_over_id',
                'house_allottment.take_over_date as take_over_date',
            'hand_over.update_date as hand_over_date')


            ->get();

        //return print($register_data[0]->hand_over_date); die();
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
//            ->addColumn('delivery_yn', function($register_data) {
//                if($register_data->delivery_yn == 'Y'){
//                    return 'Delivered';
//                }else{
//                    return 'Not Delivered';
//                }
//            })
            ->addColumn('action', function ($query) {
                return '<a target="_blank" title="Takeover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/RPT_Replacement_takeover.xdo&p_allot_id=' . $query->allot_letter_id . '&type=pdf&filename=takeover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>&nbsp;' . (isset($query->hand_over_date) ? '|&nbsp;<a target="_blank" title="Handover Letter" href="' . request()->root() . '/report/render?xdo=/~weblogic/HAS/RPT_REPLACEMENT_HANDOVER_LETTER.xdo &p_emp_code=' . $query->emp_code . '&type=pdf&filename=replacement_handover_letter" class="m-2"><i class="bx bx-download cursor-pointer"></i></a>' : '');
            })
            ->make(true);

    }


    public function civilIndex()
    {
        $data = [
            'takeOverType' => $this->loadTakeOverType(),
            'loggedUser' => auth()->user(),

        ];
        return view('take-over.replaceCivil', compact('data'));


    }

    public function civilStore(Request $request)
    {

        $params = [];
        try {
            //$p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');


            DB::beginTransaction();
            $params = [
                // "p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                "p_ALLOT_LETTER_ID" => $request->get('allot_letter_id'),
                "p_TAKE_OVER_DATE" => (($request->get("take_over_date")) ? date("Y-m-d", strtotime($request->get("take_over_date"))) : ''),
                "p_TAKE_OVER_CIVIL_EMP" => $request->get("civil_eng"),
                "p_CIVIL_ENG_COMMENT" => $request->get("civil_eng_comment"),
                "p_HOUSE_DETAILS" => $request->get("house_details"),
                "p_SANITARY_FITTINGS" => $request->get("sanitary_fittings"),
                "p_REMARKS" => $request->get("remarks"),
                "P_WATER_TAP" => $request->get("water_tap"),
                "p_insert_by" => Auth()->ID(),
                "o_status_code" => &$statusCode,
                "o_status_message" => &$statusMessage
            ];

            DB::executeProcedure('has.ha_replace_take_over_civil', $params);
//dd($params);
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


    public function elecIndex()
    {
        $data = [
            'takeOverType' => $this->loadTakeOverType(),
            'loggedUser' => auth()->user(),

        ];
        return view('take-over.replaceElec', compact('data'));


    }

    public function elecStore(Request $request)
    {
//dd($request);

        $params = [];
        try {
            //$p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            DB::beginTransaction();
            $params = [
                // "p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                "p_ALLOT_LETTER_ID" => $request->get('allot_letter_id'),
                "p_Take_OVER_DATE" => (($request->get("take_over_date")) ? date("Y-m-d", strtotime($request->get("take_over_date"))) : ''),
                "p_TAKE_OVER_ELEC_EMP" => $request->get("elec_engg"),
                "p_ELEC_ENG_COMMENT" => $request->get("electrical_eng_comment"),
                "p_HOUSE_DETAILS" => $request->get("house_details"),
                "p_ELECTRICAL_FITTINGS" => $request->get("electrical_fittings"),
                "p_REMARKS" => $request->get("remarks"),
                "P_WATER_TAP" => $request->get("water_tap"),
                "p_insert_by" => Auth()->ID(),
                "o_status_code" => &$statusCode,
                "o_status_message" => &$statusMessage
            ];

            DB::executeProcedure('has.ha_replace_take_over_elec', $params);

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

}
