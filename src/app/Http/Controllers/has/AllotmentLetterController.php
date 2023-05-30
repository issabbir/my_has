<?php

namespace App\Http\Controllers\has;

use App\Entities\Admin\LGeoDivision;
use App\Entities\Colony\Colony;
use App\Entities\Colony\ColonyType;
use App\entities\houseAllotment\AllotLetter;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\HouseAllotment\HouseAllotment;
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

class AllotmentLetterController extends Controller
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
        $data =[
            'employeeList' => $this->loadEmpCivilEng(),
            'advertisements'=>$this->loadAdvertisementList(),
        ];
        return view('allotment-letter.index',compact('data'));
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



    public function AdvLoadDatatableList($id)
    {

        $subQueryResult = AllotLetter::select('allot_letter.APPLICATION_ID')->where('allot_letter.HOUSE_ADV_ID', '=', $id)->get();

        $selectedList= array();
        $i=0;

        foreach ($subQueryResult as $i=>$value){
            $selectedList[$i] = $subQueryResult[$i]->application_id;
            $i++;
        }

        //Employee::select('EMP_ID','EMP_CODE','EMP_NAME')->where('EMP_TYPE_ID','=','2')->where('EMP_ACTIVE_YN','=','Y')->get();
        $haApplications = DB::table('ha_application')->select('ha_application.application_date','pmis.employee.emp_name','pmis.employee.emp_code')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'ha_application.emp_id')
            ->join('house_allottment','house_allottment.application_id','=','ha_application.application_id')
            ->where('ha_application.advertisement_id','=',$id)
            ->whereNotIn('ha_application.application_id', $selectedList)
            ->get();

        $applicationList = '';
        $applicationList .='<div class="table-responsive">
                            <table id="applicationListTable" class="table border display" >
                                        <thead class="border">
                                            <th>Application Date</th>
                                            <th>Employee</th>
                                            <th>Employee Code</th>
                                        </thead>
                               <tbody>';
        if(count($haApplications) > 0) {
            for($counter = 0; $counter < count($haApplications); $counter++) {
                 $applicationList .= '<tr>
                                          <td><button class="btn btn-link selectApplication" type="button" value="'. (count($haApplications)>0? $haApplications[$counter]->emp_code:'') .'">' .  Carbon::parse($haApplications[$counter]->application_date)->format('Y-m-d'). '</button></td>
                                          <td>' .(count($haApplications)>0? $haApplications[$counter]->emp_name :''). '</td>
                                          <td>' .(count($haApplications)>0? $haApplications[$counter]->emp_code :''). '</td>
                                       </tr>';
            }
        }else{
            $applicationList .= '<tr>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                 </tr>';
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
                "p_APPLICATION_ID"    => $request->get("emp_application_id"),
                "p_HOUSE_ADV_ID"      => $request->get("emp_allotted_house_adv_id"),
                "p_DELIVERY_YN"       => ($request->get("delivery_yn")=='Y'? 'Y': 'N'),
                "p_DELIVERY_DATE"     => (($request->get("allotment_letter_delivery_date"))? date("Y-m-d", strtotime($request->get("allotment_letter_delivery_date"))):''),
                "p_DELIVERED_BY"      => (($request->get("deliveredBy"))? $request->get("deliveredBy"):''),
                //"p_RECEIVED_BY"       => '',
                "p_MEMO_NO"           => $request->get("memo_no"),
                "p_MEMO_DATE"         => (($request->get("memo_date"))? date("Y-m-d", strtotime($request->get("memo_date"))):''),
                //"p_REMARKS"           => $request->get('remarks'),
                "p_insert_by"         => Auth()->ID(),
                "o_status_code"         => &$statusCode,
                "o_status_message"      => &$statusMessage
            ];

            DB::executeProcedure('allotment.allot_letter_entry', $params);

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

        $register_data = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date','allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no','allot_letter.delivery_yn',
                'allot_letter.ack_yn','pmis.employee.emp_name','pmis.employee.emp_code', 'HA_ADV_MST.adv_number')
            ->join('HA_APPLICATION', 'allot_letter.APPLICATION_ID','=','HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'HA_APPLICATION.emp_id')
            ->leftJoin('HA_ADV_MST', 'allot_letter.house_adv_id', '=', 'HA_ADV_MST.adv_id')
            ->get();
        /*
        $register_data = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date','allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no','allot_letter.delivery_yn','allot_letter.ack_yn','pmis.employee.emp_name','pmis.employee.emp_code')
            ->join('house_allottment', 'allot_letter.allot_letter_id','=','house_allottment.allot_letter_id')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')->get();

        */

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
                return '<a target="_blank" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_Final_Allotment_Letter.xdo&p_EMPL_CODE='.$query->emp_code.'&type=pdf&filename=allotment_letter" title ="Download Allotment Letter" ><i class="bx bx-download cursor-pointer"></i></a>&nbsp;|&nbsp;'.'<a href="' . route('allotmentLetter.edit', $query->allot_letter_id) . '" title ="Edit" ><i class="bx bx-edit cursor-pointer"></i></a>';
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
        //change start: 20-09-2020 cause: allotment letter list cant show not delivered
        $allotLetter =  DB::table('allot_letter')
            ->select('allot_letter.*','pmis.employee.emp_name','pmis.employee.emp_code')
            ->join('HA_APPLICATION', 'allot_letter.APPLICATION_ID','=','HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'HA_APPLICATION.emp_id')
            ->where('allot_letter.allot_letter_id','=',$id)->get();

        /*
         $allotLetter =  DB::table('allot_letter')
            ->select('allot_letter.*','pmis.employee.emp_name','pmis.employee.emp_code')
            ->leftJoin('house_allottment', 'allot_letter.allot_letter_id','=','house_allottment.allot_letter_id')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
            ->where('allot_letter.allot_letter_id','=',$id)->get();
         */
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
        return view('allotment-letter.index',compact('data'));
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

        return redirect()->route('allotmentLetter.index')->with($flashMessageContent['class'], $flashMessageContent['message']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showletter()
    {
        return view('allotment-letter.showletter');
    }


    public function userWiseallotLetter(){

        $user = Auth::user();

        $register_data = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date','allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no','allot_letter.delivery_yn',
                'allot_letter.ack_yn','pmis.employee.emp_name','pmis.employee.emp_code', 'HA_ADV_MST.adv_number')
            ->join('HA_APPLICATION', 'allot_letter.APPLICATION_ID','=','HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'HA_APPLICATION.emp_id')
            ->leftJoin('HA_ADV_MST', 'allot_letter.house_adv_id', '=', 'HA_ADV_MST.adv_id')
            ->where('HA_APPLICATION.emp_code','=', $user->user_name)
            ->get();
        /*
        $register_data = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id','allot_letter.allot_letter_date','allot_letter.allot_letter_no','allot_letter.memo_date','allot_letter.memo_no','allot_letter.delivery_yn','allot_letter.ack_yn','pmis.employee.emp_name','pmis.employee.emp_code')
            ->join('house_allottment', 'allot_letter.allot_letter_id','=','house_allottment.allot_letter_id')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')->get();

        */

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
                return '<a target="_blank" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_Final_Allotment_Letter.xdo&p_EMPL_CODE='.$query->emp_code.'&type=pdf&filename=allotment_letter" title ="Download Allotment Letter" ><i class="bx bx-download cursor-pointer"></i></a>';
            })
            ->make(true);

    }


}
