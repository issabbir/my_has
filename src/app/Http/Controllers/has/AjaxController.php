<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 1/28/20
 * Time: 3:39 PM
 */

namespace App\Http\Controllers\has;

use App\Contracts\AdvertisementContract;
use App\Contracts\HinterChangeApplicationContract;
use App\Contracts\InterchangeTakeoverContract;
use App\Contracts\LookupContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Colony\Colony;
use App\Entities\HouseAllotment\Acknowledgemnt;
use App\entities\houseAllotment\AllotLetter;
use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Pmis\Employee\EmpFamily;
use App\Entities\Pmis\Employee\Employee;
use App\Enums\AppType;
use App\Enums\Department;
use App\Enums\HouseStatus;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Managers\HinterChangeApplicationManager;
use App\Managers\InterchangeTakeoverManager;
use App\Managers\LookupManager;
use App\Managers\Pmis\Employee\EmployeeManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;
use App\Helpers\HelperClass;

class AjaxController extends Controller
{
    /** @var LookupManager */
    private $lookupManager;

    /** @var EmployeeManager */
    private $employeeManager;

    private $advertisementManager;

    /** @var InterchangeTakeoverManager */
    private $interchangeTakeoverManager;

    /** @var HinterChangeApplicationManager */
    private $houseInterchangeApplicationManager;

    public function __construct(LookupContract $lookupManager, EmployeeContract $employeeManager, AdvertisementContract $advertisementManager, InterchangeTakeoverContract $interchangeTakeoverManager, HinterChangeApplicationContract $houseInterchangeApplicationManager)
    {
        $this->lookupManager = $lookupManager;
        $this->employeeManager = $employeeManager;
        $this->advertisementManager = $advertisementManager;
        $this->interchangeTakeoverManager = $interchangeTakeoverManager;
        $this->houseInterchangeApplicationManager = $houseInterchangeApplicationManager;
    }

    public function divisions(Request $request)
    {
        return $this->lookupManager->findDivisions();
    }

    public function districts(Request $request, $divisionId)
    {
        $districts = [];

        if ($divisionId) {
            $districts = $this->lookupManager->findDistrictsByDivision($divisionId);

        }

        $html = view('ajax.districts')->with('districts', $districts)->render();

        return response()->json(array('html' => $html));
    }

    public function thanas(Request $request, $districtId)
    {
        $thanas = [];

        if ($districtId) {
            $thanas = $this->lookupManager->findThanasByDistrict($districtId);
        }

        $html = view('ajax.thanas')->with('thanas', $thanas)->render();

        return response()->json(array('html' => $html));
    }

    public function employees(Request $request)
    {

        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findEmployeeCodesBy($searchTerm);

        return $employees;
    }

    public function employee(Request $request, $employeeCode)
    {

        $employeeInformation = null;
        $advertisements = null;
        $availableAdvertisement = 0;
        $house_type = null;
        $eligible_for = null;

        if ($employeeCode) {
//            $employee = $this->employeeManager->findEmployeeInformation($employeeCode);
            $employee = $this->employeeManager->findEmployeeInformationForEligible($employeeCode);

            if ($employee) {
                $house_type = [];
                $eligible_id = [];
                foreach ($employee as $emp) {
                    $house_type[] = $emp['house_type_id'];
                    $eligible_id[] = $emp['eligible_id'];
                    if ($emp == end($employee)) {
                        $eligible_for .= $emp['eligible_for'];
                    } else {
                        $eligible_for .= $emp['eligible_for'] . '/';
                    }
                }

                $employeeInformation = $employee[0];
//                $advertisementsByHouseType = $this->advertisementManager->getAdvertisementByHouseType($employeeInformation['house_type_id'], $employeeInformation['department_id']);
                $advertisementsByHouseType = $this->advertisementManager->getAdvertisementByHouseTypeForArray($house_type, $employeeInformation['department_id']);

                $availableAdvertisement = count($advertisementsByHouseType);
//                $house_type_data = LHouseType::where('house_type_id', $employeeInformation['eligible_id'])->get();
                $house_type_data = LHouseType::whereIn('house_type_id', $eligible_id)->get();

                if ($employeeInformation['house_category_id'] == 3) {
                    $dorAvailAds = $this->advertisementManager->getAdvertisementByHouseType('11', $employeeInformation['department_id']);
                    $availableAdvertisement += count($dorAvailAds);
                    $advertisementsByHouseType = array_merge($advertisementsByHouseType, $dorAvailAds);
//                    $house_type_data = LHouseType::where('house_type_id', '11')->orWhere('house_type_id', $employeeInformation['eligible_id'])->get();
                    $house_type_data = LHouseType::where('house_type_id', '11')->orWhereIn('house_type_id', $eligible_id)->get();

                }

                $house_type = view('ajax.house-type')->with('house_types', $house_type_data)->render();
                $advertisements = view('ajax.advertisements')->with('advertisements', $advertisementsByHouseType)->render();
            }
        }

        return ['emp_code' => $employeeCode, 'employeeInformation' => $employeeInformation, 'advertisements' => $advertisements, 'house_types' => $house_type, 'availableAdvertisement' => $availableAdvertisement, 'eligible_for' => $eligible_for];
    }

    public function employeeDetailsForAllottee(Request $request, $employeeCode)
    {
        $employeeInformation = null;
        if ($employeeCode) {
            $employee = $this->employeeManager->findEmployeeInformation($employeeCode);

            $employeeInformation = $employee;
        }

        return ['emp_code' => $employeeCode, 'employeeInformation' => $employeeInformation];
    }

    public function employeeHusband(Request $request, $employeeCode)
    {
        $employeeInformation = null;
        if ($employeeCode) {
            $employee = $this->employeeManager->findEmployeeWithAllottedHouseInformation($employeeCode);
            if ($employee) {
                $employeeInformation = $employee;
            }
        }
        return $employeeInformation;
    }

    public function advertiseHouseType(Request $request, $advertisementId)
    {
        $houseTypes = [];
        $houseTypeArray = [];
        $houseTypeFinalArray = [];
        $houseTypeArray = $this->advertisementManager->findHouseTypesByAdvertisementId($advertisementId);

        /* foreach ($houseTypeArray as $houseTypeArrayData){
             if(Auth::user()->hasPermission('HAS_HOD_CAN_ADVERTISE_A_D')){
                 $alowedHouseTypeArray = (array)json_decode(env('HOD_ALLOWED_HOUSE_TYPE'));
                 if(1!=HelperClass::custom_array_search($houseTypeArrayData->house_type, $alowedHouseTypeArray, 'bool')){
                    continue;
                 }
             }
             $houseTypeFinalArray[$houseTypeArrayData->house_type_id] = $houseTypeArrayData->house_type;
         }*/


        if ($advertisementId) {
            $houseTypes = view('ajax.housetypes')->with('houseTypes', $houseTypeArray)->render();
        }

        return $houseTypes;
    }

    public function house(Request $request, $houseId)
    {
        $house = HouseList::find($houseId);

        return $house;
    }

    // For Replaced
    public function employeeBasicInfoWithReplacedAllocatedHouse(Request $request, $employeeCode)
    {

        $employeeInformation = null;
        $advertisements = null;
        $availableAdvertisement = 0;

        if ($employeeCode) {
            $employee = $this->employeeManager->findEmployeeBasicInformationWithReplacedAllocatedHouse($employeeCode);

            if ($employee) {
                $employeeInformation = $employee;
            }
        }

        return ['employeeInformation' => $employeeInformation];
    }

    // End Replaced

    public function employeeBasicInfoWithAllocatedHouse(Request $request, $employeeCode)
    {
        $employeeInformation = null;
        $advertisements = null;
        $availableAdvertisement = 0;

        if ($employeeCode) {
            $employee = $this->employeeManager->findEmployeeBasicInformationWithAllocatedHouse($employeeCode);

            if ($employee) {
                $employeeInformation = $employee;
            }
        }

        return ['employeeInformation' => $employeeInformation];
    }

    public function allocatedHouseEmployeeList(Request $request)
    {
        $employeeCode = $request->get('q');
        $employees = [];
        if ($employeeCode) {
            $employeeCodes = $this->employeeManager->findEmployeeListWhoHasHouseAllocated($employeeCode);
            if ($employeeCodes) {
                foreach ($employeeCodes as $employeeCode) {
                    $employees[] = $employeeCode->emp_code;
                }
                return $employees;
            }
            return [];
        }
        return [];
    }

    //Take Over Start
    public function allottedLetterList(Request $request)
    {
        //
    }

    public function allottedLetterWiseAndEmployeeDetails(Request $request, $allotmentNo)
    {
        $employeeInformation = null;
        $advertisements = null;
        $availableAdvertisement = 0;

        if ($allotmentNo) {
            $employee = $this->employeeManager->findEmployeeBasicInformationWithAllocatedHouseWithLetterByAllotmentNo($allotmentNo);

            if ($employee) {
                $employeeInformation = $employee;
            }
        }

        return ['employeeInformation' => $employeeInformation];
    }

    public function allottedLetterWiseReplacedEmployeeDetails(Request $request, $allotmentNo)
    {
        $employeeInformation = null;
        $advertisements = null;
        $availableAdvertisement = 0;

        if ($allotmentNo) {
            $employee = $this->employeeManager->findReplacedEmployeeBasicInformationWithAllocatedHouseWithLetterByAllotmentNo($allotmentNo);

            if ($employee) {
                $employeeInformation = $employee;
            }
        }

        return ['employeeInformation' => $employeeInformation];
    }

    public function empCodeWiseAndEmployeeDetails(Request $request, $empId)
    {

        $employeeInformation = null;
        $advertisements = null;
        $availableAdvertisement = 0;
        $old_chk = DB::selectOne("SELECT H.OLD_ENTRY_YN  FROM HAS.HOUSE_ALLOTTMENT h WHERE H.EMP_ID = $empId");


        if ($empId) {
            if (isset($old_chk->old_entry_yn)) {
                if ($old_chk->old_entry_yn == 'N') {
                    $employee = $this->employeeManager->findEmployeeBasicInformationWithAllocatedHouseWithLetterByEmpCode($empId);

                } else {
                    $employee = $this->employeeManager->findEmployeeBasicInformationWithAllocatedOldHouseWithLetterByEmpCode($empId);
                }
            }
            if ($employee) {
                $employeeInformation = $employee;
            }

        }


        return ['employeeInformation' => $employeeInformation];
    }

    public function empCodeWiseReplacedEmployeeDetails(Request $request, $empCode)
    {
        $employeeInformation = null;
        $advertisements = null;
        $availableAdvertisement = 0;

        if ($empCode) {
            $employee = $this->employeeManager->findReplacedEmployeeBasicInformationWithAllocatedHouseWithLetterByEmpCode($empCode);

            if ($employee) {
                $employeeInformation = $employee;
            }
        }

        return ['employeeInformation' => $employeeInformation];
    }

    //Take Over End


    public function employeeWithAllottedHouse(Request $request, $employeeCode)
    {

        $employeeInformation = null;
        $advertisements = null;

        if ($employeeCode) {
            $employee = $this->employeeManager->employeeInfoWithAllottedHouse($employeeCode);
            if ($employee) {
                $employeeInformation = $employee;
            }
        }

        return ['emp_code' => $employeeCode, 'employeeInformation' => $employeeInformation];
    }

    public function employeesWithAllottedHouses(Request $request)
    {
        $exclude = $request->get('exclude');
        $jsonDecodedExcludeEmpCode = json_decode($exclude) ?? [];

        $excludeEmpCodes = array_filter($jsonDecodedExcludeEmpCode);
        $employeeCode = $request->get('term');
        $employeeCodes = $this->employeeManager->employeesWithAllottedHouses($employeeCode, $excludeEmpCodes);

        return $employeeCodes;
    }

    public function interchangeInformation(Request $request, $allotmentNo)
    {
        $interchangeInformation = null;
        if ($allotmentNo) {
            $interchangeInformation = $this->interchangeTakeoverManager->findBy($allotmentNo);

        }
        return ['interchangeInformation' => $interchangeInformation];
    }

    public function buildingsByColony(Request $request, $colonyId, $dormitoryYN = null)
    {
        $buildingsHtml = '';

        //$buildings = BuildingList::where('colony_id', $colonyId)->get();

        if ($dormitoryYN) {
            $buildings = BuildingList::where(['colony_id' => $colonyId, 'dormitory_yn' => $dormitoryYN])->get();
        } else {
            $buildings = BuildingList::where(['colony_id' => $colonyId])->get();
        }
        if ($buildings) {
            $buildingsHtml = view('ajax.buildings-by-colony')->with('buildings', $buildings)->render();
        }

        return $buildingsHtml;
    }

    public function buildingsByColonyHouseType(Request $request)
    {
        $cid = $request->get('cid');
        $htid = $request->get('htid');

        $buildingsHtml = '';

        $buildings = BuildingList::where(['colony_id' => $cid, 'HOUSE_TYPE_ID' => $htid])->get();

        if ($buildings) {
            $buildingsHtml = view('ajax.buildings-by-colony')->with('buildings', $buildings)->render();
        }

        return $buildingsHtml;
    }

    public function buildingsByColonyDormitory(Request $request, $colonyId, $dormitoryYN = null, $houseTypeId)
    {
//        dd($houseTypeId);
        $buildingsHtml = '';

        $user = auth()->user();

        $logUser = DB::selectOne("SELECT P.EMP_ID,U.EMP_ID,P.DPT_DEPARTMENT_ID as DEPARTMENT_ID
  FROM CPA_SECURITY.SEC_USERS U, PMIS.EMPLOYEE p
  where U.EMP_ID = P.EMP_ID
  and U.EMP_ID = '$user->emp_id' ");


        //$buildings = BuildingList::where('colony_id', $colonyId)->get();
        if ($user->user_name == 'admin') {
            if ($dormitoryYN) {
                $buildings = BuildingList::where(['colony_id' => $colonyId, 'dormitory_yn' => $dormitoryYN, 'house_type_id' => $houseTypeId])->get();
            }
        } elseif ($logUser->department_id != 5) {
            if ($dormitoryYN) {

//            $buildings = BuildingList::where(['colony_id' => $colonyId, 'dormitory_yn' => $dormitoryYN])->get();

                $buildings = DB::select("select distinct b.BUILDING_ID, B.BUILDING_NAME from building_list b , house_list h where H.BUILDING_ID = B.BUILDING_ID
and B.COLONY_ID = $colonyId
and B.DORMITORY_YN = '$dormitoryYN'
and h.HOUSE_TYPE_ID = '$houseTypeId'"); // H.DPT_DEPARTMENT_ID = $logUser->department_id
            }
        } elseif ($logUser->department_id == 5) {

            if ($dormitoryYN) {

                $buildings = BuildingList::where(['colony_id' => $colonyId, 'dormitory_yn' => $dormitoryYN, 'house_type_id' => $houseTypeId])->get();
            }
        } else {
            $buildings = BuildingList::where(['colony_id' => $colonyId, 'house_type_id' => $houseTypeId])->get();
        }


        if ($buildings) {
            $buildingsHtml = view('ajax.buildings-by-colony')->with('buildings', $buildings)->render();
        }

        return $buildingsHtml;
    }

    public function houseTypesByBuildingDormitory(Request $request, $buildingId, $dormitoryYN = 'N')
    {
        /* Formatted on 10/06/2022 10:41:42 AM (QP5 v5.326) */
        $houseTypes = DB::select('SELECT DISTINCT HT.HOUSE_TYPE_ID, HT.HOUSE_TYPE
    FROM HAS.HOUSE_LIST H, HAS.L_HOUSE_TYPE HT
   WHERE     H.HOUSE_STATUS_ID = 1
         AND HT.HOUSE_TYPE_ID = H.HOUSE_TYPE_ID
         AND H.BUILDING_ID = :p_building_id
         AND H.DORMITORY_YN = :p_dormitory_yn
ORDER BY HT.HOUSE_TYPE_ID ASC', ['p_building_id' => $buildingId, 'p_dormitory_yn' => $dormitoryYN]);
//        dd($houseTypes);
        $houseTypeHtml = view('ajax.house-types-by-building-dormitory')
            ->with('houseTypes', $houseTypes)->with('dormitoryYN', $dormitoryYN)->render();
//dd($houseTypeHtml);
        return $houseTypeHtml;
    }

    public function houseListByBuilding(Request $request, $buildingId, $dormitoryYN = 'N', $houseTypeId)
    {
//        dd($buildingId);

        //$buildingId ='20100016'; // to test
        $houseListHtml = '';

        // $houseList = HouseList::where('building_id', $buildingId)->get();

        // New query for house list without alloted houses at 3rd January 2022 according to New CR + again query changed at 17 january 2022

//        $houseList = DB::select('SELECT h.*
//  FROM HAS.HOUSE_LIST h
// WHERE     h.house_id not in
//               (SELECT HOUSE_ID
//                  FROM HAS.HOUSE_ALLOTTMENT
//                 WHERE house_id = h.house_id
//                 AND HAND_OVER_ID is null)
//       AND h.BUILDING_ID = :p_building_id
//       AND h.DORMITORY_YN = :p_dormitory_yn', ['p_building_id' => $buildingId, 'p_dormitory_yn' => $dormitoryYN]);
//
// house available list not show (query change)
//
//
        $user = auth()->user();

        $logUser = DB::selectOne("SELECT P.EMP_ID,U.EMP_ID,P.DPT_DEPARTMENT_ID as DEPARTMENT_ID
  FROM CPA_SECURITY.SEC_USERS U, PMIS.EMPLOYEE p
  where U.EMP_ID = P.EMP_ID
  and U.EMP_ID = '$user->emp_id' ");


        if ($user->user_name == 'admin') {
            $houseList = DB::select('SELECT h.*
  FROM HAS.HOUSE_LIST h
 WHERE
        H.HOUSE_STATUS_ID =  1
       AND h.BUILDING_ID = :p_building_id
       AND h.DORMITORY_YN = :p_dormitory_yn
       AND h.HOUSE_TYPE_ID = :p_house_type_id
       order by H.HOUSE_ID', ['p_building_id' => $buildingId, 'p_dormitory_yn' => $dormitoryYN, 'p_house_type_id' => $houseTypeId]);

        } elseif ($logUser->department_id != 5) {

            $houseList = DB::select('SELECT h.*
  FROM HAS.HOUSE_LIST h
 WHERE
        H.HOUSE_STATUS_ID =  1
       AND h.BUILDING_ID = :p_building_id
       AND h.DORMITORY_YN = :p_dormitory_yn
       AND h.HOUSE_TYPE_ID = :p_house_type_id
       order by H.HOUSE_ID ', ['p_building_id' => $buildingId, 'p_dormitory_yn' => $dormitoryYN, 'p_house_type_id' => $houseTypeId]);

        } elseif ($logUser->department_id == 5) {
            $houseList = DB::select('SELECT h.*
  FROM HAS.HOUSE_LIST h
 WHERE
        H.HOUSE_STATUS_ID =  1
       AND h.BUILDING_ID = :p_building_id
       AND h.DORMITORY_YN = :p_dormitory_yn
       AND h.HOUSE_TYPE_ID = :p_house_type_id
       order by H.HOUSE_ID', ['p_building_id' => $buildingId, 'p_dormitory_yn' => $dormitoryYN, 'p_house_type_id' => $houseTypeId]);

        }

        //if($houseList) {
        $houseListHtml = view('ajax.houselist-by-building')
            ->with('houseList', $houseList)->with('dormitoryYN', $dormitoryYN)->render();
        // }

        return $houseListHtml;
    }

    public function houseListByBuildingForReport(Request $request, $buildingId, $dormitoryYN = 'N')
    {

        $houseList = DB::select('SELECT h.*
  FROM HAS.HOUSE_LIST h
 WHERE  h.house_id not in
    (SELECT HOUSE_ID
                  FROM HAS.HOUSE_ALLOTTMENT
                 WHERE house_id = h.house_id)
       AND h.BUILDING_ID = :p_building_id
       AND h.DORMITORY_YN = :p_dormitory_yn', ['p_building_id' => $buildingId, 'p_dormitory_yn' => $dormitoryYN]);

        //if($houseList) {
        $houseListHtml = view('ajax.report-houselist-by-building')->with('houseList', $houseList)->with('dormitoryYN', $dormitoryYN)->render();
        // }

        return $houseListHtml;
    }

    public function AllHouseListByBuildingForReport(Request $request, $buildingId, $dormitoryYN = 'N')
    {

        $houseList = DB::select('SELECT h.*
  FROM HAS.HOUSE_LIST h
 WHERE      h.BUILDING_ID = :p_building_id
       AND h.DORMITORY_YN = :p_dormitory_yn', ['p_building_id' => $buildingId, 'p_dormitory_yn' => $dormitoryYN]);

        //if($houseList) {
        $houseListHtml = view('ajax.report-houselist-by-building')->with('houseList', $houseList)->with('dormitoryYN', $dormitoryYN)->render();
        // }

        return $houseListHtml;
    }

    public function housedetailsByHouse(Request $request, $houseId)
    {
        //$houseId ='20100016';
        $houseDetailsHtml = '';
        $houseDetails = HouseList::where('house_id', $houseId)
            ->leftJoin('building_list', 'house_list.BUILDING_ID', '=', 'building_list.BUILDING_ID')
            ->first();

        if ($houseDetails) {
            $houseDetailsHtml = view('ajax.housedetails-info-for-allottee')->with('houseDetails', $houseDetails)->render();
        }

        return $houseDetailsHtml;
    }

    public function houseTypesByColony(Request $request, $colonyId)
    {
        $houseTypeListHtml = '';
        $houseTypeList = Colony::where('l_colony.colony_id', $colonyId)
            ->leftJoin('building_list', 'building_list.colony_id', '=', 'l_colony.colony_id')
            ->leftJoin('house_list', 'house_list.BUILDING_ID', '=', 'building_list.BUILDING_ID')
            ->leftJoin('L_HOUSE_TYPE', 'L_HOUSE_TYPE.HOUSE_TYPE_ID', '=', 'house_list.HOUSE_TYPE_ID')
            ->distinct()->orderBy('L_HOUSE_TYPE.HOUSE_TYPE', 'asc')->get(['L_HOUSE_TYPE.house_type_id', 'L_HOUSE_TYPE.house_type']);

        if ($houseTypeList) {
            $houseTypeListHtml = view('ajax.house-type')->with('house_types', $houseTypeList)->render();
        }
        return $houseTypeListHtml;
    }

    public function houseTypesByBuilding(Request $request, $buildingId)
    {

        $houseTypesHtml = '';
        $floorsHtml = '';
        $housesHtml = '';
        $houseSizeHtml = '';
        $houseFormHtml = '';

        $building = BuildingList::find($buildingId);

        if ($building) {
            $houseTypesHtml = view('ajax.house-types-by-building')->with('building', $building)->render();
            $floorsHtml = view('ajax.floors-by-building')->with('building', $building)->render();
            $housesHtml = view('ajax.houses-by-building')->with('building', $building)->render();
            $houseFormHtml = view('ajax.house-info')->with('building', $building)->render();
        }

        return [
            'houseTypesHtml' => $houseTypesHtml,
            'floorsHtml' => $floorsHtml,
            'housesHtml' => $housesHtml,
            'houseFormHtml' => $houseFormHtml,
            'houseSizeHtml' => $houseSizeHtml,
        ];
    }

    public function typeWiseHouse(Request $request, $typeId)
    {

        $buildings = BuildingList::where('house_type_id', $typeId)->get();


        $buildingdata = '';

        if (!empty($buildings)) {
            $buildingdata .= '<option value="">--- Choose ---</option>';
            foreach ($buildings as $data) {
                $buildingdata .= '<option value="' . $data->building_id . '">' . $data->building_name . '</option>';

            }
            echo $buildingdata;

        } else {
            echo '<option value="">--- Choose ---</option>';
        }
    }

    public function houses(Request $request)
    {
        $searchTerm = $request->get('term');

        return HouseList::where(
            [
                [DB::raw('LOWER(house_name)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
            ]
        )->orderBy('house_id', 'ASC')->get(['house_id', 'house_name']);
    }

    public function empRepApproved(Request $request)
    {
        $searchTerm = $request->get('term');

        return $employees = $this->employeeManager->findEmployeeCodesByRepApproved($searchTerm);
    }

    public function allottedHouses(Request $request)
    {
        $searchTerm = $request->get('term');

        return HouseList::where(
            [
                [DB::raw('LOWER(house_name)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
            ]
        )->where('house_status_id', HouseStatus::ALLOTTED)->orderBy('house_id', 'ASC')->limit(10)->get(['house_id', 'house_name']);
    }

    public function advertisements(Request $request)
    {
        $searchTerm = $request->get('term');

        return HaAdvMst::where(
            [
                [DB::raw('LOWER(adv_number)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
            ]
        )->orderBy('adv_id', 'ASC')->limit(10)->get(['adv_id', 'adv_number']);
    }

    public function advertisementsByDept(Request $request, $dept)
    {
        $advertisementHtml = '';

		if($dept == 679){ // 679 for EFG type ; its a static department value commes from report department dropdown
			$advertisements = HaAdvMst::where('dpt_department_id', null)->get();
		}else{
			$advertisements = HaAdvMst::where('dpt_department_id', $dept)->get();
		}
        if ($advertisements) {
            $advertisementHtml = view('ajax.advertisements')->with('advertisements', $advertisements)->render();
        }

        return $advertisementHtml;
    }

    public function dptAckNoByDept(Request $request, $dept)
    {
        $returnHtml = '';
        $acknowledgementRow = Acknowledgemnt::where('dpt_department_id', $dept)->get();
        if ($acknowledgementRow) {
            $returnHtml = view('ajax.acknowledgement-by-dept')->with('acknowledgementRow', $acknowledgementRow)->render();
        }
        return $returnHtml;
    }

    public function employeesByDept(Request $request, $dept)
    {
        $employeesHtml = '';

        $employees = Employee::where('dpt_department_id', $dept)->get();

        if ($employees) {
            $employeesHtml = view('ajax.employees-by-dept')->with('employees', $employees)->render();
        }

        return $employeesHtml;
    }

    public function newGeneralAllotLetters(Request $request)
    {
        $user = Auth();
        $logUser = $user->user()->emp_id;
        $searchTerm = $request->get('term');

        /** Query is same as AllotmentLetterController->datatableList() action. This should be almost same. Though the query seems complex because of database design. */

        $allotmentLetters = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id', 'allot_letter.allot_letter_date', 'allot_letter.allot_letter_no', 'allot_letter.memo_date', 'allot_letter.memo_no', 'allot_letter.delivery_yn', 'allot_letter.ack_yn', 'pmis.employee.emp_name', 'pmis.employee.emp_code')
            ->join('HA_APPLICATION', 'allot_letter.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'HA_APPLICATION.emp_id')
            ->leftJoin('house_allottment', 'house_allottment.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('house_list', 'house_list.house_id', '=', 'house_allottment.house_id')
            ->leftJoin('user_wise_colony', 'user_wise_colony.COLONY_ID', '=', 'house_list.COLONY_ID')
            ->leftJoin('take_over', 'allot_letter.allot_letter_id', '=','take_over.allot_letter_id')
            ->where(
                [
                    [DB::raw('LOWER(allot_letter.allot_letter_no)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
                ]
            )
            ->where('allot_letter.app_type_id', AppType::GENERAL)
            ->where('allot_letter.delivery_yn', YesNoFlag::YES)
            ->where('allot_letter.ACK_YN', 'N') // change ACK_YN = Y insted of ACK_YN = N  //->where('allot_letter.ACK_YN', 'N')
            ->where('allot_letter.takeover_request_yn', 'Y') // this condition added to show takeover requested data // 29-03-2022 whenever an employee request for takeover then it will be show here
//            ->where('allot_letter.cancel_req_yn', 'N')
            ->whereNull('allot_letter.int_change_id')
            ->whereNull('allot_letter.replace_app_id')
            ->where('user_wise_colony.emp_id', $logUser)
//           ->where('HA_APPLICATION.app_type_id', AppType::GENERAL)
            ->orderBy(DB::raw('LOWER(allot_letter.allot_letter_no)'), 'ASC')
            ->limit(10)
            ->distinct('allot_letter.allot_letter_id')
            ->get(['allot_letter.allot_letter_no']);


        return $allotmentLetters;
    }
    public function takeoverlettercivil(Request $request)
    {
        $user = Auth();
        $logUser = $user->user()->emp_id;
        $searchTerm = $request->get('term');

        /** Query is same as AllotmentLetterController->datatableList() action. This should be almost same. Though the query seems complex because of database design. */

        $allotmentLetters = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id', 'allot_letter.allot_letter_date', 'allot_letter.allot_letter_no', 'allot_letter.memo_date', 'allot_letter.memo_no', 'allot_letter.delivery_yn', 'allot_letter.ack_yn', 'pmis.employee.emp_name', 'pmis.employee.emp_code','user_wise_colony.emp_id')
            ->join('HA_APPLICATION', 'allot_letter.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'HA_APPLICATION.emp_id')
            ->leftJoin('house_allottment', 'house_allottment.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('house_list', 'house_list.house_id', '=', 'house_allottment.house_id')
            ->leftJoin('user_wise_colony', 'user_wise_colony.COLONY_ID', '=', 'house_list.COLONY_ID')
            ->leftJoin('take_over', 'allot_letter.allot_letter_id', '=','take_over.allot_letter_id')
            ->where(
                [
                    [DB::raw('LOWER(allot_letter.allot_letter_no)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
                ]
            )
            ->where('allot_letter.app_type_id', AppType::GENERAL)
//            ->where('allot_letter.delivery_yn', YesNoFlag::YES)
            ->where('allot_letter.ACK_YN', 'N') // change ACK_YN = Y insted of ACK_YN = N  //->where('allot_letter.ACK_YN', 'N')
            ->where('allot_letter.takeover_request_yn', 'Y') // this condition added to show takeover requested data // 29-03-2022 whenever an employee request for takeover then it will be show here
//            ->where('allot_letter.cancel_req_yn', 'N')
            ->whereNull('allot_letter.int_change_id')
            ->whereNull('allot_letter.replace_app_id')
            ->whereNull('take_over.take_over_civil_emp' )
            ->where('user_wise_colony.emp_id', $logUser)
//           ->where('HA_APPLICATION.app_type_id', AppType::GENERAL)
            ->orderBy(DB::raw('LOWER(allot_letter.allot_letter_no)'), 'ASC')
            ->limit(10)
            ->distinct('allot_letter.allot_letter_id')
            ->get(['allot_letter.allot_letter_no']);


        return $allotmentLetters;
    }

    public function takeoverletterelec(Request $request)
    {
        $user = Auth();
        $logUser = $user->user()->emp_id;
        $searchTerm = $request->get('term');

        /** Query is same as AllotmentLetterController->datatableList() action. This should be almost same. Though the query seems complex because of database design. */

        $allotmentLetters = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id', 'allot_letter.allot_letter_date', 'allot_letter.allot_letter_no', 'allot_letter.memo_date', 'allot_letter.memo_no', 'allot_letter.delivery_yn', 'allot_letter.ack_yn', 'pmis.employee.emp_name', 'pmis.employee.emp_code')
            ->join('HA_APPLICATION', 'allot_letter.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'HA_APPLICATION.emp_id')
            ->leftJoin('house_allottment', 'house_allottment.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('house_list', 'house_list.house_id', '=', 'house_allottment.house_id')
            ->leftJoin('user_wise_colony', 'user_wise_colony.COLONY_ID', '=', 'house_list.COLONY_ID')
            ->leftJoin('take_over', 'allot_letter.allot_letter_id', '=','take_over.allot_letter_id')
            ->where(
                [
                    [DB::raw('LOWER(allot_letter.allot_letter_no)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
                ]
            )
            ->where('allot_letter.app_type_id', AppType::GENERAL)
            ->where('allot_letter.delivery_yn', YesNoFlag::YES)
            ->where('allot_letter.ACK_YN', 'N') // change ACK_YN = Y insted of ACK_YN = N  //->where('allot_letter.ACK_YN', 'N')
            ->where('allot_letter.takeover_request_yn', 'Y') // this condition added to show takeover requested data // 29-03-2022 whenever an employee request for takeover then it will be show here
//            ->where('allot_letter.cancel_req_yn', 'N')
            ->whereNull('allot_letter.int_change_id')
            ->whereNull('allot_letter.replace_app_id')
            ->whereNull('take_over.take_over_elec_emp' )
            ->where('user_wise_colony.emp_id', $logUser)
//           ->where('HA_APPLICATION.app_type_id', AppType::GENERAL)
            ->orderBy(DB::raw('LOWER(allot_letter.allot_letter_no)'), 'ASC')
            ->limit(10)
            ->distinct('allot_letter.allot_letter_id')
            ->get(['allot_letter.allot_letter_no']);


        return $allotmentLetters;
    }

    public function newInterchangeAllotLetters(Request $request)
    {

        $searchTerm = $request->get('term');

        /** Query is same as AllotmentLetterInterchangeController->datatableList() action. This should be almost same. Though the query seems complex because of database design. */
        $interchangeAllotLetter = $this->houseInterchangeApplicationManager->newInterChangeAllotmentLetterSearch($searchTerm);

        return $interchangeAllotLetter;
    }

    public function newReplaceAllotLetters(Request $request)
    {

        $user = Auth();
        $logUser = $user->user()->emp_id;

        $searchTerm = $request->get('term');

        /** Query is same as ReplaceAllotmentLetterController->datatableList() action. This should be almost same. Though the query seems complex because of database design. */
        $replaceAllotLetters = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id', 'allot_letter.allot_letter_date', 'allot_letter.allot_letter_no', 'allot_letter.memo_date', 'allot_letter.memo_no', 'allot_letter.delivery_yn', 'allot_letter.ack_yn', 'pmis.employee.emp_name', 'pmis.employee.emp_code')
            ->leftJoin('replacement_application', 'replacement_application.replace_app_id', '=', 'allot_letter.replace_app_id')
            ->join('house_allottment', 'replacement_application.allot_id', '=', 'house_allottment.ALLOT_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
//            ->leftJoin('house_allottment', 'house_allottment.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('house_list', 'house_list.house_id', '=', 'house_allottment.house_id')
            ->leftJoin('take_over', 'take_over.take_over_id', '=', 'house_allottment.take_over_id')
            ->leftJoin('user_wise_colony', 'user_wise_colony.colony_id', '=', 'house_list.colony_id')
//            ->leftJoin('take_over','take_over.allot_letter_id','=','allot_letter.allot_letter_id')
            ->where(
                [
                    [DB::raw('LOWER(allot_letter.allot_letter_no)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
                ]
            )
            ->where('allot_letter.app_type_id', AppType::REPLACEMENT)
            ->where('allot_letter.delivery_yn', YesNoFlag::YES)
            ->where('allot_letter.ACK_YN', 'N') // change ACK_YN = Y insted of ACK_YN = N
               ->whereNull('allot_letter.application_id')
            ->whereNull('allot_letter.int_change_id')
            ->whereNull('take_over.take_over_civil_emp')
            ->where('user_wise_colony.emp_id', $logUser)
//            ->where('take_over.take_over_civil_emp' ,'=', null )
//            ->where('house_allottment.app_type_id', AppType::REPLACEMENT)
            ->orderBy(DB::raw('LOWER(allot_letter.allot_letter_no)'), 'ASC')
            ->distinct('allot_letter.allot_letter_no')
            ->limit(10)
            ->get(['allot_letter.allot_letter_no']);

        return $replaceAllotLetters;
    }
    public function newReplaceAllotElecLetters(Request $request)
    {

        $user = Auth();
        $logUser = $user->user()->emp_id;

        $searchTerm = $request->get('term');

        /** Query is same as ReplaceAllotmentLetterController->datatableList() action. This should be almost same. Though the query seems complex because of database design. */
        $replaceAllotLetters = DB::table('allot_letter')
            ->select('allot_letter.allot_letter_id', 'allot_letter.allot_letter_date', 'allot_letter.allot_letter_no', 'allot_letter.memo_date', 'allot_letter.memo_no', 'allot_letter.delivery_yn', 'allot_letter.ack_yn', 'pmis.employee.emp_name', 'pmis.employee.emp_code')
            ->leftJoin('replacement_application', 'replacement_application.replace_app_id', '=', 'allot_letter.replace_app_id')
            ->join('house_allottment', 'replacement_application.allot_id', '=', 'house_allottment.ALLOT_ID')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'house_allottment.emp_id')
//            ->leftJoin('house_allottment', 'house_allottment.APPLICATION_ID', '=', 'HA_APPLICATION.APPLICATION_ID')
            ->leftJoin('house_list', 'house_list.house_id', '=', 'house_allottment.house_id')
            ->leftJoin('take_over', 'take_over.take_over_id', '=', 'house_allottment.take_over_id')
            ->leftJoin('user_wise_colony', 'user_wise_colony.colony_id', '=', 'house_list.colony_id')
//            ->leftJoin('take_over','take_over.allot_letter_id','=','allot_letter.allot_letter_id')
            ->where(
                [
                    [DB::raw('LOWER(allot_letter.allot_letter_no)'), 'like', strtolower('%' . trim($searchTerm) . '%')],
                ]
            )
            ->where('allot_letter.app_type_id', AppType::REPLACEMENT)
            ->where('allot_letter.delivery_yn', YesNoFlag::YES)
            ->where('allot_letter.ACK_YN', 'N') // change ACK_YN = Y insted of ACK_YN = N
               ->whereNull('allot_letter.application_id')
            ->whereNull('allot_letter.int_change_id')
            ->whereNull('take_over.take_over_elec_emp')
            ->where('user_wise_colony.emp_id', $logUser)
//            ->where('take_over.take_over_civil_emp' ,'=', null )
//            ->where('house_allottment.app_type_id', AppType::REPLACEMENT)
            ->orderBy(DB::raw('LOWER(allot_letter.allot_letter_no)'), 'ASC')
            ->distinct('allot_letter.allot_letter_no')
            ->limit(10)
            ->get(['allot_letter.allot_letter_no']);

        return $replaceAllotLetters;
    }

    public function electricalEngineers(Request $request)
    {
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findDepartmentalEmployees(Department::ELECTRICAL_ENGINEER, $searchTerm);

        return $employees;
    }

    public function civilEngineers(Request $request)
    {
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findDepartmentalEmployees(Department::CIVIL_ENGINEER, $searchTerm);

        return $employees;
    }

    public function employeesWithDept(Request $request, $empDept = null)
    {
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findDeptWiseEmployeeCodesBy($searchTerm, $empDept);
        return $employees;
    }

    public function loadEmployeeFamilyDetails(Request $request, $employee_code = null)
    {
        $familyDetails = EmpFamily::select('*')
            ->leftJoin('pmis.l_relation_type', 'pmis.l_relation_type.relation_type_id', '=', 'pmis.emp_family.relation_type_id')
            ->leftJoin('pmis.employee', 'pmis.employee.emp_id', '=', 'pmis.emp_family.emp_id')
            ->where('emp_code', '=', $employee_code)
            ->get();

        $familyDetailsHtml = view('ajax.employee_family_detials')->with('familyDetails', $familyDetails)->render();
        //return $familyDetailsHtml;
        return response()->json(array('familyDetailsHtml' => $familyDetailsHtml));
    }

    public function houseDetailsByDpt($dptId)
    {
//        $houses = HouseList::where('dept_ack_id', $ackId)->where('house_status_id', HouseStatus::AVAILABLE)->with('colonylist', 'buildinglist')->get();
        $houses = HouseList::where('dpt_department_id', $dptId)->whereNotNull('dept_ack_id')->where('house_status_id', HouseStatus::AVAILABLE)->with('colonylist', 'buildinglist')->get();
        return $houses;
    }

    public function ackDetailsByDpt($dptId)
    {
        $acks = Acknowledgemnt::where('dpt_department_id', $dptId)->where('active_yn', 'Y')->where('transferred_yn', 'Y')->get();
        return $acks;
    }

    public function adDept($house_type)
    {
        $emp_dept = Employee::where('emp_id', Auth::user()->emp_id)->pluck('dpt_department_id')->first();

        if (Employee::where('emp_id', Auth::user()->emp_id)->pluck('designation_id')->first() == 30) { //Civil admin
            if ($house_type == 'abcd') {
                $departments = DB::select('select department_id, department_name from pmis.l_department where department_id = ' . $emp_dept);
            } else {
                $departments = DB::select('select department_id, department_name from pmis . l_department');
            }
        } else {
            $departments = DB::select('select department_id, department_name from pmis . l_department where department_id = ' . $emp_dept);
        }
        return $departments;
    }

    public function empDetailsByCode($emp_code)
    {
        $query = <<<QUERY
SELECT d.EMP_CODE,
       d.EMP_ID,
       d.EMP_NAME,
       des.DESIGNATION,
       dpt.DEPARTMENT_NAME,
       bld.BUILDING_NAME,
       c.HOUSE_ID,
       c.HOUSE_NAME,
       c.dormitory_yn,
       c.FLOOR_NUMBER,
       c.house_size,
       col.COLONY_NAME
  FROM HAS.HOUSE_ALLOTTMENT  b,
       HAS.HOUSE_LIST        c,
       HAS.BUILDING_LIST     bld,
       HAS.L_COLONY          col,
       PMIS.EMPLOYEE         d,
       PMIS.L_DEPARTMENT     dpt,
       PMIS.L_DESIGNATION    des
 WHERE     b.HOUSE_ID = c.HOUSE_ID
       AND c.BUILDING_ID = bld.BUILDING_ID
       AND c.COLONY_ID = col.COLONY_ID
       AND b.EMP_ID = d.EMP_ID
       AND d.DPT_DEPARTMENT_ID = dpt.DEPARTMENT_ID
       AND d.DESIGNATION_ID = des.DESIGNATION_ID
       AND d.EMP_CODE = '$emp_code'
QUERY;

        return DB::select($query);
    }


    public function firstEmpForInterchangeReq(Request $request)
    {
//        dd('sss');
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findInterchangeFirstEmp($searchTerm);

        return $employees;
    }


    public function secondEmpForInterchangeReq(Request $request)
    {
//        dd('sss');
        $searchTerm = $request->get('term');
        $employees = $this->employeeManager->findInterchangesecondEmp($searchTerm);

        return $employees;
    }

    public function firstRequestEmp($empId)
    {

        $query = <<<Query
SELECT E.EMP_ID,
       E.EMP_CODE,
       E.EMP_NAME,
       D.DEPARTMENT_NAME,
       DES.DESIGNATION,
       S.DPT_SECTION AS SECTION,
       HL.HOUSE_NAME,
     H.ALLOT_ID
  FROM PMIS.EMPLOYEE E
       LEFT JOIN PMIS.L_DEPARTMENT D ON D.DEPARTMENT_ID = E.DPT_DEPARTMENT_ID
       LEFT JOIN PMIS.L_DESIGNATION DES
          ON DES.DESIGNATION_ID = E.DESIGNATION_ID
       LEFT JOIN PMIS.L_DPT_SECTION S ON S.DPT_SECTION_ID = E.SECTION_ID
       LEFT JOIN HOUSE_ALLOTTMENT H ON H.EMP_ID = E.EMP_ID
       LEFT JOIN INTERCHANGE_REQUEST I ON I.REQ_FROM_ALLOT_ID = H.ALLOT_ID
       LEFT JOIN HOUSE_LIST HL ON HL.HOUSE_ID = H.HOUSE_ID
 WHERE E.EMP_ID ='$empId'
Query;

        $data = DB::selectOne($query);

        return response()->json($data);

    }

    public function secondRequestEmp($empId)
    {

        $query = <<<Query
SELECT E.EMP_ID,
       E.EMP_CODE,
       E.EMP_NAME,
       D.DEPARTMENT_NAME,
       DES.DESIGNATION,
       S.DPT_SECTION AS SECTION,
       HL.HOUSE_NAME,
     H.ALLOT_ID
  FROM PMIS.EMPLOYEE E
       LEFT JOIN PMIS.L_DEPARTMENT D ON D.DEPARTMENT_ID = E.DPT_DEPARTMENT_ID
       LEFT JOIN PMIS.L_DESIGNATION DES
          ON DES.DESIGNATION_ID = E.DESIGNATION_ID
       LEFT JOIN PMIS.L_DPT_SECTION S ON S.DPT_SECTION_ID = E.SECTION_ID
       LEFT JOIN HOUSE_ALLOTTMENT H ON H.EMP_ID = E.EMP_ID
       LEFT JOIN INTERCHANGE_REQUEST I ON I.REQ_FROM_ALLOT_ID = H.ALLOT_ID
       LEFT JOIN HOUSE_LIST HL ON HL.HOUSE_ID = H.HOUSE_ID
 WHERE E.EMP_ID ='$empId'
Query;

        $data = DB::selectOne($query);

        return response()->json($data);

    }

    public function allotedEmployee(Request $request)
    {
        $user = Auth::user();
        $logUser = DB::selectOne("SELECT P.EMP_ID,U.EMP_ID,P.DPT_DEPARTMENT_ID as DEPARTMENT_ID
  FROM CPA_SECURITY.SEC_USERS U, PMIS.EMPLOYEE p
  where U.EMP_ID = P.EMP_ID
  and U.EMP_ID = '$user->emp_id' ");
        $department = $logUser->department_id;

        $searchTerm = $request->get('term');
        if($user->user_name == 'admin'|| $department == 5){

            $employees = $this->employeeManager->allotedAllEmployee($searchTerm);
        }else {
            $employees = $this->employeeManager->allotedDepartmentEmployee($searchTerm, $department);
        }
        return $employees;
    }

    public function colonywiseassigntype(Request $request, $colonyId)
    {


        $house_type = DB::select("SELECT DISTINCT Hl.HOUSE_TYPE_ID, Hl.HOUSE_TYPE
  FROM HAS.HOUSE_LIST h, HAS.BUILDING_LIST b, HAS.L_HOUSE_TYPE hl
 WHERE     H.colony_id = $colonyId
       AND H.BUILDING_ID = B.BUILDING_ID
       AND HL.HOUSE_TYPE_ID = B.HOUSE_TYPE_ID ");


        $housetypedata = '';

        if (!empty($house_type)) {
            $housetypedata .= '<option value="">--- Choose ---</option>';
            foreach ($house_type as $data) {
                $housetypedata .= '<option value="' . $data->house_type_id . '">' . $data->house_type . '</option>';

            }
            echo $housetypedata;

        } else {
            echo '<option value="">--- Choose ---</option>';
        }
    }
    public function housetypebyadvertisement(Request $request, $advId)
    {


        $house_type = DB::select("SELECT
       D.HOUSE_TYPE_ID,
       HT.HOUSE_TYPE
  FROM HA_ADV_DTL DL
       JOIN house_list D ON (DL.house_id = D.house_id)
       JOIN l_house_type ht ON (ht.house_type_id = D.house_type_id)
 WHERE DL.ADV_ID = $advId
 group by(D.HOUSE_TYPE_ID, HT.HOUSE_TYPE)");


        $housetypedata = '';

        if (!empty($house_type)) {
            $housetypedata .= '<option value="">--- Choose ---</option>';
            foreach ($house_type as $data) {
                $housetypedata .= '<option value="' . $data->house_type_id . '">' . $data->house_type . '</option>';

            }
            echo $housetypedata;

        } else {
            echo '<option value="">--- Choose ---</option>';
        }
    }

}
