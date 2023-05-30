<?php

namespace App\Http\Controllers\has\colony;

use App\Entities\Colony\Colony;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Admin\LGeoThana;
use App\Entities\Colony\ColonyType;
use Datatables;
use App\Traits\Security\HasPermission;

class userColonyController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = DB::select("SELECT SR.ROLE_ID, SR.ROLE_NAME
  FROM CPA_SECURITY.SEC_ROLE_MENUS RM, CPA_SECURITY.SEC_ROLE SR
 WHERE SR.ROLE_ID = RM.ROLE_ID AND RM.MENU_ID = 14");

        $approve_role = DB::select("SELECT DISTINCT ROLE_ID, ROLE_NAME FROM HAS.USER_WISE_COLONY ");

//        $colony = DB::select(" SELECT C.COLONY_ID, C.COLONY_NAME
//    FROM HAS.L_COLONY C
//   WHERE NOT EXISTS
//            (SELECT U.COLONY_ID
//               FROM HAS.USER_WISE_COLONY U
//              WHERE U.COLONY_ID = C.COLONY_ID)
//ORDER BY C.COLONY_ID DESC");
        $colony = Colony::all();

        return view('colony.userArea', compact('role', 'colony','approve_role'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function getRoletoUser($id, $userSelected = null)
    {

        $roleUser = DB::select("SELECT E.EMP_ID, E.EMP_NAME,E.EMP_CODE, D.DEPARTMENT_NAME
  FROM CPA_SECURITY.SEC_USER_ROLES UR,
       CPA_SECURITY.SEC_USERS SR,
       PMIS.EMPLOYEE E, PMIS.L_DEPARTMENT D
 WHERE UR.USER_ID = SR.USER_ID
 AND SR.EMP_ID = E.EMP_ID
 AND E.DPT_DEPARTMENT_ID = D.DEPARTMENT_ID
 AND E.DPT_DEPARTMENT_ID IN(4,5)
 AND E.EMP_STATUS_ID = 1
 AND UR.ROLE_ID = $id");


        $roleUserOption = [];
        $roleUserOption[] = "<option value=''>Please select an option</option>";
        foreach ($roleUser as $item) {
            $roleUserOption[] = "<option value='" . $item->emp_id . "'" . ($userSelected == $item->emp_id ? 'selected' : '') . ">".$item->emp_code.' -' . $item->emp_name .' -'.'('.$item->department_name.')'
                . "</option>";
        }

        return $roleUserOption;
    }
    public function getRoletoApproveUser($id, $userSelected = null)
    {

        $roleUser = DB::select("SELECT DISTINCT U.EMP_ID, U.EMP_NAME, ROLE_NAME, E.EMP_CODE, D.DEPARTMENT_NAME
  FROM HAS.USER_WISE_COLONY U, PMIS.EMPLOYEE E, PMIS.L_DEPARTMENT D
 WHERE U.ROLE_ID = $id
 AND E.EMP_ID = U.EMP_ID
 AND D.DEPARTMENT_ID = E.DPT_DEPARTMENT_ID ");


        $roleUserOption = [];
        $roleUserOption[] = "<option value=''>Please select an option</option>";
        foreach ($roleUser as $item) {
            $roleUserOption[] = "<option value='" . $item->emp_id . "'" . ($userSelected == $item->emp_id ? 'selected' : '') . ">" .$item->emp_code.' -' . $item->emp_name .' -('.$item->department_name.')'. "</option>";
        }

        return $roleUserOption;
    }


    public function store(Request $request, $id = null)
    {
//    dd($request);
        $params = [];
        DB::beginTransaction();
        try {
            if($request->get('tab_colony_id')){
                foreach ($request->get('tab_colony_id') as $indx => $value){

                    $p_colony_id = $id ? $id : '';
                    $statusCode = sprintf("%4000s", "");
                    $statusMessage = sprintf('%4000s', '');

                    $params = [
                        "p_USER_ID" => [
                            "value" => &$p_colony_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],

                        "P_ROLE_ID" => $request->get("role_id"),
                        "P_EMP_ID" => $request->get("emp_id"),
                        "P_COLONY_ID" => $request->get("tab_colony_id")[$indx],
                        "P_REMARKS" => $request->get("remarks")[$indx],
                        "P_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];

                    DB::executeProcedure('USER_WISE_COLONY_ENTRY', $params);

                }

            }
//            dd($params);
            DB::commit();
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);

        } catch (\Exception $e) {
            DB::rollBack();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }

    }

    public function getRoletoUserSearch($role, $employee){
       $data = DB::select("SELECT ROWNUM AS SL, U.USER_WISE_COLONY_ID, U.COLONY_NAME, U.REMARKS
  FROM HAS.USER_WISE_COLONY u
 WHERE U.ROLE_ID = $role AND U.EMP_ID = $employee");

       return $data;


    }

    public function edit($id){

        $data = DB::table('user_wise_colony as u')
            ->leftJoin('pmis.employee e', 'e.emp_id','=', 'u.emp_id')
            ->leftJoin('pmis.l_department d', 'd.department_id' ,'=', 'e.dpt_department_id')
        ->where('u.user_wise_colony_id' ,'=', $id)
        ->select('u.*','e.emp_code','d.department_name')->first();

        $role = DB::select("SELECT SR.ROLE_ID, SR.ROLE_NAME
  FROM CPA_SECURITY.SEC_ROLE_MENUS RM, CPA_SECURITY.SEC_ROLE SR
 WHERE SR.ROLE_ID = RM.ROLE_ID AND RM.MENU_ID = 14");

        $approve_role = DB::select("SELECT DISTINCT ROLE_ID, ROLE_NAME FROM HAS.USER_WISE_COLONY ");

        $colony = Colony::all();

        $roleUser = DB::select("SELECT E.EMP_ID, E.EMP_NAME,E.EMP_CODE, D.DEPARTMENT_NAME,UR.ROLE_ID
  FROM CPA_SECURITY.SEC_USER_ROLES UR,
       CPA_SECURITY.SEC_USERS SR,
       PMIS.EMPLOYEE E, PMIS.L_DEPARTMENT D
 WHERE UR.USER_ID = SR.USER_ID
 AND SR.EMP_ID = E.EMP_ID
 AND E.DPT_DEPARTMENT_ID = D.DEPARTMENT_ID
 AND E.DPT_DEPARTMENT_ID IN(4,5)
 AND E.EMP_STATUS_ID = 1
 AND UR.ROLE_ID = $data->role_id");

        return view('colony.userArea', compact('role', 'colony','approve_role','data','roleUser'));
    }

    public function update(Request $request, $id){

        $params = [];
        DB::beginTransaction();
        try {
                    $p_colony_id = $id ? $id : '';
                    $statusCode = sprintf("%4000s", "");
                    $statusMessage = sprintf('%4000s', '');

                    $params = [
                        "p_USER_ID" => [
                            "value" => &$p_colony_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                        "P_ROLE_ID" => $request->get("role_id"),
                        "P_EMP_ID" => $request->get("emp_id"),
                        "P_COLONY_ID" => $request->get("colonyId"),
                        "P_REMARKS" => $request->get("remarks"),
                        "P_insert_by" => Auth()->ID(),
                        "o_status_code" => &$statusCode,
                        "o_status_message" => &$statusMessage
                    ];


                    DB::executeProcedure('USER_WISE_COLONY_ENTRY', $params);

           DB::commit();
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->route('user-wise-area.index')->with($flashMessageContent['class'], $flashMessageContent['message']);



        } catch (\Exception $e) {
            DB::rollBack();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }

    }

}
