<?php

namespace App\Http\Controllers\has;

use App\Contracts\HinterChangeApplicationContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Colony\Colony;
use App\Entities\Colony\ColonyType;
use App\entities\houseAllotment\AllotLetter;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Mail\InterchangeApprove;
use App\Mail\ReplacementApprove;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Managers\FlashMessageManager;
use App\Entities\HouseAllotment\InterchangeApplication;
use App\Traits\Security\HasPermission;
use Illuminate\Support\Facades\Mail;

class AllotmentLetterInterchangeController extends Controller
{
    use HasPermission;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $flashMessageManager;
    public $houseInterchangeApplicationManager;
    public $employeeManager;

    public function __construct(FlashMessageManager $flashMessageManager,HinterChangeApplicationContract $houseInterchangeApplicationManager, EmployeeContract $employeeManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->houseInterchangeApplicationManager = $houseInterchangeApplicationManager;
        $this->employeeManager = $employeeManager;
    }
    public function index()
    {
        $data =[
            'employeeList' => $this->loadEmpCivilEng(),
            'advertisements'=>$this->loadAdvertisementList(),
        ];
        return view('allotment-letter.interChangeIndex',compact('data'));
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

    public function loadAdvertisementList($advMstNumber = null){
        $advMstList = HaAdvMst::select('*')->where('ACTIVE_YN','=','Y')->get();
        //$advMstList = HouseAllotment::select('*')->where('ACTIVE_YN','=','Y')->get();
        $advMstOption = [];
        $advMstOption[] = "<option value='' selected>Please select an option</option>";
        foreach ($advMstList as $item) {
            $advMstOption[] = "<option value='".$item->adv_id."'".($advMstNumber == $item->adv_id ? 'selected':'').">".$item->adv_number."</option>";
        }
        return $advMstOption;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id =null)
    {
        $allots = InterchangeApplication::where('int_change_id', $request->int_change_id)->first();

        $emp1Array = HouseAllotment::where('allot_id', $allots->first_allot_id)->select('emp_id','OLD_ENTRY_YN')->first();
        $emp2Array = HouseAllotment::where('allot_id', $allots->second_allot_id)->select('emp_id','OLD_ENTRY_YN')->first();

        $old_entry_yn1 = $emp1Array['old_entry_yn'];
        $old_entry_yn2 = $emp2Array['old_entry_yn'];

        $emp1 = $emp1Array['emp_id'];
        $emp2 = $emp2Array['emp_id'];

        if($old_entry_yn1 !='Y'){
            $emp_code_1 = HaApplication::where('emp_id', $emp1)->pluck('emp_code')->first();
        }else{
            $emp_code_1 = Employee::where('emp_id', $emp1)->pluck('emp_code')->first();
        }
        if($old_entry_yn1 !='Y'){
            $emp_code_2 = HaApplication::where('emp_id', $emp2)->pluck('emp_code')->first();
        }else{
            $emp_code_2 = Employee::where('emp_id', $emp2)->pluck('emp_code')->first();
        }

        $employeeInfo_1 = $this->employeeManager->findEmployeeInformation($emp_code_1);
        $employeeInfo_2 = $this->employeeManager->findEmployeeInformation($emp_code_2);

        if($employeeInfo_1['emp_email'] && $employeeInfo_2['emp_email'])
        {
            $delivery = 'Y';
            $date = date("Y-m-d", strtotime(Carbon::now()));
        }
        else
        {
            $delivery = 'N';
            $date = '';
        }
       //dd($employeeInfo_1, $employeeInfo_2);
        $params =[];
        try {
            $p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $params = [
                "p_ALLOT_LETTER_ID" => [
                    "value" => &$p_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],
                "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime($request->get("allotment_letter_date"))),
                "p_ALLOT_LETTER_NO"   => $request->get("allotment_letter_no"),
//                "p_APPLICATION_ID"    => $request->get("emp_application_id"),
//                "p_HOUSE_ADV_ID"      => $request->get("emp_allotted_house_adv_id"),
                "p_DELIVERY_YN"       => ($request->get("delivery_yn")=='Y'? 'Y': $delivery),
                "p_DELIVERY_DATE"     => (($request->get("allotment_letter_delivery_date"))? date("Y-m-d", strtotime($request->get("allotment_letter_delivery_date"))):$date),
                "p_DELIVERED_BY"      => (($request->get("deliveredBy"))? $request->get("deliveredBy"):''),
                //"p_RECEIVED_BY"       => '',
                "p_MEMO_NO"           => $request->get("memo_no"),
                "p_MEMO_DATE"         => (($request->get("memo_date"))? date("Y-m-d", strtotime($request->get("memo_date"))):''),
                "p_INT_CHANGE_ID"    => $request->get("int_change_id"),
                "p_insert_by"         => Auth()->ID(),
                "o_status_code"       => &$statusCode,
                "o_status_message"    => &$statusMessage
            ];

            DB::executeProcedure('allotment.int_change_allot_letter', $params);

           /* if(($params['o_status_code'] == 1) && ($delivery == 'Y'))
            {
                //Sends Email
                Mail::to($employeeInfo_1['emp_email'])->send(new InterchangeApprove($employeeInfo_1['emp_name'], $request->get("allotment_letter_no")));
                Mail::to($employeeInfo_2['emp_email'])->send(new InterchangeApprove($employeeInfo_2['emp_name'], $request->get("allotment_letter_no")));
            }*/

            if($id){
                return $params;
            }else{
                $flashMessageContent = $this->flashMessageManager->getMessage($params);
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            }
        }
        catch (\Exception $e) {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function datatableList(){

        $houseInterchanges = $this->houseInterchangeApplicationManager->interChangeAllotmentLetterQuery();

        return datatables()->of($houseInterchanges)
            ->addColumn('allot_letter_date', function($query) {
                return Carbon::parse($query->allot_letter_date)->format('Y-m-d');
            })
            ->addColumn('delivery_yn', function($register_data) {
                if($register_data->delivery_yn == 'Y'){
                    return 'Delivered';
                }else{
                    return 'Not Delivered';
                }
            })
            ->addColumn('action', function ($query) {
//                return '<a target="_blank" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/rpt_house_allotment_letter_final.xdo&P_EMP='.$query->emp_code.'&type=pdf&filename=allotment_letter" class="btn btn-xs btn-primary mr-2"><i class="bx bx-download cursor-pointer"></i></a>'.'<a href="' . route('allotmentLetter.edit', $query->allot_letter_id) . '" class="btn btn-xs btn-primary"><i class="bx bx-edit cursor-pointer"></i></a>';
                return '<a target="_blank" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/rpt_interchange_allotment_letter.xdo&p_ALLOT_LETTER_NUM='.$query->allot_letter_no.'&type=pdf&filename=allotment_interchange_letter" title ="Download Interchange Allotment Letter"><i class="bx bx-download cursor-pointer"></i></a>&nbsp;|&nbsp;'.'<a href="' . route('allotmentLetterInterchange.edit', $query->int_change_id) . '" title ="Edit" ><i class="bx bx-edit cursor-pointer"></i></a>';
//                return '<a href="' . route('allotmentLetterInterchange.edit', $query->int_change_id) . '" title ="Edit" ><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->make(true);

    }

    public function showInterchangeApplicationInfo($id) //
    {
        $houseInterchangeApplication = null;
        $firstEmployeeInformation = null;
        $secondEmployeeInformation = null;

        if($id) {
            $houseInterchangeApplication = InterchangeApplication::find($id);
            if($houseInterchangeApplication) {
                $firstEmployeeInformation = $this->employeeManager->employeeInfoWithAllottedHouse($houseInterchangeApplication->first_allotment->employee->emp_code);
                $secondEmployeeInformation = $this->employeeManager->employeeInfoWithAllottedHouse($houseInterchangeApplication->second_allotment->employee->emp_code);
//                if($id){
//                    $allotmentLetter = AllotLetter::select('*')->where('INT_CHANGE_ID','=',$id)->get();
//                }
            }
        }

        $data = [
           // 'allotmentLetter'          => $allotmentLetter? $allotmentLetter: new AllotLetter(),
            'employeeList'             => $this->loadEmpCivilEng(),
            'interchangeInfo'          => $houseInterchangeApplication,
            'firstEmployeeInformation' => $firstEmployeeInformation,
            'secondEmployeeInformation' => $secondEmployeeInformation,
        ];
        return view('allotment-letter.interChangeIndex',compact('data'));

    }

    public function findAllotmentLetter($id){
        $allotLetter =  DB::table('allot_letter')
            ->select('allot_letter.*','pmis.employee.emp_name','pmis.employee.emp_code')
            ->leftJoin('house_allottment', 'allot_letter.application_id','=','house_allottment.application_id')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
            ->where('allot_letter.allot_letter_id','=',$id)->get();

        return $allotLetter;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if($id){
            $data = $this->houseInterchangeApproval($id);
        }
        return view('allotment-letter.interChangeIndex',compact('data'));
    }

    private function houseInterchangeApproval($id=null)
    {
        $houseInterchangeApplication = null;
        $firstEmployeeInformation = null;
        $secondEmployeeInformation = null;

        if($id) {
            $houseInterchangeApplication = InterchangeApplication::find($id);
            if($houseInterchangeApplication) {
                $firstEmployeeInformation = $this->employeeManager->employeeInfoWithAllottedHouse($houseInterchangeApplication->first_allotment->employee->emp_code);
                $secondEmployeeInformation = $this->employeeManager->employeeInfoWithAllottedHouse($houseInterchangeApplication->second_allotment->employee->emp_code);
                if($id){
//                    $allotmentLetter = AllotLetter::select('*')->where('INT_CHANGE_ID','=',$id)->get();
                    $allotmentLetter = DB::table('ALLOT_LETTER')->select('*')->where('INT_CHANGE_ID','=',$id)->get();
//                    dd($allotmentLetter);
                }
            }
        }

        $data = [
            'allotmentLetter'          => ($allotmentLetter? $allotmentLetter: new AllotLetter),
            'employeeList'             => ($allotmentLetter? $this->loadEmpCivilEng($allotmentLetter[0]->delivered_by):$this->loadEmpCivilEng()),
            'interchangeInfo'          => $houseInterchangeApplication,
            'firstEmployeeInformation' => $firstEmployeeInformation,
            'secondEmployeeInformation' => $secondEmployeeInformation,
        ];
        return $data;
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
        $params = $this->store($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('allotmentLetterInterchange.index')->with($flashMessageContent['class'], $flashMessageContent['message']);

    }

    public function datatableListToShowInterchange(Request $request)
    {
        $houseInterchanges = $this->houseInterchangeApplicationManager->approveQuery();

        return datatables()->of($houseInterchanges)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('allotmentLetterInterchange.showInterchangeApplicationInfo', $query->int_change_id) . '" title="Populate Information"><i class="bx bx-show cursor-pointer"></i></a>';
            })->make(true);
    }
}
