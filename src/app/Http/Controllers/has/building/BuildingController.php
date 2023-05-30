<?php

namespace App\Http\Controllers\has\building;

use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\LBuildingInfra;
use App\Entities\HouseAllotment\LBuildingStatus;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Entities\Colony\Colony;
use App\Entities\HouseAllotment\LHouseType;
use Datatables;
use App\Traits\Security\HasPermission;
use function foo\func;

class BuildingController extends Controller
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
        $data                 = $this->populateBuilding_list();
        $colonyOptionList     = $this->load_colony();
        $houseTypeOptionList  = $this->load_house_type();
        $buildingInfraList    = $this->load_building_infrastructure();
        $empManagerList       = $this->load_emp_manager();
        $buildingStatusOptionList = $this->load_building_status();
        return view('building.index',compact('colonyOptionList','houseTypeOptionList','buildingInfraList','empManagerList','buildingStatusOptionList','data'));
    }

    public function loadBuilding(Request $request, $id)
    {
        $building = BuildingList::find($id);
        return $building;
    }

    public function populateBuilding_list($id=null)
    {
        $single_building_data = null;

        if($id){
            $single_building_data = BuildingList::find($id);
        }

        $buildings = BuildingList::all();
        $data = [
            'table_info'  =>  datatables()->of($buildings)->make(true),
            'building' => $single_building_data ? $single_building_data : new  BuildingList(),
            'status'     => (count($buildings)> 0? true: false),
            'message'    => (count($buildings)> 0? '': 'No Data Found')
        ];
        return  $data;
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

    public function load_colony($colony_selected_id = null)
    {
        $colonyList = Colony::select('COLONY_ID','COLONY_NAME')->where('COLONY_YN','=','Y')->get();
        $colonyOption = [];
        $colonyOption[] = "<option value=''>Please select an option</option>";
        foreach ($colonyList as $item) {
            $colonyOption[] = "<option value='".$item->colony_id."'".($colony_selected_id == $item->colony_id? 'selected':'').">".$item->colony_name."</option>";
        }
        return $colonyOption;
    }

    public function load_house_type($house_type_selected = null)
    {
        $houseTypeList = LHouseType::select('HOUSE_TYPE_ID','HOUSE_TYPE','HOUSE_SIZE')->get();
        $houseTypeOption = [];
        $houseTypeOption[] = "<option value=''>Please select an option</option>";
        foreach ($houseTypeList as $item) {
            $houseTypeOption[] = "<option value='".$item->house_type_id."'".($house_type_selected == $item->house_type_id ? 'selected':'').">".$item->house_type." Size : ".$item->house_size."</option>";
        }
        return $houseTypeOption;
    }

    public function load_building_infrastructure($building_infra_selected = null)
    {
        $buildingInfraList = LBuildingInfra::select('BUILDING_INFRA_ID','BUILDING_INFRA')->get();
        $buildinInfraOption = [];
        $buildinInfraOption[] = "<option value=''>Please select an option</option>";
        foreach ($buildingInfraList as $item) {
            $buildinInfraOption[] = "<option value='".$item->building_infra_id."'".($building_infra_selected == $item->building_infra_id ? 'selected':'').">".$item->building_infra."</option>";
        }
        return $buildinInfraOption;
    }

    public function load_emp_manager($emp_manger_selected = null)
    {
        $empManagerList = Employee::select('EMP_ID','EMP_CODE','EMP_NAME')->where('EMP_TYPE_ID','=','2')->where('EMP_ACTIVE_YN','=','Y')->get();
        $empManagerOption = [];
        $empManagerOption[] = "<option value=''>Please select an option</option>";
        foreach ($empManagerList as $item) {
            $empManagerOption[] = "<option value='".$item->emp_id."'".($emp_manger_selected == $item->emp_id ? 'selected':'').">".$item->emp_code." : ".$item->emp_name."</option>";
        }
        return $empManagerOption;
    }

    public function load_building_status($building_status_selected = null)
    {
        $buildingStatusList = LBuildingStatus::select('BUILDING_STATUS_ID','BUILDING_STATUS')->get();
        $buildingStatusOption = [];
        $buildingStatusOption[] = "<option value=''>Please select an option</option>";
        foreach ($buildingStatusList as $item) {
            $buildingStatusOption[] = "<option value='".$item->building_status_id."'".($building_status_selected == $item->building_status_id ? 'selected':'').">".$item->building_status."</option>";
        }
        return $buildingStatusOption;
    }

    public function store(Request $request,$id=null)
    {

        $params =[];
        try {
            $p_building_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $params = [
                        "p_building_id" => [
                            "value" => &$p_building_id,
                            "type" => \PDO::PARAM_INPUT_OUTPUT,
                            "length" => 255
                        ],
                    "p_building_name"            => $request->get("name_english"),
                    "p_building_name_bng"        => $request->get("name_bangla"),
                    "p_colony_id"                => $request->get("colony"),
                    "p_house_type_id"            => $request->get("house_type"),
                    "p_building_description"     => $request->get("description_english"),
                    "p_building_infra_id"        => $request->get("building_infrastructure"),
                    "p_no_of_floor"              => $request->get("no_of_floor"),
                    "p_no_of_house"              => $request->get("no_of_house"),
                    "p_hand_over_date"           => (($request->get("handover_date"))? date("Y-m-d", strtotime($request->get("handover_date"))):''),
                    "p_inauguration_date"        => (($request->get("inauguration_date"))? date("Y-m-d", strtotime($request->get("inauguration_date"))):''),
                    "p_expiration_date"          => (($request->get("expiration_date"))? date("Y-m-d", strtotime($request->get("expiration_date"))):''),
                    "p_building_status_id"       => $request->get("building_status"),
                    "p_contractor_id"            => '', //$request->get("colony_name_english"),
                    "p_buidling_description_bng" => $request->get("description_bangla"),
                    "p_building_manager_id"      => $request->get("manager"),
                    "p_building_block"           => $request->get("building_block"),
                    "p_building_length"          => $request->get("building_length"),
                    "p_building_width"           => $request->get("building_width"),
                    "p_no_water_tank"            => $request->get("no_of_water_tank"),
                    "p_no_reserve_tank"          => $request->get("no_of_reserve_tank"),
                    "p_lift_yn"                  => $request->get("lift_yn"),
                    "p_no_of_lift"               => $request->get("no_of_lift"),
                    "p_parking_yn"               => $request->get("parking_yn"),
                    "p_generator_yn"             => $request->get("generator_yn"),
                    "p_no_of_generator"          => $request->get("no_of_generator"),
                    "p_no_of_parking"            => $request->get("no_of_parking"),
                    "p_sec_intercom_no"          => $request->get("security_no"),
                    "p_fire_ext_yn"              => $request->get("fire_yn"),
                    "p_no_of_fire_ext"           => $request->get("no_of_fire_ext"),
                    "p_contractor"               => $request->get("contractor"),
                    "p_building_no"              => $request->get("building_no"),
                    "p_building_road_no"         => $request->get("building_road_no"),
                    "p_civil_construction_cost"  => $request->get("civil_construction_cost"),
                    "p_electric_work_cost"       => $request->get("electric_work_cost"),
                    "p_construction_year"        => (($request->get("construction_year"))? date("Y", strtotime($request->get("construction_year"))):''),
                    "p_dormitory_yn"             => $request->get("dormitory_yn"),
                    "p_insert_by"                => Auth()->ID(),
                    "o_status_code"         => &$statusCode,
                    "o_status_message"      => &$statusMessage
            ];

//            dd($params);
            DB::executeProcedure('allotment.building_entry', $params);

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = $this->populateBuilding_list($id);
        $dates['hand_over_date'] = isset($data['building']->hand_over_date)? Carbon::parse($data['building']->hand_over_date)->format('Y-m-d'):'';//$data['building']->hand_over_date->format('Y-m-d'):'';
        $dates['inauguration_date'] = isset($data['building']->inauguration_date)? $data['building']->inauguration_date->format('Y-m-d'):'';
        $dates['expiration_date'] = isset($data['building']->expiration_date)? $data['building']->expiration_date->format('Y-m-d'):'';

        $colony_id_selected = $data['building']->colony_id;
        $house_type_selected = $data['building']->house_type_id;
        $building_infrastructure_selected = $data['building']->building_infra_id;
        $emp_manger_selected =  $data['building']->building_manager_id;
        $contractor =  $data['building']->contractor;
        $building_status_selected =  $data['building']->building_status_id;

        if($emp_manger_selected){
            $item = Employee::select('EMP_ID','EMP_CODE','EMP_NAME')
                //->where('EMP_TYPE_ID','=','2')
                //->where('EMP_ACTIVE_YN','=','Y')
                ->where('EMP_ID','=',$emp_manger_selected)->first();
            if(isset($item->emp_id)){
                $empManagerOption = "<option value='".$item->emp_id."'".($emp_manger_selected == $item->emp_id ? 'selected':'').">".$item->emp_code." : ".$item->emp_name."</option>";
            }else{
                $empManagerOption = "";
            }
        }else{
            $empManagerOption ='';
        }

        $colonyOptionList     = $this->load_colony($colony_id_selected );
        $houseTypeOptionList  = $this->load_house_type($house_type_selected);
        $buildingInfraList    = $this->load_building_infrastructure($building_infrastructure_selected);
        $empManagerList       = $empManagerOption;
        $buildingStatusOptionList = $this->load_building_status($building_status_selected);
//        echo '<pre>';
//        print_r($data);
        return view('building.index',compact('dates','data','colonyOptionList','houseTypeOptionList','buildingInfraList','empManagerList','buildingStatusOptionList'));
      // return view('building.index',compact('data'));
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

        return redirect()->route('building.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
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

    public function datatableList()
    {
        $buildings = BuildingList::select('*')->orderBy('building_id', 'desc')->get();

        return datatables()->of($buildings)
            ->addIndexColumn()
//            ->addColumn('dormitory_yn', function($query){
//                if('dormitory_yn' == 'Y') {
//                    return 'Yes';
//                }else {
//                    return 'No';
//                }
//            })
            ->addColumn('action', function ($query) {
                return '<a href="' . route('building.edit', $query->building_id) . '" class="btn btn-xs btn-primary"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->make(true);
    }
}
