<?php

namespace App\Http\Controllers\has;

use App\Entities\Admin\LGeoDivision;
use App\Entities\Colony\Colony;
use App\Entities\Colony\ColonyType;
use App\entities\houseAllotment\AllotLetter;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\HouseAllotment\ReplacementApplication;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Mail\HouseApprove;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Managers\FlashMessageManager;
use App\Traits\Security\HasPermission;
use Illuminate\Support\Facades\Mail;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Mail\ReplacementApprove;

class ReplaceAllotmentLetterController extends Controller
{
    use HasPermission;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $flashMessageManager;
    private $employeeManager;

    public function __construct(FlashMessageManager $flashMessageManager, EmployeeContract $employeeManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->employeeManager = $employeeManager;
    }
    public function index()
    {
        $data =[
            'employeeList' => $this->loadEmpCivilEng(),
            'advertisements'=>$this->loadAdvertisementList(),
        ];
        return view('allotment-letter.replaceIndex',compact('data'));
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

    public function AdvLoadDatatableList()
    {
        //Employee::select('EMP_ID','EMP_CODE','EMP_NAME')->where('EMP_TYPE_ID','=','2')->where('EMP_ACTIVE_YN','=','Y')->get();
        $haApplications = ReplacementApplication::select('replacement_application.REPLACE_APP_ID','replacement_application.REPLACE_APP_DATE','replacement_application.REPLACE_HOUSE_ID','replacement_application.ALLOT_ID','pmis.employee.emp_name','pmis.employee.emp_code')
            ->leftJoin('house_allottment','house_allottment.allot_id','=', 'replacement_application.allot_id')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
            ->where('replacement_application.active_yn','Y')
            ->get();

        $applicationList ='<div class="table-responsive">
                            <table id="applicationListTable" class="table border display" >
                                        <thead class="border">
                                            <th>Replace Application Date</th>
                                            <th>Employee</th>
                                            <th>Employee Code</th>
                                        </thead>
                                        <tbody>';

        for($counter = 0; $counter < count($haApplications); $counter++) {
            if (count($haApplications) > 0) {
                 $applicationList .= '<tr>
                                          <td><button class="btn btn-link selectApplication" type="button" value="'. $haApplications[$counter]->emp_code .'-##-'.$haApplications[$counter]->replace_app_id.'">' .  Carbon::parse($haApplications[$counter]->application_date)->format('Y-m-d'). '</button></td>
                                          <td>' . $haApplications[$counter]->emp_name . '</td>
                                          <td>' . $haApplications[$counter]->emp_code . '</td>
                                       </tr>';
            } 
        }
        $applicationList .= '</tbody>
                             </table>
                            </div>';

        return $applicationList;

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
    public function store(Request $request,$id =null)
    {


        if($request->emp_application_id)
        {
            $emp_code = HaApplication::where('application_id', $request->emp_application_id)->pluck('emp_code')->first();
        }
        else
        {
            $allot_id = ReplacementApplication::where('replace_app_id', $request->replace_app_id)->pluck('allot_id')->first();
            $emp_id = HouseAllotment::where('allot_id', $allot_id)->pluck('emp_id')->first();
            $emp_code = HaApplication::where('emp_id', $emp_id)->pluck('emp_code')->first();
        }
        $employeeInfo = $this->employeeManager->findEmployeeInformation($emp_code);

        if (isset($employeeInfo['emp_email']) && $employeeInfo['emp_email'])
        {
            $delivery = 'Y';
            $date = date("Y-m-d", strtotime(Carbon::now()));
//            $request->get("delivery_yn")=='Y'? 'Y': 'N'
        }
        else{
            $delivery = 'N';
            $date = '';
        }

        $params =[];
        try {
            $p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            DB::beginTransaction();
            $params = [
                "p_ALLOT_LETTER_ID" => [
                    "value" => &$p_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],
                "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime($request->get("allotment_letter_date"))),
                "p_ALLOT_LETTER_NO"   => $request->get("allotment_letter_no"),
                "p_DELIVERY_YN"       => $request->get("delivery_yn")=='Y'? 'Y': $delivery,
                "p_DELIVERY_DATE"     => (($request->get("allotment_letter_delivery_date"))? date("Y-m-d", strtotime($request->get("allotment_letter_delivery_date"))):$date),
                "p_DELIVERED_BY"      => (($request->get("deliveredBy"))? $request->get("deliveredBy"):''),
                //"p_RECEIVED_BY"       => '',
                "p_MEMO_NO"           => $request->get("memo_no"),
                "p_MEMO_DATE"         => (($request->get("memo_date"))? date("Y-m-d", strtotime($request->get("memo_date"))):''),
                //"p_REMARKS"           => $request->get('remarks'),
                'p_REPLACE_APP_ID'    => $request->get("replace_app_id"),
                "p_insert_by"         => Auth()->ID(),
                "o_status_code"         => &$statusCode,
                "o_status_message"      => &$statusMessage
            ];


            DB::executeProcedure('allotment.replace_allot_letter', $params);

//            if(($params['o_status_code'] == 1) && ($delivery == 'Y'))
//            {
//                //Sends Email
//                Mail::to($employeeInfo['emp_email'])->send(new ReplacementApprove($employeeInfo['emp_name'], $employeeInfo['emp_code']));
//            }

            DB::commit();
            if($id){
                return $params;
            }else{
                $flashMessageContent = $this->flashMessageManager->getMessage($params);
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            }
        }

        catch (\Exception $e) {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            DB::rollback();
            //return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function datatableList(){
        $register_data = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date','allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no','allot_letter.delivery_yn','allot_letter.ack_yn','pmis.employee.emp_name','pmis.employee.emp_code')
            ->leftJoin('replacement_application','replacement_application.replace_app_id','=','allot_letter.replace_app_id')
            ->join('house_allottment', 'replacement_application.ALLOT_ID','=','house_allottment.ALLOT_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
            ->where('allot_letter.app_type_id','=','3')
            ->get();

        return datatables()->of($register_data)
            ->addColumn('allot_letter_date', function($register_data) {
                return Carbon::parse($register_data->allot_letter_date)->format('Y-m-d');
            })
            ->addColumn('delivery_yn', function($register_data) {
                if($register_data->delivery_yn == 'Y'){
                    return 'Delivered';
                }else{
                    return 'Not Delivered';
                }
            })
            ->addColumn('action', function ($query) {
                return '<a target="_blank" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_REPLACEMENT_ALLOTMENT_LETTER.xdo&p_EMPL_CODE='.$query->emp_code.'&type=pdf&filename=replacement_allotment_letter" title ="Download Allotment Letter" ><i class="bx bx-download cursor-pointer"></i></a>&nbsp;|&nbsp;'.'<a href="' . route('replaceAllotmentLetter.edit', $query->allot_letter_id) . '" title ="Edit" ><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->make(true);

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

    public function findAllotmentLetter($id){
        $allotLetter =  DB::table('allot_letter')
            ->select('allot_letter.*','pmis.employee.emp_name','pmis.employee.emp_code')
            ->leftJoin('replacement_application','replacement_application.replace_app_id','=','allot_letter.replace_app_id')
            ->join('house_allottment', 'replacement_application.ALLOT_ID','=','house_allottment.ALLOT_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
            ->where('allot_letter.app_type_id','=',3)
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
            $insertedData = $this->findAllotmentLetter($id);
        }
        $data =[
            'employeeList' => $this->loadEmpCivilEng($insertedData[0]->delivered_by),
            'allotmentLetter'=>$insertedData,
            'advertisements'=>$this->loadAdvertisementList(),
        ];
        return view('allotment-letter.replaceIndex',compact('data'));
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

        return redirect()->route('replaceAllotmentLetter.index')->with($flashMessageContent['class'], $flashMessageContent['message']);

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
