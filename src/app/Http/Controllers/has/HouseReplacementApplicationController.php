<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 5:33 PM
 */

namespace App\Http\Controllers\has;

use App\Contracts\HreplacementApplicationContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Entities\HouseAllotment\ReplacementApplication;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;

class HouseReplacementApplicationController extends Controller
{
    use HasPermission;

    private $flashMessageManager;
    public $houseReplacementApplicationManager;
    public $employeeManager;

    public function __construct(FlashMessageManager $flashMessageManager, HreplacementApplicationContract $houseReplacementApplicationManager, EmployeeContract $employeeManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->houseReplacementApplicationManager = $houseReplacementApplicationManager;
        $this->employeeManager = $employeeManager;
    }

    public function index(Request $request)
    {
        $data = $this->houseReplacementApplication();
        $loggedUserCode = (string) auth()->user()->user_name;

        return view('housereplacementapplication.index',compact('data','loggedUserCode'));
    }

    public function datatableList(Request $request)
    {

        $houseReplacements = $this->houseReplacementApplicationManager->query();

        return datatables()->of($houseReplacements)
            /*->addColumn('action', function ($query) {
                return '<a href="' . route('house-replacement-application.edit', $query->replace_app_id) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })*/
            ->addColumn('action', function ($query) {
                if($query->workflow_process!=null){
                    $actionBtn = '<a href="' . route('house-replacement-application.edit', $query->replace_app_id) . '"><i class="bx bx-edit cursor-pointer"></i></a> ';
                    //$actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Approve"><i class="bx bx-check-circle cursor-pointer"></i></a>';
                    return $actionBtn;
                }else{
                    $actionBtn = '<a href="' . route('house-replacement-application.edit', $query->replace_app_id) . '"><i class="bx bx-edit cursor-pointer"></i></a> ';
                    //$actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal workflowBtn" style="border: 1px solid #0D6AAD" title="Workflow">Workflow</a>';
                    return $actionBtn;
                }
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        $params = $this->houseReplacementApplicationEntry($request);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    public function houseReplacementApplicationEntry(Request $request, $id='')
    {
        $houseReplacementApplication = $request->post();

        $employeeAllotment = $this->employeeManager->employeeInfoWithAllottedHouse($houseReplacementApplication['employee_code']);

        if( ($employeeAllotment['allotment_id'])) {
            try {
                $p_house_replacement_application_id = $id ? $id : '';
                $statusCode = sprintf("%4000s", "");
                $statusMessage = sprintf('%4000s', '');
                DB::beginTransaction();
                $params = [
                    "p_REPLACE_APP_ID" => [
                        "value" => &$p_house_replacement_application_id,
                        "type" => \PDO::PARAM_INPUT_OUTPUT,
                        "length" => 255
                    ],
                    'p_REPLACE_APP_DATE' => $houseReplacementApplication['application_date'] ? date("Y-m-d", strtotime($houseReplacementApplication['application_date'])) : '',
                    'p_ALLOT_ID' => $employeeAllotment['allotment_id'],
                    'p_INSERT_BY' => auth()->id(),
                    'p_REPLACE_REASON' => $houseReplacementApplication['replace_reason'],
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];

                DB::executeProcedure('allotment.replace_app_entry', $params);

                if ($params['o_status_code'] == 1)
                {
                    //Send Notification
                    $notification_msg = 'A New Application for House Replacement has been Submitted. Please Review. (Employee Code: '.Auth::user()->user_name.')';
                    $coEmpId = Employee::where('emp_code', Auth::user()->user_name)->pluck('reporting_officer_id')->first();
                    $coUserId = DB::table('cpa_security.sec_users')->where('emp_id', $coEmpId)->pluck('user_id')->first();
                    if($coUserId) {
                        $controller_user_notification = [
                            "p_notification_to" => $coUserId,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $notification_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => url('/house-replacement-approvals')
                        ];
                        DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
                    }
                }

                DB::commit();
                return $params;
            }
            catch (\Exception $e) {
                DB::rollback();
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $data = $this->houseReplacementApplication($id);
        $loggedUserCode = (string) auth()->user()->user_name;

        return view('housereplacementapplication.index',compact('data','loggedUserCode'));
    }

    public function update(Request $request, $id)
    {
        $params = $this->houseReplacementApplicationEntry($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('house-replacement-application.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function houseReplacementApplication($id=null)
    {
        $houseReplacementApplication = null;
        $employeeInformation = null;

        if($id) {
            $houseReplacementApplication = ReplacementApplication::find($id);
            if($houseReplacementApplication) {
                $employeeInformation = $this->employeeManager->employeeInfoWithAllottedHouse($houseReplacementApplication->allotment->employee->emp_code);
            }
        }

        $data = [
            'houseReplacementApplication' => $houseReplacementApplication ? $houseReplacementApplication : new ReplacementApplication(),
            'employeeInformation' => $employeeInformation
        ];

        return  $data;
    }
}
