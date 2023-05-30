<?php

namespace App\Http\Controllers\has;

use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\LBuildingInfra;
use App\Entities\HouseAllotment\LBuildingStatus;
use App\Entities\Pmis\Employee\Employee;
use App\Managers\FlashMessageManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Entities\Colony\Colony;
use App\Entities\HouseAllotment\LHouseType;
use Datatables;
use App\Traits\Security\HasPermission;

class AllotteeInformationController extends Controller
{
    use HasPermission;

    private $flashMessageManager;
    private $employeeManager;

    public function __construct(FlashMessageManager $flashMessageManager, EmployeeContract $employeeManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->employeeManager = $employeeManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
        $user = Auth::user();
        $logUser = DB::selectOne("SELECT P.EMP_ID,U.EMP_ID,P.DPT_DEPARTMENT_ID as DEPARTMENT_ID
  FROM CPA_SECURITY.SEC_USERS U, PMIS.EMPLOYEE p
  where U.EMP_ID = P.EMP_ID
  and U.EMP_ID = '$user->emp_id' ");


        if($user->user_name == 'admin'){
            $employee = Employee::all();
        }else {
            $employee = Employee::where('dpt_department_id',$logUser->department_id)->get();
        }
        */

        $colonyOptionList = $this->load_colony();
        $house_types_option = $this->load_house_types();


        $data['haApplication'] = [];//$this->populateBuilding_list();

        //return view('haallottee.index', compact('data', 'colonyOptionList','employee'));
        return view('haallottee.index', compact('data', 'colonyOptionList', 'house_types_option'));
    }

    public function load_house_types()
    {
        $house_types = LHouseType::select('house_type_id', 'house_type')->where('active_yn', '=', 'Y')->get();

        $house_typesOption = [];
        $house_types_selected_id = '';
        $house_typesOption[] = "<option value=''>Please select an option</option>";
        foreach ($house_types as $item) {
            $house_typesOption[] = "<option value='" . $item->house_type_id . "'" . ($house_types_selected_id == $item->house_type_id ? 'selected' : '') . ">" . $item->house_type . "</option>";
        }
        return $house_typesOption;
    }

    public function load_colony($colony_selected_id = null)
    {

        $user = auth()->user();

        $logUser = DB::selectOne("SELECT P.EMP_ID,U.EMP_ID,P.DPT_DEPARTMENT_ID as DEPARTMENT_ID
  FROM CPA_SECURITY.SEC_USERS U, PMIS.EMPLOYEE p
  where U.EMP_ID = P.EMP_ID
  and U.EMP_ID = '$user->emp_id' ");

        if ($user->user_name == 'admin' || $logUser->department_id == 5) {
            $colonyList = Colony::select('COLONY_ID', 'COLONY_NAME')->where('COLONY_YN', '=', 'Y')->get();
        } else {
            $colonyList = $colonyList = DB::select("SELECT  distinct h.COLONY_ID, C.COLONY_NAME
    FROM    HOUSE_LIST H left join l_colony c on (h.COLONY_ID= c.COLONY_ID)
where   h.DEPT_ACK_ID is null or h.DEPT_ACK_ID is not null
and h.DPT_DEPARTMENT_ID = '$logUser->department_id' ");
        }

        $colonyOption = [];
        $colonyOption[] = "<option value=''>Please select an option</option>";
        foreach ($colonyList as $item) {
            $colonyOption[] = "<option value='" . $item->colony_id . "'" . ($colony_selected_id == $item->colony_id ? 'selected' : '') . ">" . $item->colony_name . "</option>";
        }
        return $colonyOption;
    }

    public function store(Request $request, $id = null)
    {
        DB::beginTransaction();
        $params = [];
        $fileName = '';
        $fileType = '';
        $fileContent = '';
        $file = $request->file('special_consider_attachment');

        if ($file) {
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
            $fileContent = base64_encode(file_get_contents($file->getRealPath()));
        }
        try {
            $p_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            $params = [
                "p_ALLOT_ID" => [
                    "value" => &$p_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],
                "p_EMP_ID" => $request->get('emp_id'),
                "p_EMP_CODE" => $request->get('emp_code'),
                "p_DATE_OF_ALLOTMENT" => (($request->get("date_of_allotment")) ? date("Y-m-d", strtotime($request->get("date_of_allotment"))) : ''),
                "p_OFFICE_ORDER_NO" => $request->get('office_order_no'),
                "p_OFFICE_ORDER_DATE" => (($request->get("office_order_date")) ? date("Y-m-d", strtotime($request->get("office_order_date"))) : ''),
                "p_HOUSE_ID" => $request->get('house_id'),
                "p_DORMITORY_YN" => ($request->get('dormitory_yn') == 'Y') ? $request->get('dormitory_yn') : ($request->get('flat_dormitory_yn') == 'Y' ? $request->get('flat_dormitory_yn') : 'N'),
                "p_SPECIAL_CONSIDERATION_YN" => $request->get('special_consideration_yn'),
                "p_SPECIAL_REMARKS" => $request->get('special_consider_remarks'),
                "p_SPECIAL_CONSIDER_FILE" => $fileContent,
                "p_SPECIAL_CONSIDER_FILE_NAME" => $fileName,
                "p_SPECIAL_CONSIDER_FILE_TYPE" => $fileType,
                "p_FLAT_NAME_ID" => $request->get('flat_name_id'),
                "p_insert_by" => Auth()->ID(),
                "o_status_code" => &$statusCode,
                "o_status_message" => &$statusMessage

            ];
            DB::executeProcedure('has.house_allot_old_entry', $params);

            if ($params['o_status_code'] == 1) {
                DB::commit();
            } else {
                DB::rollback();
            }

            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);

        } catch (\Exception $e) {
            DB::rollback();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function datatableList(Request $request)
    {
//        $sql = "select ha.allot_id, hl.house_name,ha.emp_code, emp.emp_name, ld.department_name, ldes.designation,ha.emp_id, ha.application_id from house_allottment ha
//left join house_list hl on(hl.house_id = ha.house_id)
//left join pmis.employee emp on (emp.emp_id = ha.emp_id)
//left join pmis.l_department ld on (ld.department_id = emp.dpt_department_id)
//left join pmis.l_designation ldes on (ldes.designation_id = emp.designation_id)
//order by ha.insert_date desc";

        // Query modified to add 2 new column for house type and building name at 3rd January 2022 according to New CR
        $sql = "SELECT da.dept_ack_no,
         ha.allot_id,
         hl.house_name,
         ha.emp_code,
         emp.emp_name,
         ld.department_name,
         ldes.designation,
         ha.emp_id,
         ha.application_id,
         ht.house_type,
         bl.building_name,
         bl.building_road_no,
         ha.OLD_ENTRY_YN,
         ha.TAKE_OVER_ID,
         hl.DORMITORY_YN,
         hl.HOUSE_CODE,
         ha.hand_over_id
    FROM house_allottment ha
         LEFT JOIN house_list hl ON (hl.house_id = ha.house_id)
         LEFT JOIN dept_acknowledgement da ON (da.dept_ack_id = hl.dept_ack_id)
         LEFT JOIN has.l_house_type ht ON (hl.house_type_id = ht.house_type_id)
         LEFT JOIN has.building_list bl ON (hl.building_id = bl.building_id)
         LEFT JOIN pmis.employee emp ON (emp.emp_id = ha.emp_id)
         LEFT JOIN pmis.l_department ld
             ON (ld.department_id = emp.dpt_department_id)
         LEFT JOIN pmis.l_designation ldes
             ON (ldes.designation_id = emp.designation_id)
WHERE (ha.OLD_ENTRY_YN = 'N' AND ha.TAKE_OVER_ID IS NOT NULL) OR (ha.OLD_ENTRY_YN = 'Y' AND ha.TAKE_OVER_ID IS NULL)
ORDER BY ha.insert_date DESC";
//        dd($sql);
        $data = DB::select($sql);

        return datatables()->of($data)
            ->addColumn('house_name', function ($data){
                if($data->dormitory_yn == 'Y') {
                    return $data->house_name.' ('.$data->house_code.')';
                }
                return $data->house_name;
            })
            ->addColumn('action', function ($data) {
                $optionHtml = '';
                if ($data->application_id == null ) {
                    $optionHtml .= '<a href="javascript:void(0)" onclick="showData(' . $data->allot_id . ',' . $data->emp_id . ')" ><i class="bx bx-show cursor-pointer"></i></a>';
                    if (!$data->hand_over_id) {
                        $optionHtml .= '<a class="text-danger" href="' . route('allottee_informations.allottee-remove', ['allot_id' => $data->allot_id, 'emp_id' => $data->emp_id]) . '" onclick="return confirm(\'Are you sure to delete?\')"><i class="bx bx-trash cursor-pointer"></i></a>';
                    }
                } 
                return $optionHtml;
            })
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function removeAllottee(Request $request)
    {
        $status_code = sprintf("%4000s", "");
        $status_message = sprintf("%4000s", "");

        $params = [
            'P_ALLOT_ID' => $request->get('allot_id'),
            'P_EMP_ID' => $request->get('emp_id'),
            'o_status_code' => &$status_code,
            'o_status_message' => &$status_message,
        ];
        DB::executeProcedure('HAS.ALLOTTEE_DEL', $params);
        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
//        return $params['o_status_code'] . '+' . $params['o_status_message'];
    }

    public function showAllottee(Request $request)
    {
//        dd($request);
        $allot_id = $request->allot_id;
        $emp_id = $request->emp_id;

        $employee_code = Employee::where('emp_id', $emp_id)->pluck('emp_code')->first();
        $employee_info = $this->employeeManager->findEmployeeInformation($employee_code);

        $allot_query = <<<QUERY
select * from has.house_allottment where allot_id = $allot_id
QUERY;

        $allot_info = DB::selectOne($allot_query);

        $house_query = <<<QUERY
select * from has.house_list where house_id = $allot_info->house_id
QUERY;

        $house_info = DB::selectOne($house_query);

        return [
            'employeeInformation' => $employee_info,
            'allotInformation' => $allot_info,
            'houseInformation' => $house_info
        ];
    }
}


