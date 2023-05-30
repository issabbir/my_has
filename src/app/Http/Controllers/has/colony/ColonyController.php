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

class ColonyController extends Controller
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
        return view('dashboard.index');
    }


    public function colony_register()
    {
        $data = $this->colony_register_list();
        $divisionOptionList = $this->load_division();
        $colonyTypeOptionList = $this->load_colony_type();
        return view('colony.index',compact('data'),compact('colonyTypeOptionList','divisionOptionList'));
    }

    public function loadColonyRegister(Request $request, $id)
    {
        $colony = Colony::find($id);
        return $colony;
    }

    public function colony_register_list($id=null)
    {
        $single_colony_data = null;
        if($id){
            $single_colony_data = Colony::find($id);
        }
        $register_data = Colony::all(); //->where('colony_yn','Y')
        $data = [
            'table_info' => datatables()->of($register_data)->make(true),
            'colony'     => $single_colony_data? $single_colony_data : new Colony(),
            'status'     => (count($register_data)> 0? true: false),
            'message'    => (count($register_data)> 0? '': 'No Data Found')
        ];
        return  $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function report_colony_register()
    {
        echo 'report_colony_register';
        die();
    }

    public function load_colony_type($colonyTypeSelected = null)
    {
        $colonyType = ColonyType::select('COLONY_TYPE_ID','COLONY_TYPE','COLONY_TYPE_BNG')->get();

        $colonyTypeOption = [];
         $colonyTypeOption[] = "<option value=''>Please select an option</option>";
          foreach ($colonyType as $item) {
             $colonyTypeOption[] = "<option value='".$item->colony_type_id."'".($colonyTypeSelected == $item->colony_type_id? 'selected':'').">".$item->colony_type."</option>";
         }

         return $colonyTypeOption;
    }

    public function load_division($divisionSelected = null)
    {
        $division = LGeoDivision::select('GEO_DIVISION_ID','GEO_DIVISION_NAME')->get();
        //return $division;
        // print_r($division);
         $divisionOption = [];
         $divisionOption[] = "<option value=''>Please select an option</option>";
          foreach ($division as $item) {
             $divisionOption[] = "<option value='".$item->geo_division_id."'".($divisionSelected == $item->geo_division_id? 'selected':'').">".$item->geo_division_name."</option>";
         }

         return $divisionOption;

    }


    public function loadDivisionToDistrict($id,$districtSelected=null)
    {
        $district = LGeoDistrict::select('GEO_DISTRICT_ID','GEO_DISTRICT_NAME')->where('GEO_DIVISION_ID','=', $id)->get();
        // print_r($division);
        $districtOption = [];
        $districtOption[] = "<option value=''>Please select an option</option>";
        foreach ($district as $item) {
            $districtOption[] = "<option value='".$item->geo_district_id."'".($districtSelected == $item->geo_district_id? 'selected':'').">".$item->geo_district_name."</option>";
        }
        return $districtOption;
    }

    public function loadDistirictToThana($id,$districtSelected=null)
    {
        $thana = LGeoThana::select('GEO_THANA_ID','GEO_THANA_NAME')->where('GEO_DISTRICT_ID','=', $id)->get();
        // print_r($thana);
        $thanaOtpion = [];
        $thanaOtpion[] = "<option value=''>Please select an option</option>";
        foreach ($thana as $item) {
            $thanaOtpion[] = "<option value='".$item->geo_thana_id."'".($districtSelected == $item->geo_thana_id? 'selected':'').">".$item->geo_thana_name."</option>";
        }

        return $thanaOtpion;
    }

    public function load_division_to_district(Request $request, $id)
    {
        $selected_dist = $request->get('selected_dist');
        $district = LGeoDistrict::select('GEO_DISTRICT_ID','GEO_DISTRICT_NAME')->where('GEO_DIVISION_ID','=', $id)->get();
         $districtOption = [];
         $districtOption[] = "<option value=''>Please select an option</option>";
          foreach ($district as $item) {
                  $districtOption[] = "<option value='".$item->geo_district_id."'".($selected_dist == $item->geo_district_id? 'selected':'').">".$item->geo_district_name."</option>";
         }

         return $districtOption;
    }

    public function load_district_to_thana(Request $request, $id)
    {   $selected_thana = $request->get('selected_thana');
        $thana = LGeoThana::select('GEO_THANA_ID','GEO_THANA_NAME')->where('GEO_DISTRICT_ID','=', $id)->get();
        // print_r($thana);
         $thanaOtpion = [];
         $thanaOtpion[] = "<option value=''>Please select an option</option>";
          foreach ($thana as $item) {
             $thanaOtpion[] = "<option value='".$item->geo_thana_id."'".($selected_thana == $item->geo_thana_id? 'selected':'').">".$item->geo_thana_name."</option>";
         }

         return $thanaOtpion;
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
    public function store(Request $request,$id=null)
    {
        $params =[];
        try {
            $p_colony_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            //$statusYN = 'Y';
            $params = [
                    "p_colony_id" => [
                        "value" => &$p_colony_id,
                        "type" => \PDO::PARAM_INPUT_OUTPUT,
                        "length" => 255
                    ],

                    "p_colony_name"         => $request->get("colony_name_english"),
                    "p_colony_name_bng"     => $request->get("colony_name_bangla"),
                    "p_colony_address"      => $request->get("colony_address_english"),
                    "p_colony_address_bng"  => $request->get("colony_address_bangla"),
                    "p_geo_division_id"     => $request->get("division"),
                    "p_geo_district_id"     => $request->get("district"),
                    "p_geo_thana_id"        => $request->get("thana"),
                    "p_description"         => $request->get("colony_description_english"),
                    "p_description_bng"     => $request->get("colony_description_bangla"),
                    "p_colony_type_id"      => $request->get("colony_type"),
                    "p_colony_yn"           => ($request->get("colony_yn"))? $request->get("colony_yn"):'Y',
                    "p_insert_by"           => Auth()->ID(),
                    "o_status_code"         => &$statusCode,
                    "o_status_message"      => &$statusMessage
            ];

             DB::executeProcedure('allotment.colony_entry', $params);

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
    public function edit($id)
    {

        $data = $this->colony_register_list($id);

        $colonyTypeSelected = $data['colony']->colony_type_id;
        $division_selected = $data['colony']->geo_division_id;
        $district_selected = $data['colony']->geo_district_id;
        $thana_selected = $data['colony']->geo_thana_id;

        $divisionOptionList = $this->load_division($division_selected);
        $districtOptionList = $this->loadDivisionToDistrict($division_selected,$district_selected);
        $thanaOptionList = $this->loadDistirictToThana($district_selected,$thana_selected);
        $colonyTypeOptionList = $this->load_colony_type($colonyTypeSelected);

        return view('colony.index',compact('data'),compact('colonyTypeOptionList','divisionOptionList', 'districtOptionList','thanaOptionList'));

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

        return redirect()->route('colony.colony_register')->with($flashMessageContent['class'], $flashMessageContent['message']);

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
        $register_data = Colony::select('*')->get();
        return datatables()->of($register_data)
            ->addIndexColumn()
            ->addColumn('division.geo_division_name', function ($query) {
                return (isset($query->division->geo_division_name)? $query->division->geo_division_name:" ");
            })
            ->addColumn('district.geo_district_name', function ($query) {
                return (isset($query->district->geo_district_name)? $query->district->geo_district_name:" ");
            })
            ->addColumn('thana.geo_thana_name', function ($query) {
                return (isset($query->thana->geo_thana_name)? $query->thana->geo_thana_name: " ");
            })
            ->addColumn('action', function ($query) {
                return '<a href="' . route('colony.edit', $query->colony_id) . '" ><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->make(true);
    }

}
