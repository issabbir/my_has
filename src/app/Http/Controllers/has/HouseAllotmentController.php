<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 2/6/20
 * Time: 5:33 PM
 */

namespace App\Http\Controllers\has;

use App\Entities\Admin\LDepartment;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HouseAllotment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;

use App\Entities\HouseAllotment\Acknowledgemnt;
use Illuminate\Database\Eloquent\Model;


class HouseAllotmentController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    public function __construct(FlashMessageManager $flashMessageManager)
    {
        $this->flashMessageManager = $flashMessageManager;
    }

    public function index(Request $request)
    {
        return view('houseallotment.index');
    }

    public function depAllotment(Request $request)
    {
        $department = LDepartment::get();
        return view('houseallotment.depallotment', compact('department'));
    }


    public function store(Request $request, $id = null)

    {

        $params = [];
        DB::beginTransaction();
        try {
            $p_ack_id = $id ? $id : '';
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');
            $dormitory_yn = $request->get("dormitory_yn");
            $ack_bn = $request->get("acknoledgement_no_bn");
            $req_flat = $request->get("req_flat");
            $valid_from = $request->get("valid_from");
            $valid_to = $request->get("valid_to");
            $depAttachedFile = $request->file('depAttachedFile');

            if (!isset($depAttachedFile)) {
                $depattachedFileName = '';
                $depattachedFileType = '';
                $depattachedFileContent = '';
            } elseif (isset($depAttachedFile)) {
                $depattachedFileName = $depAttachedFile->getClientOriginalName();
                $depattachedFileType = $depAttachedFile->getMimeType();
                $depattachedFileContent = base64_encode(file_get_contents($depAttachedFile->getRealPath()));
            } else {
                if ($p_ack_id) { // only at file update time when attachment not selected newly, wanted to re-allocate previously inserted
                    $depattachedFileData = Acknowledgemnt::where('DEPT_ACK_ID', '=', $p_ack_id)->first();
                    $depattachedFileName = $depattachedFileData->dept_ack_doc_name;
                    $depattachedFileType = $depattachedFileData->dept_ack_doc_type;
                    $depattachedFileContent = $depattachedFileData->dept_ack_doc;
                }
            }

            $allotAttachedFile = $request->file('allotAttachedFile');
            if (!isset($allotAttachedFile)) {
                $allotattachedFileName = '';
                $allotattachedFileType = '';
                $allotattachedFileContent = '';
            } elseif (isset($allotAttachedFile)) {
                $allotattachedFileName = $allotAttachedFile->getClientOriginalName();
                $allotattachedFileType = $allotAttachedFile->getMimeType();
                $allotattachedFileContent = base64_encode(file_get_contents($allotAttachedFile->getRealPath()));
            } else {
                if ($p_ack_id) { // only at file update time when attachment not selected newly, wanted to re-allocate previously inserted
                    $allotattachedFileData = Acknowledgemnt::where('DEPT_ACK_ID', '=', $p_ack_id)->first();
                    $allotattachedFileName = $allotattachedFileData->accept_doc_name;
                    $allotattachedFileType = $allotattachedFileData->accept_doc_type;
                    $allotattachedFileContent = $allotattachedFileData->accept_doc;
                }
            }


            $params = [
                "p_DEPT_ACK_ID" => [
                    "value" => &$p_ack_id,
                    "type" => \PDO::PARAM_INPUT_OUTPUT,
                    "length" => 255
                ],

                "p_DEPT_ACK_NO" => $request->get("acknoledgement_no"),
                "p_DEPT_ACK_NO_BN" => isset($ack_bn) ? $ack_bn : '',
//                    "p_DEPT_ACK_DATE" => date('Y-m-d', strtotime($request->get("dep_ack_date"))),
                "p_DEPT_ACK_DATE" => $request->get("dep_ack_date"),
                "p_DPT_DEPARTMENT_ID" => $request->get("dept_id"),
                "p_NO_OF_REQ_FLAT" => isset($req_flat) ? $req_flat : '',
                "p_NO_OF_ALLOTED_FLAT" => $request->get("alloted_flat"),
                "p_DEPT_REQ_VALID_FROM" => isset($valid_from) ? $valid_from : '',
                "p_DEPT_REQ_VALID_TO" => isset($valid_to) ? $valid_to : '',
                "p_DEPT_REQ_ACTIVE_YN" => 'Y',

                "p_DEPT_ACK_DOC_NAME" => $depattachedFileName,
                "p_DEPT_ACK_DOC_TYPE" => $depattachedFileType,
//                    "p_DEPT_ACK_DOC" => $depattachedFileContent,
                "p_DEPT_ACK_DOC" => [
                    'value' => $depattachedFileContent,
//                        'type'  => \PDO::PARAM_LOB,
                    'type' => SQLT_CLOB,
                ],

                "p_ACCEPT_DOC_NAME" => $allotattachedFileName,
                "p_ACCEPT_DOC_TYPE" => $allotattachedFileType,
//                    "p_ACCEPT_DOC" => $allotattachedFileContent,
                "p_ACCEPT_DOC" => [
                    'value' => $allotattachedFileContent,
//                        'type'  => \PDO::PARAM_LOB,
                    'type' => SQLT_CLOB,
                ],

                "p_ACTIVE_YN" => ($request->get("active_yn")) ? $request->get("active_yn") : 'Y',
                "p_DORMITORY_YN" => $dormitory_yn,
                "p_insert_by" => Auth()->ID(),
                "o_status_code" => &$statusCode,
                "o_status_message" => &$statusMessage

            ];


            DB::executeProcedure("HAS.DEPT_ACKNOWLEDGEMENT_ENTRY", $params);

            if ($params['o_status_code'] != 1) {
                $flashMessageContent = $this->flashMessageManager->getMessage($params);
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
                DB::rollBack();

                return $params;
            }

            DB::commit();
            if ($id) {
//                    return ["exception" => false, "o_status_code" => true, "o_status_message" => 'Update Successful'];
                $flashMessageContent = $this->flashMessageManager->getMessage($params);
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
            } else {

                $flashMessageContent = $this->flashMessageManager->getMessage($params);
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
                //return $params;
            }

        } catch
        (\Exception $e) {
//                return ["exception" => true, "o_status_code" => false, "o_status_message" => $e->getMessage()];
            $flashMessageContent = $this->flashMessageManager->getMessage($params);
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
        }


    }


    public function edit(Request $request, $id)
    {

        $department = LDepartment::get();
        $data = Acknowledgemnt::where('DEPT_ACK_ID', '=', $id)->get();
//        dd($data);
        return view('houseallotment.depallotment', compact('data', 'department'));
    }

    public function update(Request $request, $id)
    {//dd($request);
        $params = $this->store($request, $id);

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->route('house-allotment.depAllotment')->with($flashMessageContent['class'], $flashMessageContent['message']);
    }


    public function datatableList(Request $request)
    {
        $data = DB::table('dept_acknowledgement')
            ->select('dept_acknowledgement.dept_ack_id', 'dept_acknowledgement.dept_ack_no', 'dept_acknowledgement.department_name', 'dept_acknowledgement.no_of_req_flat', 'dept_acknowledgement.no_of_alloted_flat', 'dept_acknowledgement.dept_req_valid_from', 'dept_acknowledgement.dept_req_valid_to', 'l_dept_ack_status.ack_status', 'dept_acknowledgement.dept_req_active_yn', 'dept_acknowledgement.ack_status_id')
            ->join('l_dept_ack_status', 'l_dept_ack_status.ack_status_id', '=', 'dept_acknowledgement.ack_status_id')
            ->where('dept_acknowledgement.OLD_ACK_YN', '=', 'N')
            ->where('dept_acknowledgement.TRANSFERRED_YN', '=', 'N')
            ->orderBy('dept_acknowledgement.insert_date', 'DESC')
            ->get();

        return datatables()->of($data)
            ->addColumn('action', function ($query) {
                return '<a href="' . route('house-allotment.depAllotmentEdit', $query->dept_ack_id) . '"><i class="bx bx-edit cursor-pointer"></i></a>';
            })
            ->addColumn('department_name', function ($query) {
                if ($query->department_name == '') {
                    return "All Department";
                } else {
                    return $query->department_name;
                }
            })
            ->addColumn('dept_req_active_yn', function ($query) {
                if ($query->dept_req_active_yn == 'Y') {
                    return '<span class="badge bg-success sts-btn-s">Active</span>';
                } else {
                    return '<span class="badge bg-danger sts-btn-s">Expired</span>';
                }
            })
            ->addColumn('ack_status', function ($query) {
                if ($query->ack_status_id == 1) {
                    return '<span class="badge bg-dark sts-btn">' . $query->ack_status . '</span>';
                } else if ($query->ack_status_id == 2) {
                    return '<span class="badge bg-instagram sts-btn">' . $query->ack_status . '</span>';
                } else if ($query->ack_status_id == 3) {
                    return '<span class="badge bg-info sts-btn">' . $query->ack_status . '</span>';
                } else {
                    return '<span class="badge bg-dark sts-btn">' . $query->ack_status . '</span>';
                }
            })
            ->addColumn('dept_req_valid_from', function ($query) {
                if ($query->dept_req_valid_from) {
                    return date('d-m-Y', strtotime($query->dept_req_valid_from));
                } else {
                    return null;
                }
            })
            ->addColumn('dept_req_valid_to', function ($query) {
                if ($query->dept_req_valid_to) {
                    return date('d-m-Y', strtotime($query->dept_req_valid_to));
                } else {
                    return null;
                }
            })
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }
}
