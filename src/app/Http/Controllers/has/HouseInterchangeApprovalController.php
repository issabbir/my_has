<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 5:33 PM
 */

namespace App\Http\Controllers\has;

use App\Contracts\HinterChangeApplicationContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\HouseAllotment\InterchangeApplication;
use App\Entities\Pmis\WorkFlowProcess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;

class HouseInterchangeApprovalController extends Controller
{
    use HasPermission;

    private $flashMessageManager;
    public $houseInterchangeApplicationManager;
    public $employeeManager;

    public function __construct(FlashMessageManager $flashMessageManager, HinterChangeApplicationContract $houseInterchangeApplicationManager, EmployeeContract $employeeManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->houseInterchangeApplicationManager = $houseInterchangeApplicationManager;
        $this->employeeManager = $employeeManager;
    }

    public function index(Request $request)
    {
        $data = $this->houseInterchangeApproval();

        return view('houseinterchangeapproval.index',compact('data'));
//        return view('houseinterchangeapproval.edit',compact('data'));
    }

    public function datatableList(Request $request)
    {
//        dd(Auth::id());
        $houseInterchanges = $this->houseInterchangeApplicationManager->approveQuery();
//        dd($houseInterchanges);

        return datatables()->of($houseInterchanges)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('house-interchange-approval.edit', $query->int_change_id) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })->make(true);
    }

    public function store(Request $request)
    {
        $params = $this->houseInterchangeApprovalEntry($request);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    public function houseInterchangeApprovalEntry(Request $request, $id='')
    {
        $houseInterchangeApplication = $request->post();

        //TODO: approved_file
        $attachedFile2 = $attachedFileName2 = $attachedFileType2 = $attachedFileContent2 ='';
        $attachedFile2 = $request->file('approved_file');

        if(isset($attachedFile2)){
            $attachedFileName2 = $attachedFile2->getClientOriginalName();
            $attachedFileType2 = $attachedFile2->getMimeType();
            $attachedFileContent2 = base64_encode(file_get_contents($attachedFile2->getRealPath()));
        }

        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            $params = [
                "p_INT_CHANGE_ID" => $id,
                "p_APR_DOC_FILE"  => [
                    'value' => $attachedFileContent2,
                    'type' => SQLT_CLOB,
                ],
                "p_APR_DOC_NAME"  =>$attachedFileName2,
                "p_APR_DOC_TYPE"  =>$attachedFileType2,
                'p_APPROVED_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];
//            dd($params);
            DB::executeProcedure('has.inter_change_approve', $params);
//            dd($params);
            return $params;
        }
        catch (\Exception $e) {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $this->houseInterchangeApproval($id);

        $showBtn = 0;
        $nodata = DB::select("select workflow_step_id from pmis.workflow_process where workflow_object_id = "."'".$id."'");
        $nodata = array_column($nodata, 'workflow_step_id');
        $nodata = array_unique($nodata);

//        if($data['houseInterchangeApplication']->workflow_process!=null){
//            $compareData = DB::select("select workflow_step_id from pmis.workflow_steps where approval_workflow_id = ".$data['houseInterchangeApplication']->workflow_process);
//            $compareData = array_column($compareData, 'workflow_step_id');
//            $compareData = array_unique($compareData);
//        }else{
//            $compareData = [];
//        }
//
//        if(count($nodata)==count($compareData) && count($compareData)!=0){
//            $showBtn = 1;
//        }
        $object_id = $data['prefix'].$data['houseInterchangeApplication']->int_change_id;
        $is_approved = WorkFlowProcess::where('workflow_object_id', $object_id)->where('workflow_step_id', null)->first();
        $isHod = Auth::user()->roles->where('role_key','ha_hod')->first();

        if($is_approved  && $isHod)
        {
            $showBtn = 1;
        }

        return view('houseinterchangeapproval.edit',compact('data','showBtn'));
    }

    public function update(Request $request, $id)
    {
        $params = $this->houseInterchangeApprovalEntry($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('house-interchange-approval.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
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
            }
        }

        $data = [
            'houseInterchangeApplication' => $houseInterchangeApplication ? $houseInterchangeApplication : new InterchangeApplication(),
            'firstEmployeeInformation' => $firstEmployeeInformation,
            'secondEmployeeInformation' => $secondEmployeeInformation,
            'prefix' => 'iaa'
        ];

        return  $data;
    }
}
