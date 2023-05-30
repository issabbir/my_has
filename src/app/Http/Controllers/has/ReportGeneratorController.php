<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/12/20
 * Time: 11:54 AM
 */

namespace App\Http\Controllers\has;

use App\Entities\Admin\LDepartment;
use App\Entities\HouseAllotment\Acknowledgemnt;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Colony\Colony;
use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\HouseAllotment\LHouseStatus;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Security\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\Security\HasPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ReportGeneratorController extends Controller
{
    use HasPermission;

    public function index(Request $request)
    {
        $user = Auth::user();
        $logUser = DB::selectOne("SELECT P.EMP_ID,U.EMP_ID,P.DPT_DEPARTMENT_ID as DEPARTMENT_ID
  FROM CPA_SECURITY.SEC_USERS U, PMIS.EMPLOYEE p
  where U.EMP_ID = P.EMP_ID
  and U.EMP_ID = '$user->emp_id' ");

        $module = 14;
        $reportObject = new Report();

        if (auth()->user()->hasGrantAll()) {
            $reports = $reportObject->where('module_id', $module)->orderBy('report_name', 'ASC')->get();
        }

        else {
            $roles = auth()->user()->getRoles();
            $reports = array();
            foreach ($roles as $role) {
                if(count($role->reports)) {
                    $rpts = $role->reports->where('module_id', $module);
                    foreach ($rpts as $report) {
                        $reports[$report->report_id] = $report;
                    }
                }
            }
        }

        return view('reportgenerator.index', compact('reports'));
    }

    public function reportParams(Request $request, $id)
    {

        $user = Auth::user();

        $logUser = DB::selectOne("SELECT P.EMP_ID,U.EMP_ID,P.DPT_DEPARTMENT_ID AS DEPARTMENT_ID ,D.DEPARTMENT_NAME
  FROM CPA_SECURITY.SEC_USERS U, PMIS.EMPLOYEE P , PMIS.L_DEPARTMENT D
  WHERE U.EMP_ID = P.EMP_ID
  AND D.DEPARTMENT_ID = P.DPT_DEPARTMENT_ID
  AND U.EMP_ID = '$user->emp_id' ");

        $allDepartmentSeeReport = DB::selectOne("SELECT SP.*, P.PERMISSION_KEY
  FROM CPA_SECURITY.SEC_ROLE_PERMISSIONS SP,
       CPA_SECURITY.SEC_ROLE SR,
       CPA_SECURITY.SEC_USER_ROLES UR,
       CPA_SECURITY.SEC_PERMISSIONS P
 WHERE     SP.ROLE_ID = SR.ROLE_ID
       AND UR.ROLE_ID = SR.ROLE_ID
       AND P.PERMISSION_ID = SP.PERMISSION_ID
       AND P.PERMISSION_KEY = 'CAN_SEE_ALL_DEPARMENT_REPORT'
       AND UR.USER_ID = '$user->user_id'");

        $report = Report::find($id);

        $houseStatuses = LHouseStatus::all();
        $lColony = Colony::all();
        $lHouseType = LHouseType::orderBy('house_type', 'asc')->get();
        $buildingList = BuildingList::all();

        if (isset($logUser->department_id)){
            if ( $logUser->department_id == 5 ){
                $lDepartment = LDepartment::all();
            }elseif(isset($allDepartmentSeeReport) )
            {

                if ($allDepartmentSeeReport->permission_key){
                    $lDepartment = LDepartment::all();
                }


            }else{
                $lDepartment = LDepartment::where('department_id','=', $logUser->department_id)->get();
            }
        }

        $lHouseList = HouseList::all();
        $employees = [];
        $acknowledgemnt = Acknowledgemnt::all();

        $reportForm = view('reportgenerator.report-params', compact('report','houseStatuses','lColony','lHouseType','buildingList','lDepartment','lHouseList', 'employees','acknowledgemnt','logUser'))->render();

        return $reportForm;
    }
}
