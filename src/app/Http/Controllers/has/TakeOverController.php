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

class TakeOverController extends Controller
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

    public function index()
    {
        // Has Permission Example.
        // $hasPermission = auth()->user()->hasPermission("CAN_OT_ACTUAL_DEPT_HEAD");

        $data =[
            'takeOverType'          => $this->loadTakeOverType(),
            // 'civilEmployeeList'     => $this->loadEmpCivilEng(), // SELECT2 element is added by commenting out this! No business rule changed here.
            // 'electricEmployeeList' => $this->loadEmpElectricalEng(),  // SELECT2 element is added by commenting out this! No business rule changed here.
        ];
        return view('take-over.index',compact('data'));
    }

    public function takeoverElec() {

        $loggedUserCode = (string) auth()->user()->user_name;
        $loggedUserId = (string) auth()->user()->emp_id;

        $data =[
            'takeOverType'          => $this->loadTakeOverType(),
             'electricEmployeeList' => $this->loadEmpElectricalEng(),  // SELECT2 element is added by commenting out this! No business rule changed here.
        ];
        return view('take-over.take-over-elec',compact('data','loggedUserCode','loggedUserId'));
    }

    public function takeoverCivil() {


        $loggedUserCode = (string) auth()->user()->user_name;
        $loggedUserId = (string) auth()->user()->emp_id;

        $data =[
            'takeOverType'          => $this->loadTakeOverType(),
            'civilEmployeeList'     => $this->loadEmpCivilEng(), // SELECT2 element is added by commenting out this! No business rule changed here.
        ];

        return view('take-over.take-over-civil',compact('data','loggedUserCode','loggedUserId'));
    }

    public function loadTakeOverType($takeOverType_selected = null)
    {
        $takeOverTypeList = LTakeOver::select('take_over_type_id','take_over_type')->where('ACTIVE_YN','=','Y')->where('take_over_type_id','=','1')->get();
        $takeOverTypeOption = [];
        //$takeOverTypeOption[] = "<option value=''>Please select an option</option>";
        foreach ($takeOverTypeList as $item) {
            $takeOverTypeOption[] = "<option value='".$item->take_over_type_id."'".($takeOverType_selected == $item->take_over_type_id ? 'selected':'').">".$item->take_over_type."</option>";
        }
        return $takeOverTypeOption;
    }

    public function loadEmpCivilEng($emp_manger_selected = null)
    {
        $empEmpCivilEngList = Employee::select('EMP_ID','EMP_CODE','EMP_NAME')->where('EMP_ACTIVE_YN','=','Y')->where('DPT_DEPARTMENT_ID','=','5')->get();
        $empOption = [];
        $empOption[] = "<option value=''>Please select an option</option>";
        foreach ($empEmpCivilEngList as $item) {
            $empOption[] = "<option value='".$item->emp_id."'".($emp_manger_selected == $item->emp_id ? 'selected':'').">".$item->emp_code." : ".$item->emp_name."</option>";
        }
        return $empOption;
    }

    public function loadEmpElectricalEng($emp_manger_selected = null)
    {
        $empEmpElectricalEngList = Employee::select('EMP_ID','EMP_CODE','EMP_NAME')->where('EMP_ACTIVE_YN','=','Y')->where('DPT_DEPARTMENT_ID','=','4')->get();
        $empOption = [];
        $empOption[] = "<option value=''>Please select an option</option>";
        foreach ($empEmpElectricalEngList as $item) {
            $empOption[] = "<option value='".$item->emp_id."'".($emp_manger_selected == $item->emp_id ? 'selected':'').">".$item->emp_code." : ".$item->emp_name."</option>";
        }
        return $empOption;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        $params =[];
        try {
            //$p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
//
            $attach_civil = $request->file('attach_civil');

            $allotattachedFileContent = base64_encode(file_get_contents($attach_civil->getRealPath()));
            if(1 == $request->get('takeOverType')){
                $attach_civil = $request->file('attach_civil');
                if ($attach_civil){
                    $allotattachedFileContent = base64_encode(file_get_contents($attach_civil->getRealPath()));
                }else
                {
                    $allotattachedFileContent = '';
                }
//                $allotattachedFileContent = base64_encode(file_get_contents($attach_civil->getRealPath()));
                $params = [
                    "p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                    "p_EMP_ID"                => $request->get('emp_id'),
                    "p_ALLOT_LETTER_ID"       => $request->get('allot_letter_id'),
                    "p_TAKE_OVER_DATE_CIVIL"        => (($request->get("take_over_date_civil"))? date("Y-m-d", strtotime($request->get("take_over_date_civil"))):''),
                   "p_TAKE_OVER_CIVIL_EMP"   => $request->get("civil_emp_id"),
                    "p_CIVIL_ENG_COMMENT"     => $request->get("civil_eng_comment"),
                    "p_HOUSE_DETAILS"         => $request->get("house_details"),
                    "p_SANITARY_FITTINGS"     => $request->get("sanitary_fittings"),
                    "p_AUTH_DOC_CIVIL"              =>  [
                        'value' =>  $allotattachedFileContent,
//                        'type'  => \PDO::PARAM_LOB,
                        'type'  => SQLT_CLOB,
                    ],
                    "p_REMARKS"               => $request->get("remarks"),
                    "p_DORMITORY_YN"          => null,
                    "p_WATER_TAP"               =>$request->get("water_tap"),
                    "p_insert_by"             => Auth()->ID(),
                    "o_status_code"           => &$statusCode,
                    "o_status_message"        => &$statusMessage
                ];

                DB::executeProcedure('has.TAKE_OVER_TO_CIVIL', $params);

            }else{
                $params = [
                   // "p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                    "p_EMP_ID"                => $request->get('emp_id'),
                    "p_ALLOT_LETTER_ID"       => $request->get('allot_letter_id'),
                    "p_TAKE_OVER_DATE_CIVIL"        => (($request->get("take_over_date_civil"))? date("Y-m-d", strtotime($request->get("take_over_date_civil"))):''),
                    "p_TAKE_OVER_CIVIL_EMP"   => $request->get("civil_emp_id"),
                    "p_CIVIL_ENG_COMMENT"     => $request->get("civil_eng_comment"),
                    "p_HOUSE_DETAILS"         => $request->get("house_details"),
                    "p_SANITARY_FITTINGS"     => $request->get("sanitary_fittings"),
                    "p_AUTH_DOC_CIVIL"              =>  [
                        'value' =>  $allotattachedFileContent,
//                        'type'  => \PDO::PARAM_LOB,
                        'type'  => SQLT_CLOB,
                    ],
                    "p_REMARKS"               => $request->get("remarks"),
                    "p_DORMITORY_YN"          => null,
                    "p_WATER_TAP"               =>$request->get("water_tap"),
                    "p_insert_by"             => Auth()->ID(),
                    "o_status_code"           => &$statusCode,
                    "o_status_message"        => &$statusMessage
                ];

                DB::executeProcedure('has.TAKE_OVER_TO_CIVIL', $params);
            }
//            }else{
 			DB::commit();
			$flashMessageContent = $this->flashMessageManager->getMessage($params);
			return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
        }
        catch (\Exception $e) {
            DB::rollback();
            return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }
    public function elecStore(Request $request)
    {

        DB::beginTransaction();
        $params =[];
        try {
            //$p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
//
            $attach_elec = $request->file('attach_elec');
            $allotattachedFileContent = base64_encode(file_get_contents($attach_elec->getRealPath()));
            if(1 == $request->get('takeOverType')){
                $attach_elec = $request->file('attach_elec');
                $allotattachedFileContent = base64_encode(file_get_contents($attach_elec->getRealPath()));
                $params = [
                    "p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                    "p_EMP_ID"                => $request->get('emp_id'),
                    "p_ALLOT_LETTER_ID"       => $request->get('allot_letter_id'),
                    "p_TAKE_OVER_DATE_ELEC"        => (($request->get("take_over_date_elec"))? date("Y-m-d", strtotime($request->get("take_over_date_elec"))):''),
                    "p_TAKE_OVER_ELEC_EMP"   => $request->get("elec_emp_id"),
                    "p_ELEC_ENG_COMMENT"     => $request->get("electrical_eng_comment"),
                    "p_HOUSE_DETAILS"         => $request->get("house_details"),
                    "p_ELECTRICAL_FITTINGS"     => $request->get("electrical_fittings"),
                    "p_AUTH_DOC_ELEC"              =>  [
                        'value' =>  $allotattachedFileContent,
//                        'type'  => \PDO::PARAM_LOB,
                        'type'  => SQLT_CLOB,
                    ],
                    "p_REMARKS"               => $request->get("remarks"),
                    "p_DORMITORY_YN"          => null,
                    "p_WATER_TAP"               =>$request->get("water_tap"),
                    "p_insert_by"             => Auth()->ID(),
                    "o_status_code"           => &$statusCode,
                    "o_status_message"        => &$statusMessage
                ];

                DB::executeProcedure('has.TAKE_OVER_TO_ELEC', $params);

            }else{
                $params = [
                    // "p_TAKE_OVER_TYPE_ID"     => $request->get('takeOverType'),
                    "p_EMP_ID"                => $request->get('emp_id'),
                    "p_ALLOT_LETTER_ID"       => $request->get('allot_letter_id'),
                    "p_TAKE_OVER_DATE_CIVIL"        => (($request->get("take_over_date_civil"))? date("Y-m-d", strtotime($request->get("take_over_date_civil"))):''),
                    "p_TAKE_OVER_CIVIL_EMP"   => $request->get("civil_emp_id"),
                    "p_CIVIL_ENG_COMMENT"     => $request->get("civil_eng_comment"),
                    "p_HOUSE_DETAILS"         => $request->get("house_details"),
                    "p_SANITARY_FITTINGS"     => $request->get("sanitary_fittings"),
                    "p_AUTH_DOC_CIVIL"              =>  [
                        'value' =>  $allotattachedFileContent,
//                        'type'  => \PDO::PARAM_LOB,
                        'type'  => SQLT_CLOB,
                    ],
                    "p_REMARKS"               => $request->get("remarks"),
                    "p_DORMITORY_YN"          => null,
                    "p_WATER_TAP"               =>$request->get("water_tap"),
                    "p_insert_by"             => Auth()->ID(),
                    "o_status_code"           => &$statusCode,
                    "o_status_message"        => &$statusMessage
                ];

                DB::executeProcedure('has.TAKE_OVER_TO_ELEC', $params);
            }

//            }else{
            DB::commit();
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
        }
        catch (\Exception $e) {
            DB::rollback();
            return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //->where('cancel_yn', 'N')
    }
    public function datatableList(){
        $user_id = auth()->id();

        $register_data = DB::table('house_allottment')
            ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date',
                'allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no',
                'allot_letter.delivery_yn','allot_letter.ack_yn',
                'pmis.employee.emp_name','pmis.employee.emp_code',
                'take_over.TAKE_OVER_DATE as take_over_date','hand_over.TAKE_OVER_DATE as hand_over_date')
            ->join('allot_letter', 'allot_letter.application_id','=','house_allottment.application_id')
            ->join('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
            ->join('take_over', 'take_over.take_over_id','=','house_allottment.take_over_id')
            ->leftJoin('take_over as hand_over', 'house_allottment.hand_over_id','=','hand_over.take_over_id')
            ->where('take_over.insert_by','=', $user_id)
//            ->where('take_over.insert_by','=', 2002260190)
            ->get();

//        dd($register_data);

        return datatables()->of($register_data)
            ->addColumn('allot_letter_date', function($register_data) {
                return Carbon::parse($register_data->allot_letter_date)->format('Y-m-d');
            })
            ->addColumn('take_over_date', function($register_data){
                return Carbon::parse($register_data->take_over_date)->format('Y-m-d');
            })
            ->addColumn('hand_over_date', function($register_data){
                return ($register_data->hand_over_date ? Carbon::parse($register_data->hand_over_date)->format('Y-m-d'):'');
            })
            ->addColumn('action', function ($query) {
                return '<a target="_blank" title="Takeover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_Take_Over_Report.xdo&p_allot_id='.$query->allot_letter_id.'&type=pdf&filename=takeover_letter"  class="m-2" ><i class="bx bx-download cursor-pointer"></i></a>&nbsp;'.(isset($register_data->hand_over_date)?'|&nbsp;<a target="_blank" title="Handover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_General_Handover_Report.xdo&p_emp_code='.$query->emp_code.'&type=pdf&filename=handover_letter"  class="m-2"><i class="bx bx-download cursor-pointer"></i></a>':'');
            })
            ->make(true);

    }

    public function datatableListElec(){
        $user_id = auth()->user()->emp_id;
//dd($user_id);
        $register_data = DB::select('SELECT EM.EMP_CODE,
       EM.EMP_NAME,
       AL.ALLOT_LETTER_NO,
       AL.ALLOT_LETTER_ID,
       AL.ALLOT_LETTER_DATE,
       HT.TAKE_OVER_DATE_ELEC as TAKE_OVER_DATE FROM HAS.TAKE_OVER         HT, PMIS.EMPLOYEE EM, HAS.ALLOT_LETTER      AL, HAS.HOUSE_ALLOTTMENT  HA

WHERE   HT.TAKE_OVER_ELEC_EMP =  '.$user_id.'
AND HT.EMP_ID =  EM.EMP_ID
AND AL.ALLOT_LETTER_ID = HT.ALLOT_LETTER_ID
AND HT.ALLOT_LETTER_ID = HA.ALLOT_LETTER_ID(+)

       ');

        return datatables()->of($register_data)
            ->addColumn('allot_letter_date', function($register_data) {
                return Carbon::parse($register_data->allot_letter_date)->format('Y-m-d');
            })
            ->addColumn('take_over_date', function($register_data){
                return Carbon::parse($register_data->take_over_date)->format('Y-m-d');
            })
//            ->addColumn('hand_over_date', function($register_data){
//                return ($register_data->hand_over_date ? Carbon::parse($register_data->hand_over_date)->format('Y-m-d'):'');
//            })
            ->addColumn('action', function ($query) {
                return '<a target="_blank" title="Takeover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_Take_Over_Report.xdo&p_allot_id='.$query->allot_letter_id.'&type=pdf&filename=takeover_letter"  class="m-2" ><i class="bx bx-download cursor-pointer"></i></a>&nbsp;'.(isset($register_data->hand_over_date)?'|&nbsp;<a target="_blank" title="Handover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_General_Handover_Report.xdo&p_emp_code='.$query->emp_code.'&type=pdf&filename=handover_letter"  class="m-2"><i class="bx bx-download cursor-pointer"></i></a>':'');
            })
            ->make(true);

    }

    public function datatableListCivil(){
        $user_id = auth()->user()->emp_id;

        $register_data = DB::select('SELECT EM.EMP_CODE,
       EM.EMP_NAME,
       AL.ALLOT_LETTER_NO,
       AL.ALLOT_LETTER_ID,
       AL.ALLOT_LETTER_DATE,
       HT.TAKE_OVER_DATE_CIVIL as TAKE_OVER_DATE FROM HAS.TAKE_OVER         HT, PMIS.EMPLOYEE EM, HAS.ALLOT_LETTER      AL, HAS.HOUSE_ALLOTTMENT  HA

WHERE   HT.TAKE_OVER_CIVIL_EMP =  '.$user_id.'
AND HT.EMP_ID =  EM.EMP_ID
AND AL.ALLOT_LETTER_ID = HT.ALLOT_LETTER_ID
AND HT.ALLOT_LETTER_ID = HA.ALLOT_LETTER_ID(+)
       ');
      return datatables()->of($register_data)
            ->addColumn('allot_letter_date', function($register_data) {
                return Carbon::parse($register_data->allot_letter_date)->format('Y-m-d');
            })
            ->addColumn('take_over_date', function($register_data){
                return Carbon::parse($register_data->take_over_date)->format('Y-m-d');
            })
//            ->addColumn('hand_over_date', function($register_data){
//                return ($register_data->hand_over_date ? Carbon::parse($register_data->hand_over_date)->format('Y-m-d'):'');
//            })
            ->addColumn('action', function ($query) {
                return '<a target="_blank" title="Takeover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_Take_Over_Report.xdo&p_allot_id='.$query->allot_letter_id.'&type=pdf&filename=takeover_letter"  class="m-2" ><i class="bx bx-download cursor-pointer"></i></a>&nbsp;'.(isset($register_data->hand_over_date)?'|&nbsp;<a target="_blank" title="Handover Letter" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_General_Handover_Report.xdo&p_emp_code='.$query->emp_code.'&type=pdf&filename=handover_letter"  class="m-2"><i class="bx bx-download cursor-pointer"></i></a>':'');
            })
            ->make(true);
    }
}
