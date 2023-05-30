<?php

namespace App\Http\Controllers\has;

use App\Entities\HouseAllotment\LFlatName;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FlatNameEntryController extends Controller
{
    use HasPermission;

    public function index()
    {
        return view('flatEntry.index', [
            'flatName' => null,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $flatNameData = LFlatName::find($id);

        return view('flatEntry.index', [
            'flatName' => $flatNameData,
        ]);
    }

    public function dataTableList()
    {
        $queryResult = LFlatName::all();

        return datatables()->of($queryResult)
            ->addColumn('action', function($query) {
                return '<a href="'. route('flat-name-entry.flat-name-entry-edit', [$query->flat_name_id]) .'"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function post(Request $request) {
        $response = $this->flat_name_entry_api_ins($request);

        $message = $response['o_status_message'];

        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('flat-name-entry.flat-name-entry-index');
    }

    public function update(Request $request, $id) {
        $response = $this->flat_name_entry_api_upd($request, $id);

        $message = $response['o_status_message'];
        if($response['o_status_code'] != 1) {
            session()->flash('m-class', 'alert-danger');
            return redirect()->back()->with('message', 'error|'.$message);
        }

        session()->flash('m-class', 'alert-success');
        session()->flash('message', $message);

        return redirect()->route('flat-name-entry.flat-name-entry-index');
    }

//    public function delete(Request $request, $id) {
//        $response = $this->flat_name_entry_api_delete($request, $id);
//
//        $message = $response['o_status_message'];
//        if($response['o_status_code'] != 1) {
//            session()->flash('m-class', 'alert-danger');
//            return redirect()->back()->with('message', 'error|'.$message);
//        }
//
//        session()->flash('m-class', 'alert-success');
//        session()->flash('message', $message);
//
//        return redirect()->route('tour-type-entry.tour-type-entry-index');
//    }

//    public function tour_type_entry_api_delete($request, $id)
//    {
//        $deleteTourType = LTourTypes::find($id);
//        $deleteTourType->delete();
//
//        if($deleteTourType)
//        {
//            $params = [
//                'o_status_code' => 1,
//                'o_status_message' => 'SUCCESSFULLY DELETED RECORD',
//            ];
//        }
//        else
//        {
//            $params = [
//                'o_status_code' => 99,
//                'o_status_message' => 'PROBLEM OCCURRED',
//            ];
//        }
//
//        return $params;
//    }

    private function flat_name_entry_api_ins(Request $request)
    {
        $postData = $request->post();

        try {
            $flat_id = null;
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_FLAT_NAME_ID' => [
                    'value' => &$flat_id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_FLAT_NAME' => $postData['flat_name'],
                'P_DESCRIPTION' => $postData['description'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('HAS.ALLOTMENT.L_FLAT_NAME_IU', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }

    private function flat_name_entry_api_upd($request, $id)
    {
        $postData = $request->post();

        try {
            $status_code = sprintf("%4000s","");
            $status_message = sprintf("%4000s","");

            $params = [
                'P_FLAT_NAME_ID' => [
                    'value' => &$id,
                    'type' => \PDO::PARAM_INPUT_OUTPUT,
                    'length' => 255
                ],
                'P_FLAT_NAME' => $postData['flat_name'],
                'P_DESCRIPTION' => $postData['description'],
                'P_INSERT_BY' => auth()->id(),
                'o_status_code' => &$status_code,
                'o_status_message' => &$status_message,
            ];
            DB::executeProcedure('HAS.ALLOTMENT.L_FLAT_NAME_IU', $params);
        }
        catch (\Exception $e) {
            return ["exception" => true, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
        }

        return $params;
    }
}
