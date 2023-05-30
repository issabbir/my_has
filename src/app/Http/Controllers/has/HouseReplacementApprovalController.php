<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 5:33 PM
 */

namespace App\Http\Controllers\has;

use App\Contracts\HouseContract;
use App\Contracts\HreplacementApplicationContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\HouseAllotment\ReplacementApplication;
use App\Entities\Pmis\WorkFlowProcess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;

class HouseReplacementApprovalController extends Controller
{
    use HasPermission;

    private $flashMessageManager;
    public $houseReplacementApplicationManager;
    public $employeeManager;
    public $houseManager;

    public function __construct(FlashMessageManager $flashMessageManager, HreplacementApplicationContract $houseReplacementApplicationManager, EmployeeContract $employeeManager, HouseContract $houseManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->houseReplacementApplicationManager = $houseReplacementApplicationManager;
        $this->employeeManager = $employeeManager;
        $this->houseManager = $houseManager;
    }

    public function index(Request $request)
    {
        $data = $this->houseReplacementApproval();

        return view('housereplacementapproval.index',compact('data'));
    }

    public function datatableList(Request $request)
    {
        $houseReplacements = $this->houseReplacementApplicationManager->approveQuery();

        return datatables()->of($houseReplacements)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('house-replacement-approval.edit', $query->replace_app_id) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })->make(true);
    }

    public function store(Request $request)
    {
        $params = $this->houseReplacementApprovalEntry($request);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    public function houseReplacementApprovalEntry(Request $request, $id='')
    {
        $houseReplacementApplication = $request->post();

        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            $params = [
                'p_REPLACE_APP_ID' => $id,
                'p_REPLACE_HOUSE_ID' => $houseReplacementApplication['house_id'],
                'p_APPROVED_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];

            DB::executeProcedure('ha_replace_approve', $params);
            return $params;
        }
        catch (\Exception $e) {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $this->houseReplacementApproval($id);//dd($data);

        $showBtn = 0;
        $nodata = DB::select("select workflow_step_id from pmis.workflow_process where workflow_object_id = "."'".$id."'");
        $nodata = array_column($nodata, 'workflow_step_id');
        $nodata = array_unique($nodata);

//        if($data['houseReplacementApplication']->workflow_process!=null){
//            $compareData = DB::select("select workflow_step_id from pmis.workflow_steps where approval_workflow_id = ".$data['houseReplacementApplication']->workflow_process);
//            $compareData = array_column($compareData, 'workflow_step_id');
//            $compareData = array_unique($compareData);
//        }else{
//            $compareData = [];
//        }
//
//        if(count($nodata)==count($compareData) && count($compareData)!=0){
//            $showBtn = 1;
//        }
        $object_id = $data['prefix'].$data['houseReplacementApplication']->replace_app_id;
        $is_approved = WorkFlowProcess::where('workflow_object_id', $object_id)->where('workflow_step_id', null)->first();
        $isHod = Auth::user()->roles->where('role_key','ha_hod')->first();

        if($is_approved  && $isHod)
        {
            $showBtn = 1;
        }

        return view('housereplacementapproval.edit',compact('data','showBtn'));
    }

    public function update(Request $request, $id)
    {
        $params = $this->houseReplacementApprovalEntry($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('house-replacement-approval.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function houseReplacementApproval($id=null)
    {
        $houseReplacementApplication = null;
        $employeeInformation = null;
        $availableHouses = null;

        if($id) {
            $houseReplacementApplication = ReplacementApplication::find($id);
            if($houseReplacementApplication) {
                $employeeInformation = $this->employeeManager->employeeInfoWithAllottedHouse($houseReplacementApplication->allotment->employee->emp_code);
                $availableHouses = $this->houseManager->findAvailableHouses($employeeInformation['emp_grade_id']);
            }
        }

        $data = [
            'houseReplacementApplication' => $houseReplacementApplication ? $houseReplacementApplication : new ReplacementApplication(),
            'employeeInformation' => $employeeInformation,
            'houses' => $availableHouses,
            'prefix' => 'raa'
        ];

        return  $data;
    }

    public function unAssign(Request $request, $id)
    {
        $params = $this->unAssignHouseToApplicant($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('house-replacement-approval.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function unAssignHouseToApplicant(Request $request, $id)
    {
        $houseReplacementApplication = ReplacementApplication::find($id);

        if($houseReplacementApplication) {
            try {
                $statusCode = sprintf("%4000s", "");
                $statusMessage = sprintf('%4000s', '');
                $params = [
                    'p_REPLACE_APP_ID' => $id,
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];

                DB::executeProcedure('delete_replace_approve', $params); // PROCEDURE ISSUE: delete_replace_approve delete the whole row! It should just un-assign house and grab the old one in the row!
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
}
