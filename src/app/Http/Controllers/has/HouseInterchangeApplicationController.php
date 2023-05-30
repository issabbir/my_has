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
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;
use App\Entities\Pmis\Employee\Employee;

class HouseInterchangeApplicationController extends Controller
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
        $data = $this->houseInterchangeApplication();

        return view('houseinterchangeapplication.index',compact('data'));
    }

    public function datatableList(Request $request)
    {

        $houseInterchanges = $this->houseInterchangeApplicationManager->query();

        return datatables()->of($houseInterchanges)
            /*->addColumn('action', function ($query) {
                return '<a href="' . route('house-interchange-application.edit', $query->int_change_id) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })*/
            ->addColumn('action', function ($query) {
                if($query->workflow_process!=null){
                    $actionBtn = '<a href="' . route('house-interchange-application.edit', $query->int_change_id) . '"><i class="bx bx-edit cursor-pointer"></i></a> ';
                    //$actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Approve"><i class="bx bx-check-circle cursor-pointer"></i></a>';
                    return $actionBtn;
                }else{
                    $actionBtn = '<a href="' . route('house-interchange-application.edit', $query->int_change_id) . '"><i class="bx bx-edit cursor-pointer"></i></a> ';
                    //$actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal workflowBtn" style="border: 1px solid #0D6AAD" title="Workflow">Workflow</a>';
                    return $actionBtn;
                }
            })
            ->make(true);
    }

    public function store(Request $request)
    {

        $params = $this->houeInterchangeApplicationEntry($request);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    public function houeInterchangeApplicationEntry(Request $request, $id='')
    {

        $houseInterchangeApplication = $request->post();

//        $firstEmployeeAllotment = $this->employeeManager->employeeInfoWithAllottedHouse($houseInterchangeApplication['first_employee_code']);
//        $secondEmployeeAllotment = $this->employeeManager->employeeInfoWithAllottedHouse($houseInterchangeApplication['second_employee_code']);

        if( ($houseInterchangeApplication['first_alloted_id']) && ($houseInterchangeApplication['second_alloted_id']) ) {
            try {
                $p_house_interchange_application_id = $id ? $id : '';
                $statusCode = sprintf("%4000s", "");
                $statusMessage = sprintf('%4000s', '');
                DB::beginTransaction();
                $params = [
                    "p_INT_CHANGE_ID" => [
                        "value" => &$p_house_interchange_application_id,
                        "type" => \PDO::PARAM_INPUT_OUTPUT,
                        "length" => 255
                    ],
                    'p_INT_CHANGE_APP_DATE' => $houseInterchangeApplication['application_date'] ? date("Y-m-d", strtotime($houseInterchangeApplication['application_date'])) : '',
                    'p_FIRST_ALLOT_ID' => $houseInterchangeApplication['first_alloted_id'],
                    'p_SECOND_ALLOT_ID' => $houseInterchangeApplication['second_alloted_id'],
                    'p_REMARKS'     => $houseInterchangeApplication['remarks'],
                    'p_INSERT_BY' => auth()->id(),
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];
//dd($params);
                DB::executeProcedure('allotment.int_change_app_entry', $params);

                if ($params['o_status_code'] == 1)
                {
                    //Send Notification
                    $notification_msg = 'A New Application for House Interchange has been Submitted. Please Review. (Employee Code: '.$houseInterchangeApplication['f_emp_code'].', '.$houseInterchangeApplication['f_emp_code'].')';
                    $coEmpId = Employee::where('emp_id', $houseInterchangeApplication['first_employee_code'])->pluck('reporting_officer_id')->first();
                    $coUserId = DB::table('cpa_security.sec_users')->where('emp_id', $coEmpId)->pluck('user_id')->first();

                    if($coUserId)
                    {
                        $controller_user_notification = [
                            "p_notification_to" => $coUserId,
                            "p_insert_by" => Auth::id(),
                            "p_note" => $notification_msg,
                            "p_priority" => null,
                            "p_module_id" => 14,
                            "p_target_url" => url('/house-interchange-approvals')
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
        $data = $this->houseInterchangeApplication($id);

        return view('houseinterchangeapplication.index',compact('data'));
    }

    public function update(Request $request, $id)
    {
        $params = $this->houeInterchangeApplicationEntry($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if($params['o_status_code'] != 1){
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('house-interchange-application.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function houseInterchangeApplication($id=null)
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
        ];

        return  $data;
    }
}
