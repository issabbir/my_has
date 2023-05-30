<?php

namespace App\Http\Controllers\has;

use App\Entities\Admin\LDesignation;
use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\HouseAllotment\LHouseStatus;
use App\Entities\HouseAllotment\LHouseType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;

class HouseController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    public function index(Request $request)
    {
        $data = $this->houses();
        $flatList = DB::table('l_flat_name')->get();

        return view('house.index', compact('data', 'flatList'));
    }

    public function datatableList(Request $request)
    {
        if ($request->get('building_id') == null) {
            $houses = HouseList::where('tmp_yn', 'N')->orderby('insert_date', 'DESC')->get();
        } else {
            $houses = HouseList::select('*')->where('building_id', $request->get('building_id'))->where('tmp_yn', 'N')->orderby('insert_date', 'DESC')->get();
        }

        return datatables()->of($houses)
            ->addIndexColumn()
            ->addColumn('dept', function ($query) {

                if (isset($query->department->department_name)) {
                    $dep = $query->department->department_name;
                    return $dep;
                } else {
                    return 'N/A';
                }


            })
            ->addColumn('action', function ($query) {
                return '<a href="' . route('house.edit', $query->house_id) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->make(true);
    }

    public function tempDatatableList(Request $request)
    {

//        if($request->get('building_id') != null) {
        $houses = HouseList::select('*')->where('building_id', $request->get('building_id'))->where('tmp_yn', 'Y')->orderby('insert_date', 'DESC')->get();
//dd($houses);
        return datatables()->of($houses)
            ->addIndexColumn()
            ->addColumn('house_name', function ($query) {
                if ($query->dormitory_yn == 'Y') {
                    return $query->house_name . '-' . $query->house_code;
                }
                return $query->house_name;
            })
            ->addColumn('action', function ($query) {
                return '<button type="button" class="btn p-0" style="color: red" onclick="deleteTemp(' . $query->house_id . ')"><i class="bx bx-trash cursor-pointer"></i></button>';
            })
            ->make(true);
//        }
    }

    public function store(Request $request)
    {
        //dd($request);

        $params = $this->houseEntry($request);

        return $params;
        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

//        $flashMessageContent = $this->flashMessageManager->getMessage($params);
//        if($params['o_status_code'] != 1){
//            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
//        }
//
//        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    public function delete(Request $request)
    {
        $house_id = $request->get('house_id');
        $del = HouseList::where('house_id', $house_id)->delete();

        return $del;
    }

    public function permanent(Request $request)
    {
//        $house_list = HouseList::where('building_id',$request->get('building'))->where('tmp_yn', 'Y')->get('house_id');
//        foreach ($house_list as $house)
//        {
//            dd($house->house_id);
//        }
//        HouseList::where('building_id', $request->get('building'))
//            ->where('tmp_yn', 'Y')
//            ->update(['tmp_yn' => 'N']);

        if (count(HouseList::where('building_id', $request->get('building'))->where('tmp_yn', 'Y')->get()) > 0) {
            DB::table('house_list')
                ->where('building_id', $request->get('building'))
                ->where('tmp_yn', 'Y')
                ->update(['tmp_yn' => 'N']);

            return redirect()->back()->with('success', 'Submitted Successfully');
        } else {
            return redirect()->back()->with('error', 'Nothing to Submit');
        }
    }

    private function houseEntry(Request $request, $id = '')
    {
        DB::beginTransaction();
        $house = $request->post();
        //dd($house);
        $paringId = isset($house['parking_id']) ? $house['parking_id'] : '';
        $btclNumber = isset($house['btcl_number']) ? $house['btcl_number'] : '';
        $designationId = isset($house['designation_id']) ? $house['designation_id'] : '';
        $intercomNo = isset($house['intercom_no']) ? $house['intercom_no'] : '';

        if (empty($id)) {
            $same_data_entry_check = HouseList::Where(['BUILDING_ID' => $house['building_id'], 'FLAT_NAME_ID' => $house['house_name'], 'HOUSE_CODE' => $house['house_code']])->first();

            if ($same_data_entry_check) {
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => 'Data already exists'];
            }
        }


        try {
            $p_house_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $params = [
                "p_HOUSE_ID" => [
                    "value" => &$p_house_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],

                'p_HOUSE_CODE' => $house['house_code'],
//                'p_HOUSE_NAME' => $house['house_name'],
                'p_BUILDING_ID' => $house['building_id'],
                'p_HOUSE_SIZE' => $house['house_size'],
                'p_FLOOR_NUMBER' => $house['floor_number'],
                'p_DORMITORY_YN' => $house['dormitory_yn'],
                'p_PARKING_YN' => $house['parking_yn'],
                'p_PARKING_ID' => $paringId,
                'p_DOUBLE_GAS_YN' => $house['double_gas_yn'],
                'p_WATER_TAP' => $house['water_tap'],
                'p_ELECTRIC_METER_NUMBER' => $house['electric_meter_number'],
                'p_BTCL_CONNECTION_YN' => $house['btcl_connection_yn'],
                'p_BTCL_NUMBER' => $btclNumber,
                'p_RESERVE_YN' => $house['reserve_yn'],
                'p_RESERVE_FOR' => $designationId,
                'p_HOUSE_STATUS_ID' => $house['house_status_id'],
//                'p_HOUSE_NAME_BNG' => $house['house_name_bng'],
                'p_HOUSE_NAME_BNG' => '',
                'p_HOUSE_TYPE_ID' => $house['house_type_id'],
                'p_INTERCOM_YN' => $house['intercom_yn'],
                'p_INTERCOM_NO' => $intercomNo,
                'p_TMP_YN' => 'Y', //isset($id)?'N':'Y',
                'p_FLAT_NAME_ID' => $house['house_name'],
                'p_DORMITORY_TOTAL_SEAT' => isset($house['dormitory_total_seat']) ? $house['dormitory_total_seat'] : 0,
                'p_DORMITORY_ROOM_NO' => isset($house['room_number']) ? $house['room_number'] : '',
                'p_INSERT_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];

            DB::executeProcedure('allotment.house_entry', $params);

            DB::commit();
            return $params;
        } catch (\Exception $e) {
            DB::rollback();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
        }

    }

    public function edit(Request $request, $id)
    {
        $data = $this->houses($id);
        //dd($data);
        $flatList = DB::table('l_flat_name')->get();

        return view('house.index', compact('data', 'flatList'));
    }

    public function update(Request $request, $id)
    {

        $params = $this->houseEntry($request, $id);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);

        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('house.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function houses($id = null)
    {
        $house = null;

        $houses = HouseList::all();

        $buildings = BuildingList::all();
        $houseTypes = [];
        $houseStatuses = LHouseStatus::all();
        $designations = LDesignation::where('enable_yn', 'Y')->where('designation', 'CHAIRMAN')->orWhere('designation', 'like', 'MEMBER%')->get();
        $floors = [];
        if ($id) {
            $house = HouseList::find($id);
            //dd($house);
            $building = BuildingList::find($house->building_id);
            $floors = range(1, $building->no_of_floor);
            $houseTypes = LHouseType::where('house_type_id', $building->house_type_id)->get();
            $dormitory_yn = $house->dormitory_yn;
            //dd($dormitory_yn);
        }

        $data = [
            'house' => $house ? $house : new HouseList(),
            'houses' => datatables()->of($houses)->make(true),
            'buildings' => $buildings,
            'house_types' => $houseTypes,
            'floors' => $floors,
            'designations' => $designations,
            'house_statuses' => $houseStatuses,
            'status' => (count($houses) > 0 ? true : false),
            'message' => (count($houses) > 0 ? '' : 'No Data Found')

        ];

        return $data;
    }

    public function load(Request $request, $id)
    {
        if ($id) {
            return HouseList::find($id);
        }

        return [];
    }

    public function loadData(Request $request, $buildingId)
    {
        $buildingData = [];

        if ($buildingId) {
            $building = BuildingList::find($buildingId);
            $floors = range(1, $building->no_of_floor);
            $floorsSelect = view('house.floors')->with('floors', $floors)->render();
            $buildingData['floors'] = $floorsSelect;
            $houseTypes = LHouseType::where('house_type_id', $building->house_type_id)->get();
            $houseTypesSelect = view('house.housetypes')->with('housetypes', $houseTypes)->render();
            $buildingData['housetypes'] = $houseTypesSelect;
            $buildingData['colony'] = $building->colony;
            $buildingData['house_size'] = $houseTypes[0]->house_size;
            $buildingData['dormitory_yn'] = $building->dormitory_yn;

        }

        return $buildingData;
    }
}
