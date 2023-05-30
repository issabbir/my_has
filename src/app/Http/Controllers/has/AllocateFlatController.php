<?php

namespace App\Http\Controllers\has;

use App\Entities\Colony\Colony;
use App\Entities\HouseAllotment\AllottmentMST;
use App\Entities\HouseAllotment\AllottmenttDtl;
use App\Entities\HouseAllotment\BuildingList;

use App\Entities\HouseAllotment\Acknowledgemnt;
use App\Entities\HouseAllotment\DeptAcknowledgement;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Admin\LDepartment;
use App\Entities\HouseAllotment\TempHouseAlloted;
use App\Entities\Pmis\WorkFlowProcess;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use App\Managers\AdvertisementManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\Security\HasPermission;
use App\Helpers\HelperClass;

class AllocateFlatController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager, AdvertisementManager $advertisementManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->advertisementManager = $advertisementManager;
    }

    public function index()
    {
        $houselist = null;
        $allotMaster = AllottmenttDtl::get();
        $ackInfo = $this->advertisementManager->getAckdata();
        $department = LDepartment::get();
        $colony = Colony::get();
//        $houseType = $this->advertisementManager->getHouseType();
        $acknowedgment = Acknowledgemnt::get();

        return view('allocateFlat.index', compact('houselist','allotMaster', 'department', 'ackInfo','colony', 'acknowedgment'));
    }


    public function depData(Request $request, $dept_ack_id)
    {
        $ackInfo = Acknowledgemnt::where('dept_ack_id', '=', $dept_ack_id)->first();
        $tempData = TempHouseAlloted::where('ack_id', $dept_ack_id)->get();
        return [
            'ackInfo' => $ackInfo,
            'tempData' => $tempData
            ];
    }

    public function ajaxBulidingData(Request $request, $house_type_id,$colonyId)
    {

        $buildingList = $this->advertisementManager->getBuildingData($house_type_id,$colonyId);

        $disdata = '';
        if (!empty($buildingList)) {
            $disdata .= '<option value="">--- Choose ---</option>';
            foreach ($buildingList as $data) {
                $disdata .= '<option value="' . $data->building_id . '">' . $data->building_name . '</option>';
            }
            echo $disdata;
            die;
        } else {
            echo '<option value="">--- Choose ---</option>';
        }
    }

    public function ajaxHouseTypeData(Request $request, $colonyId)
    {

        $typeList = $this->advertisementManager->getHouseTypeData($colonyId);

        $disdata = '';
        if (!empty($typeList)) {
            $disdata .= '<option value="">--- Choose ---</option>';
            foreach ($typeList as $data) {
                $disdata .= '<option value="' . $data->house_type_id . '">' . $data->house_type . '</option>';
            }
            echo $disdata;
            die;
        } else {
            echo '<option value="">--- Choose ---</option>';
        }
    }

    public function ajaxHouseData(Request $request, $building_id, $house_type_id,$colonyId)
    {

        $dor_yn = 'N';
        if(isset($request->ack_id))
        {
            $dor_yn = Acknowledgemnt::where('dept_ack_id', $request->ack_id)->pluck('dormitory_yn')->first();
        }

        $houseList = $this->advertisementManager->getHouseData($building_id, $house_type_id, $dor_yn,$colonyId);
//dd($houseList);
        $disdata = '';

        if (!empty($houseList)) {
            $disdata .= '<option value="">--- Choose ---</option>';
            foreach ($houseList as $data) {
                if($dor_yn == 'Y')
                {
                    $disdata .= '<option value="' . $data->house_id . '">' . $data->house_name . ' (' . $data->house_code . ')</option>';
                }
                else
                {
                    $disdata .= '<option value="' . $data->house_id . '">' . $data->house_name . '</option>';
                }
            }
            echo $disdata;

        } else {
            echo '<option value="">--- Choose ---</option>';
        }
    }

    public function buildingRoad(Request $request, $buildingId ){

       $road = DB::select("SELECT B.BUILDING_ROAD_NO
  FROM  BUILDING_LIST B
 WHERE  B.BUILDING_ID = '$buildingId'");

       return $road;
    }


    public function store(Request $request, $id = null)
    {
        $params = [];
        DB::beginTransaction();
        try {
            if ($request->get('ack_id') && $request->get('house_id')) {
                foreach ($request->get('ack_id') as $indx => $value) {
                    $statusCode = sprintf("%4000s", "");
                    $statusMessage = sprintf('%4000s', '');
                    $params = [

                        "p_DEPT_ACK_ID" => $request->get("ack_id")[$indx],
                        "p_HOUSE_ID" => $request->get("house_id")[$indx],
                        "p_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure("HAS.DEPT_HOUSE_ALLOT_PROCESS", $params);

                    if ($params['o_status_code'] != 1) {
                        DB::rollBack();
                        return $params;
                    }
                    else
                    {
                        //delete from temp
                        DB::table('tmp_house_alloted')
                            ->where('ack_id', $request->get("ack_id")[$indx])
                            ->where('house_id', $request->get("house_id")[$indx])
                            ->delete();
                    }
                }
            }

            if ($id) {
				DB::commit();
                return ["exception" => false, "o_status_code" => true, "o_status_message" => 'Update Successful'];
            } else {
				DB::commit();
                $flashMessageContent = $this->flashMessageManager->getMessage($params);
                return redirect()->route('allocate-flat.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
                //return $params;
            }

        } catch (\Exception $e) {
			DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function dataTableList()
    {

        $queryResult = DB::select('SELECT ROWNUM AS SL, a.*
  FROM (  SELECT DISTINCT
                 COUNT (hl.HOUSE_ID) OVER (PARTITION BY hl.DEPT_ACK_ID)
                    AS tot_house,
                 hl.DEPT_ACK_ID,
                 hl.DPT_DEPARTMENT_ID,
                 da.DEPARTMENT_NAME,
                 da.DEPT_ACK_NO,
                 --da.dept_ack_id,
                 da.approved_yn,
                 da.workflow_process,
                 \'acknowledge\' as prefix
            FROM HOUSE_LIST hl, DEPT_ACKNOWLEDGEMENT da
           WHERE     hl.DEPT_ACK_ID = da.DEPT_ACK_ID
                 AND hl.DPT_DEPARTMENT_ID = da.DPT_DEPARTMENT_ID
                 AND hl.DEPT_ACK_ID IS NOT NULL
                 AND DA.OLD_ACK_YN = \'N\'
                 AND DA.TRANSFERRED_YN = \'N\'
        ORDER BY hl.DEPT_ACK_ID DESC) a');
//        dd($queryResult[2]);

//        $workflow = HelperClass::workflowStatus($queryResult[2]->workflow_process,$queryResult[2]->prefix.$queryResult[2]->dept_ack_id);
//
//        $user_roles = Auth::user()->roles->pluck('role_key')->toArray();
//        $in = in_array($workflow['current_step']->user_role, $user_roles);
//        dd($in);

        return datatables()->of($queryResult)
           /* ->addColumn('action', function ($query) {
                return '<a href="' . route('allocate-flat.edit', $query->dept_ack_id) . '" ><i class="bx bx-show cursor-pointer"></i></a>';
            })*/
            ///////////////Start Workflow///////////////
            ->addColumn('action', function ($query){
                if ($query->workflow_process != null) {
                    $canUpdate = false;

                    $roles = Auth::user()->roles->pluck('role_key')->toArray();
                    $hasRole = in_array('Ha_Operator_civil', $roles);
                    if($hasRole) { //if user is 'Ha_Operator_civil'
                        $ackData = DeptAcknowledgement::where('dept_ack_id', $query->dept_ack_id)->first();
                        if ($ackData->ack_status_id == 2) { //if ack not advertised
                            if ($ackData->workflow_process_id) {
                                $user_role = DB::table('pmis.workflow_steps ws')
                                    ->select('ws.user_role')
                                    ->leftJoin('pmis.workflow_process wp', 'wp.workflow_step_id', 'ws.workflow_step_id')
                                    ->where('wp.workflow_process_id', $ackData->workflow_process_id)
                                    ->first();

                                if (isset($user_role->user_role) && $user_role->user_role == 'Ha_Operator_civil'){ //if workflow is at step 'Ha_Operator_civil'
                                    $canUpdate = true;
                                }
                            }
                        }
                    }
                    if($canUpdate) {
                        $actionBtn = '<a href="' . route('allocate-flat.edit', $query->dept_ack_id) . '" title="Edit" ><i class="bx bx-edit cursor-pointer"></i></a>';
                    }else{
                        $actionBtn = '<a href="' . route('allocate-flat.edit', $query->dept_ack_id) . '" title="View"><i class="bx bx-show cursor-pointer"></i></a>';
                    }
                        if ($query->approved_yn != 'D') {
                            $hasWorkflowPermission = HelperClass::hasPermission($query->workflow_process,$query->prefix.$query->dept_ack_id);

                            if($hasWorkflowPermission) {
                                $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Needs To Approve"><i class="bx bxs-right-arrow-circle"></i></a>';
                            } else {
                                $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal approveBtn" title="Approved"><i class="bx bx-check-circle cursor-pointer"></i></a>';
                            }
                        }

                    return $actionBtn;
                } else {
                    $actionBtn = '<a href="' . route('allocate-flat.edit', $query->dept_ack_id) . '" ><i class="bx bx-show cursor-pointer"></i></a>';
                        if ($query->approved_yn != 'D') {
                            $actionBtn .= '<a href="javascript:void(0)" class="show-receive-modal workflowBtn" title="Assign Workflow"><i class="bx bx-sitemap cursor-pointer"></i></a>';
                        }

                    return $actionBtn;
                }
            })
            ///////////////End Workflow////////////////

//            ->addIndexColumn()

            ->addColumn('report', function ($query) {
                return '<a target="_blank" href="'.request()->root().'/report/render?xdo=/~weblogic/HAS/RPT_HOUSE_ALLOTED_REPORT.xdo&p_dept_ack_id='.$query->dept_ack_id.'&type=pdf&filename=allotment_letter" title ="House Alloted Report" ><i class="bx bx-download cursor-pointer"></i></a>';
            })
            ->escapeColumns([])
            ->make(true);
    }

    public function edit(Request $request, $id)
    {
        $houselist =
            DB::select("SELECT rownum sl, hl.HOUSE_ID,C.COLONY_NAME, C.COLONY_ID ,BL.BUILDING_ROAD_NO,
       HL.BUILDING_ID,
       HL.DORMITORY_YN,
       HL.DORMITORY_ROOM_NO,
       HL.HOUSE_CODE,
       BL.BUILDING_NAME,
       HL.HOUSE_NAME,
       hl.HOUSE_TYPE_ID,
       HT.HOUSE_TYPE,
       hl.DEPT_ACK_ID,
       DP.DEPT_ACK_ID,
       DP.DEPT_ACK_NO,
       hl.DPT_DEPARTMENT_ID,
       DP.DPT_DEPARTMENT_ID,
       DP.DEPARTMENT_NAME,
       DP.NO_OF_ALLOTED_FLAT
  FROM HOUSE_LIST hl,
       DEPT_ACKNOWLEDGEMENT dp,
       l_house_type ht,
       building_list bl, L_colony c
 WHERE     HL.DPT_DEPARTMENT_ID = DP.DPT_DEPARTMENT_ID
       AND HL.DEPT_ACK_ID = DP.DEPT_ACK_ID
       AND HL.HOUSE_TYPE_ID = HT.HOUSE_TYPE_ID
       AND HL.BUILDING_ID = BL.BUILDING_ID
       AND HL.COLONY_ID = C.COLONY_ID
       AND HL.DEPT_ACK_ID = $id");
//dd($houselist);
        $ackInfo = $this->advertisementManager->getAckdata();
        $department = LDepartment::get();
        $colony = Colony::get();

        $canUpdate = false;

        $roles = Auth::user()->roles->pluck('role_key')->toArray();
        $hasRole = in_array('Ha_Operator_civil', $roles);
        if($hasRole) { //if user is 'Ha_Operator_civil'
            $ackData = DeptAcknowledgement::where('dept_ack_id', $id)->first();
            if ($ackData->ack_status_id == 2) { //if ack not advertised
                if ($ackData->workflow_process_id) {
                    $user_role = DB::table('pmis.workflow_steps ws')
                        ->select('ws.user_role')
                        ->leftJoin('pmis.workflow_process wp', 'wp.workflow_step_id', 'ws.workflow_step_id')
                        ->where('wp.workflow_process_id', $ackData->workflow_process_id)
                        ->first();

                    if (isset($user_role->user_role) && $user_role->user_role == 'Ha_Operator_civil'){ //if workflow is at step 'Ha_Operator_civil'
                        $canUpdate = true;
                    }
                }
//                else //if workflow is at step 'Ha_Operator_civil'
//                {
//                    $canUpdate = true;
//                }
            }
        }
//dd($ackData->workflow_process_id);
        return view('allocateFlat.index', compact("houselist", 'department', 'ackInfo', 'colony', 'canUpdate'));
    }

    public function update(Request $request, $id)
    {
//        dd($request);
        $params = [];
        DB::beginTransaction();
        try {
            if($request->get('house_id')) {
                if ($request->get('ack_id') && $request->get('house_id')) {
                    $ack_id_array   = $request->get('ack_id');
                    $house_id_array = $request->get("house_id");
                    $update = DB::table('has.house_list')
                        ->whereIn('dept_ack_id',$ack_id_array)
                        ->update(['dept_ack_id' => null, 'dpt_department_id' => null]);

//                        ->get();

                    if($update > 0) {
                         foreach ($ack_id_array as $indx => $value) {

                            $statusCode = sprintf("%4000s", "");
                            $statusMessage = sprintf('%4000s', '');
                            $params = [

                                "p_DEPT_ACK_ID" => $value, 		//$request->get("ack_id")[$indx],
                                "p_HOUSE_ID" => $house_id_array[$indx],	//$request->get("house_id")[$indx],
                                "p_insert_by" => Auth()->ID(),
                                "o_status_code" => &$statusCode,
                                "o_status_message" => &$statusMessage
                            ];

                            DB::executeProcedure("HAS.DEPT_HOUSE_ALLOT_PROCESS_UPD", $params);

                            if ($params['o_status_code'] != 1) {
                                DB::rollBack();
                                return $params;
                            }
                        }

                    }
                    else
                    {
                        return redirect()->back()->with('error', 'Delete Failed!');
                    }
                }

                DB::commit();
                if ($id != null) {
                    $flashMessageContent = $this->flashMessageManager->getMessage($params);
                    return redirect()->route('allocate-flat.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
                    //return $params;
                }
            }
            else
            {
                return redirect()->back()->with('error', 'Please, Give Houses!');
            }

        } catch (\Exception $e) {
			DB::rollBack();
            return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }


    public function addToTemp(Request $request)
    {
        $params =[];
        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $params = [
//                'P_TYPE' => $request->ack_id,
                'P_ACK_ID' => $request->ack_id,
                'P_ACK_NO' => $request->ack_no,
                'P_DEP_ID' => $request->dep_id,
                'P_HOUSE_TYPE_ID' => $request->house_type_id,
                'P_HOUSE_TYPE' => $request->house_type,
                'P_BUILDING_ID' => $request->building_id,
                'P_BUILDING_NAME' => $request->building_name,
                'P_COLONY_ID' => $request->colony_id,
                'P_COLONY_NAME' => $request->colony_name,
                'P_HOUSE_ID' => $request->house_id,
                'P_HOUSE' => $request->house,
                'P_ROAD_NO' => $request->road_no,
                'P_USER_ID' => Auth()->ID(),
                'o_STATUS_CODE' => &$statusCode,
                'o_STATUS_MESSAGE' => &$statusMessage
            ];

            DB::executeProcedure('has.TMP_HOUSE_ALLOTED_IN_DEL', $params);

            if($params['o_STATUS_CODE'] == 1){
                return $params;
            }else{
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $params['o_STATUS_MESSAGE']];
            }
        }
        catch (\Exception $e) {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
        }
    }

    public function deleteFromTemp(Request $request)
    {
        $data = TempHouseAlloted::where('ack_id', $request->ack_id)->where('house_id', $request->house_id)->delete();
    }
}
