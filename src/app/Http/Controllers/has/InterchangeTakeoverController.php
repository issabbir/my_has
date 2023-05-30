<?php

namespace App\Http\Controllers\has;

use App\entities\houseallotment\LTakeOver;
use App\Entities\Pmis\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Managers\FlashMessageManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Security\HasPermission;

class InterchangeTakeoverController extends Controller
{
    use HasPermission;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    public function index()
    {
        return view('interchangetakeover.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $this->takeover($request);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('interchange-takeover.index')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function takeover(Request $request)
    {
        $params = $request->post();

        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            $params = [
                'p_ALLOT_LETTER_ID' => $params['allot_letter_id'],
                'p_TAKE_OVER_DATE' => date('Y-m-d', strtotime($params['take_over_date'])),
                'p_INSERT_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];

            DB::executeProcedure('int_change_take_over', $params);
            return $params;
        } catch (\Exception $e) {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
            //return [  "exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
        }
    }

    public function civilIndex()
    {

        $loggedUser = auth()->user();

        return view('interchangetakeover.civilIndex', compact('loggedUser'));

    }


    public function civilStore(Request $request)
    {
        $params = $request->post();


        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            DB::beginTransaction();
            $params = [
                'p_ALLOT_LETTER_ID' => $params['allot_letter_id'],
                'p_TAKE_OVER_DATE' => date('Y-m-d', strtotime($params['take_over_date'])),
//                'p_FIRST_HOUSE_DETAILS' => $params['first_house_details'],
//                'p_FIRST_SANITARY_FITTINGS' => $params['first_sanitary_fittings'],
//                'p_SECOND_HOUSE_DETAILS' => $params['second_house_details'],
//                'p_SECOND_SANITARY_FITTINGS' => $params['second_sanitary_fittings'],

                'p_TAKE_OVER_CIVIL_EMP' => $params['civilEng'],
                'p_CIVIL_ENG_COMMENT' => '',
                'p_INSERT_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];
//            dd($params);
            DB::executeProcedure('HAS.int_change_take_over_civil', $params);
            dd($params);
            DB::commit();
            return $params;
        } catch (\Exception $e) {
            DB::rollback();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];

        }
    }

    public function elecIndex()
    {

        $loggedUser = auth()->user();

        return view('interchangetakeover.elecIndex', compact('loggedUser'));

    }

    public function elecStore(Request $request)
    {
        $params = $request->post();

        try {
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            DB::beginTransaction();
            $params = [
                'p_ALLOT_LETTER_ID' => $params['allot_letter_id'],
                'p_TAKE_OVER_DATE' => date('Y-m-d', strtotime($params['take_over_date'])),
                'p_FIRST_HOUSE_DETAILS' => $params['first_house_details'],
                'P_FIRST_ELECTRICAL_FITTINGS' => $params['first_electrical_fittings'],
                'p_SECOND_HOUSE_DETAILS' => $params['second_house_details'],
                'p_SECOND_ELECTRICAL_FITTINGS' => $params['second_electrical_fittings'],
                'p_INT_TAKE_OVER_BY' => $params['elecEng'],
                'p_INSERT_BY' => auth()->id(),
                'o_status_code' => &$statusCode,
                'o_status_message' => &$statusMessage
            ];
            dd($params);
            DB::executeProcedure('int_change_elec_take_over', $params);

            DB::commit();
            return $params;
        } catch (\Exception $e) {
            DB::rollback();
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];

        }
    }


}
