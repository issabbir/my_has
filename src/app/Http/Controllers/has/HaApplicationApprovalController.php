<?php

namespace App\Http\Controllers\has;

use App\Contracts\AdvertisementContract;
use App\Contracts\Pmis\Employee\EmployeeContract;
use App\Entities\Admin\LDesignation;
use App\Entities\Admin\LGender;
use App\Entities\Admin\LGeoDistrict;
use App\Entities\Admin\LGeoDivision;
use App\Entities\Admin\LGeoThana;
use App\Entities\Admin\LMaritalStatus;
use App\Entities\Admin\LRelationType;
use App\Entities\HouseAllotment\BuildingList;
use App\Entities\HouseAllotment\HaAdvMst;
use App\Entities\HouseAllotment\HaAppEmpFamily;
use App\Entities\HouseAllotment\HaApplication;
use App\Entities\HouseAllotment\HouseList;
use App\Entities\HouseAllotment\LHouseStatus;
use App\Entities\HouseAllotment\LHouseType;
use App\Entities\Pmis\Employee\Employee;
use App\Entities\Pmis\WorkFlowProcess;
use App\Entities\Pmis\WorkFlowStep;
use App\Enums\YesNoFlag;
use App\Http\Controllers\Controller;
use App\Mail\HouseApprove;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Yajra\Pdo\Oci8\Exceptions\Oci8Exception;
use App\Managers\FlashMessageManager;
use Datatables;
use App\Traits\Security\HasPermission;
use Carbon\Carbon;

class HaApplicationApprovalController extends Controller
{
    use HasPermission;

    private $flashMessageManager;

    private $employeeManager;

    private $advertisementManager;

    public function __construct(FlashMessageManager $flashMessageManager, EmployeeContract $employeeManager, AdvertisementContract $advertisementManager)
    {
        $this->flashMessageManager = $flashMessageManager;
        $this->employeeManager = $employeeManager;
        $this->advertisementManager = $advertisementManager;
    }

    public function store(Request $request, $applicationId)
    {
        $params = $this->allotHouseToApplicant($request, $applicationId);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    public function unAssign(Request $request, $applicationId)
    {
        $params = $this->unAssignHouseToApplicant($request, $applicationId);

        /*if(isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }*/

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    public function deny(Request $request, $applicationId)
    {
        $params = $this->denyApplicant($request, $applicationId);

        if (isset($params['exception']) && ($params['exception'] == true)) {
            return $params;
        }

        $flashMessageContent = $this->flashMessageManager->getMessage($params);
        if ($params['o_status_code'] != 1) {
            return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
        }

        //send notification
        $applicant = DB::table('cpa_security.sec_users su')
            ->select('user_id')
            ->leftJoin('has.ha_application ha', 'su.emp_id', '=', 'ha.emp_id')
            ->where('ha.application_id', $applicationId)
            ->first();
        if ($applicant) {
            $notification_msg = 'Your house application has been denied! Please contact authority for more information.';
            $controller_user_notification = [
                "p_notification_to" => $applicant->user_id,
                "p_insert_by" => Auth::id(),
                "p_note" => $notification_msg,
                "p_priority" => null,
                "p_module_id" => 14,
                "p_target_url" => '/ha-applications'
            ];
            try {
                DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
            } catch (\Exception $e) {
                DB::rollback();
                $error = ["exception" => true, "status" => false, "o_status_code" => 99, "o_status_message" => $e->getMessage()];
                $flashMessageContent = $this->flashMessageManager->getMessage($error);
                return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message'])->withInput();
            }
        }

        return redirect()->back()->with($flashMessageContent['class'], $flashMessageContent['message']);
    }

    private function allotHouseToApplicant(Request $request, $applicationId)
    {
        $houseAllotmentApproval = $request->post();
        $application = HaApplication::find($applicationId);

        $employeeInfo = $this->employeeManager->findEmployeeInformation($application->emp_code);

        //TODO: special_consider_file
        $attachedFile = $attachedFileName = $attachedFileType = $attachedFileContent = '';
        $attachedFile = $request->file('special_consider_file');
        $attachedFileName = $application->emp_code . '-' . date('y-m-d');

        if (isset($attachedFile)) {
            $attachedFileName = date('y-m-d') . '-' . $application->emp_code;//$attachedFile->getClientOriginalName();
            $attachedFileType = $attachedFile->getMimeType();
            $attachedFileContent = base64_encode(file_get_contents($attachedFile->getRealPath()));
        } else {
            if ($applicationId) { // only at file update time when attachment not selected newly, wanted to re-allocate previously inserted
                $query = "Select special_consider_file_name,special_consider_file_type,special_consider_file from HOUSE_ALLOTTMENT Where APPLICATION_ID = " . $applicationId;
                $entityList = DB::select($query);
                if ($entityList) {
                    $attachedFileData = $entityList[0];
                    $attachedFileName = $attachedFileData->special_consider_file_name;
                    $attachedFileType = $attachedFileData->special_consider_file_type;
                    $attachedFileContent = $attachedFileData->special_consider_file;
                } else {
                    $attachedFileName = '';
                    $attachedFileType = '';
                    $attachedFileContent = '';
                }

            } else {
                $attachedFileName = '';
                $attachedFileType = '';
                $attachedFileContent = '';
            }
        }

        if ($application) {
            DB::beginTransaction();
            //Approval Process
            $statusCode = sprintf("%4000s", "");
            $statusMessage = sprintf('%4000s', '');

            $houseId = isset($houseAllotmentApproval['house_id']) ? $houseAllotmentApproval['house_id'] : $houseAllotmentApproval['available_house'];

            try {
                $params = [
                    'p_ADV_ID' => $application->advertisement_id,
                    'p_APPLICATION_ID' => $applicationId,
                    'p_HOUSE_ID' => $houseId,
                    'p_MERIT_POINT' => null, // Procedure has this parameter. But no use. PL/SQL developer suggested to keep it as it is. For future use.
                    'p_EXTRA_POINT' => null, // Procedure has this parameter. But no use. PL/SQL developer suggested to keep it as it is. For future use.
                    'p_SPECIAL_CONSIDER_YN' => isset($houseAllotmentApproval['special_consideration_yn']) ? $houseAllotmentApproval['special_consideration_yn'] : YesNoFlag::NO,
                    'p_SPECIAL_REMARKS' => $houseAllotmentApproval['remarks'],
                    'p_BOARD_DECISION_NUMBER' => $houseAllotmentApproval['board_decision_number'],
                    'p_SPECIAL_CONSIDER_FILE' => [
                        'value' => $attachedFileContent,
                        'type' => SQLT_CLOB,
                    ],
                    'p_SPECIAL_CONSIDER_FILE_NAME' => $attachedFileName,
                    'p_SPECIAL_CONSIDER_FILE_TYPE' => $attachedFileType,
                    'p_APPROVE_BY' => auth()->id(),
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];

                DB::executeProcedure('has.ha_process_approve', $params);

            } catch (\Exception $e) {
                DB::rollBack();
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
            }

            //Generates Letter
//                if($params['o_status_code'] == 1)
//                {
//                    $p_id = '';
//                    $statusCode = sprintf("%4000s", "");
//                    $statusMessage = sprintf('%4000s', '');
//                    try {
//                        $params_letter = [
//                            "p_ALLOT_LETTER_ID" => [
//                                "value" => &$p_id,
//                                "type" => \PDO::PARAM_INPUT_OUTPUT,
//                                "length" => 255
//                            ],
//                            "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime(Carbon::now())),
//                            "p_ALLOT_LETTER_NO"   => $request->get("board_decision_number"),
//                            "p_APPLICATION_ID"    => $applicationId,
//                            "p_HOUSE_ADV_ID"      => $application->advertisement_id,
//                            "p_DELIVERY_YN"       => 'N',
//                            "p_DELIVERY_DATE"     => '',
//                            "p_DELIVERED_BY"      => '',
//                            //"p_RECEIVED_BY"       => '',
//                            "p_MEMO_NO"           => '',
//                            "p_MEMO_DATE"         => date("Y-m-d", strtotime(Carbon::now())),
//                            //"p_REMARKS"           => $request->get('remarks'),
//                            "p_insert_by"         => Auth()->ID(),
//                            "o_status_code"         => &$statusCode,
//                            "o_status_message"      => &$statusMessage
//                        ];
//
//                        DB::executeProcedure('allotment.allot_letter_entry', $params_letter);
//
//
//
//                    } catch (\Exception $e) {
//                        DB::rollBack();
//                        return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
//                        //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
//                    }
//                    if($params_letter['o_status_code'] == 1)//Trying to get property 'emp_id' of non-object
//                    {
//                        //Sends Notification
//
//                        //To Applicant
//                        $houseInfo = HouseList::where('house_id', $houseId)->with('colonylist', 'buildinglist')->first();
//                        $name = isset($houseInfo->house_code) ? $houseInfo->house_name . ' ('. $houseInfo->house_code .')' : $houseInfo->house_name;
//                        $notification_msg = 'Congratulations! A flat has been allotted to you. Colony: '. $houseInfo->colonylist->colony_name .', Building: '. $houseInfo->buildinglist->building_name .', Flat: '. $name ;
//                        $coUserId = DB::table('cpa_security.sec_users')->where('emp_id', $employeeInfo['emp_id'])->pluck('user_id')->first();
//                        if($coUserId) {
//                            try {
//                            $controller_user_notification = [
//                                "p_notification_to" => $coUserId,
//                                "p_insert_by" => Auth::id(),
//                                "p_note" => $notification_msg,
//                                "p_priority" => null,
//                                "p_module_id" => 14,
//                                "p_target_url" => url('/take-over-application')
//                            ];
//
//                            DB::executeProcedure("cpa_security.cpa_general.notify_add", $controller_user_notification);
//                            } catch (\Exception $e) {
//                                DB::rollBack();
//                                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
//                                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
//                            }
//                        }
//
//                        if ($employeeInfo['emp_email'])
//                        {
//                            //Sends Email
//                            //Mail::to($employeeInfo['emp_email'])->send(new HouseApprove($employeeInfo['emp_name'], $employeeInfo['emp_code']));
//                            try {
//                                //Updates Letter Delivery
//                                $p_id = $params_letter['p_ALLOT_LETTER_ID']['value'];
//                                $statusCode = sprintf("%4000s", "");
//                                $statusMessage = sprintf('%4000s', '');
//                                $params_upd_letter = [
//                                    "p_ALLOT_LETTER_ID" => [
//                                        "value" => &$p_id,
//                                        "type" => \PDO::PARAM_INPUT_OUTPUT,
//                                        "length" => 255
//                                    ],
//                                    "p_ALLOT_LETTER_DATE" => date("Y-m-d", strtotime(Carbon::now())),
//                                    "p_ALLOT_LETTER_NO"   => $request->get("board_decision_number"),
//                                    "p_APPLICATION_ID"    => $applicationId,
//                                    "p_HOUSE_ADV_ID"      => $application->advertisement_id,
//                                    "p_DELIVERY_YN"       => 'Y',
//                                    "p_DELIVERY_DATE"     => date("Y-m-d", strtotime(Carbon::now())),
//                                    "p_DELIVERED_BY"      => '',
//                                    //"p_RECEIVED_BY"       => '',
//                                    "p_MEMO_NO"           => '',
//                                    "p_MEMO_DATE"         => date("Y-m-d", strtotime(Carbon::now())),
//                                    //"p_REMARKS"           => $request->get('remarks'),
//                                    "p_insert_by"         => Auth()->ID(),
//                                    "o_status_code"         => &$statusCode,
//                                    "o_status_message"      => &$statusMessage
//                                ];
//
//                                DB::executeProcedure('allotment.allot_letter_entry', $params_upd_letter);
//
//                            } catch (\Exception $e) {
//                                DB::rollBack();
//                                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
//                                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
//                            }
//							 DB::commit();
//                            return $params_upd_letter;
//                        }
//						DB::commit();
//                        return $params_letter;
//                    }
//                    DB::rollBack();
//                    return $params_letter;
//                }
            DB::commit();
            return $params;

        } else {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => 'Application not found!'];
            //return [  "exception" => true, "class" => 'error', "message" => 'Application not found!'];
        }
    }

    private function unAssignHouseToApplicant(Request $request, $applicationId)
    {
        $application = HaApplication::find($applicationId);
        $finalApprove = DB::selectOne("select max(WORKFLOW_PROCESS_ID) as final_approve_process_id from PMIS.WORKFLOW_PROCESS  where WORKFLOW_OBJECT_ID = 'haa$applicationId'");

        if ($application) {
            try {

                $statusCode = sprintf("%4000s", "");
                $statusMessage = sprintf('%4000s', '');
                $params = [
                    'p_APPLICATION_ID' => $applicationId,
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];

                DB::executeProcedure('delete_allot_approve', $params);

                //Remove final approve for this house

//                if ($params['o_status_code'] == 1) {
//
//                    $query = "delete from PMIS.WORKFLOW_PROCESS  where WORKFLOW_PROCESS_ID = '$finalApprove->final_approve_process_id'";
//
//                    DB::delete($query);
//                    DB::commit();
////                    DB::delete("delete from PMIS.WORKFLOW_PROCESS  where WORKFLOW_OBJECT_ID = '$finalApprove->final_approve_process_id'");
//                }
                   return $params;
            } catch (\Exception $e) {
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
            }
        } else {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => 'Application not found!'];
            //return [  "exception" => true, "class" => 'error', "message" => 'Application not found!'];
        }

    }

    private function denyApplicant(Request $request, $applicationId)
    {
        $application = HaApplication::find($applicationId);

        if ($application) {
            try {
                $statusCode = sprintf("%4000s", "");
                $statusMessage = sprintf('%4000s', '');
                $params = [
                    'p_APPLICATION_ID' => $applicationId,
                    'p_INSERT_BY' => auth()->id(),
                    'o_status_code' => &$statusCode,
                    'o_status_message' => &$statusMessage
                ];

                DB::executeProcedure('has.deny_allotment', $params);

                return $params;
            } catch (\Exception $e) {
                return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => $e->getMessage()];
                //return [  "exception" => true, "class" => 'error', "message" => $e->getMessage()];
            }
        } else {
            return ['exception' => true, 'o_status_code' => 99, 'o_status_message' => 'Application not found!'];
            //return [  "exception" => true, "class" => 'error', "message" => 'Application not found!'];
        }

    }

    public function edit(Request $request, $id)
    {
        $showBtn = 0;
        $haApplication = HaApplication::with(['allot_point', 'houseallotment'])->find($id);

        $employeeInformation = $this->employeeManager->findEmployeeInformation($haApplication->emp_code);

//        $availableHouses = $this->advertisementManager->findAvailableHouses($haApplication->advertisement->adv_id, $employeeInformation['house_type_id']);
//        $preferenceHouses = $this->advertisementManager->findPreferenceHouses($haApplication->application_id, $haApplication->advertisement->adv_id, $employeeInformation['house_type_id']);
        $preferenceHouses = $this->advertisementManager->findPreferenceHouses($haApplication->application_id, $haApplication->advertisement->adv_id, $haApplication['applied_house_type_id']);
        $availableWithoutPreference = $this->advertisementManager->findavailableWithoutPreferenceHouses($haApplication->advertisement->adv_id);

        $allottedHouse = null;

        if ($haApplication->houseallotment) {
            $allottedHouse = HouseList::find($haApplication->houseallotment->house_id);
        }

        $data = DB::select("select workflow_step_id from pmis.workflow_process where workflow_object_id = " . "'" . 'haa' . $haApplication->application_id . "'");

        $data = array_column($data, 'workflow_step_id');
        $data = array_unique($data);

//        if($haApplication->workflow_process!=null){
//            $compareData = DB::select("select workflow_step_id from pmis.workflow_steps where approval_workflow_id = ".$haApplication->workflow_process);
//            $compareData = array_column($compareData, 'workflow_step_id');
//            $compareData = array_unique($compareData);
//        }else{
//            $compareData = [];
//        }
//
//        if(count($data)==count($compareData) && count($compareData)!=0){
//            $showBtn = 1;
//        }
        $object_id = 'haa' . $haApplication->application_id;
        $is_approved = WorkFlowProcess::where('workflow_object_id', $object_id)->where('workflow_step_id', null)->first();

        $isHod = Auth::user()->roles->where('role_key', 'ha_hod')->first();
        if ($is_approved) {

            $finalAdmin = WorkFlowStep::where('approval_workflow_id', $haApplication->workflow_process)->orderBy('process_step', 'DESC')->first();
            $isFinalAdmin = Auth::user()->roles->where('role_key', $finalAdmin->user_role)->first();

            if ($isHod or $isFinalAdmin) {
                $showBtn = 1;
            }
        }

        $takeOver = DB::selectOne("select take_over_date from house_allottment where application_id = '$id'");

        $with = [
            'haApplication' => $haApplication,
            'preferenceHouses' => $preferenceHouses,
            'employeeInformation' => $employeeInformation,
            'allottedHouse' => $allottedHouse,
            'showBtn' => $showBtn,
            'availableWithoutPreference' => $availableWithoutPreference,
            'takeOver' => isset($takeOver) ? $takeOver->take_over_date : '',
        ];

        if ($haApplication) {
            return view('haapplicationapproval.form')->with($with)->render();
        }

        return [];
    }

    public function eligibleAttachmentDownload(Request $request, $id)
    {
        $eligibleAttachmentFile = HaApplication::find($id);

        if ($eligibleAttachmentFile) {
            if ($eligibleAttachmentFile->eligable_attachment && $eligibleAttachmentFile->eligable_attachment_name && $eligibleAttachmentFile->eligable_attachment_type) {
                $content = base64_decode($eligibleAttachmentFile->eligable_attachment);

                return response()->make($content, 200, [
                    'Content-Type' => $eligibleAttachmentFile->eligable_attachment_type,
                    'Content-Disposition' => 'attachment; filename="' . $eligibleAttachmentFile->eligable_attachment_name . '"'
                ]);
            } else {
                return redirect()->back()->with('error', 'No Attachment Found!');
            }
        }
    }
}
