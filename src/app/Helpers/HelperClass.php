<?php
//app/Helpers/HelperClass.php
namespace App\Helpers;

use App\Entities\Admin\LGeoDistrict;
use App\Entities\Pmis\WorkFlowProcess;
use App\Entities\Pmis\WorkFlowStep;
use App\Entities\Security\Menu;
use App\Enums\ModuleInfo;
use App\Managers\Authorization\AuthorizationManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class HelperClass
{

    public $id;
    public $links;

    /**
     * @return mixed
     */
    public static function menuSetup()
    {
        if (Auth::user()->hasGrantAll()) {
            $moduleId = ModuleInfo::MODULE_ID;
            $menus = Menu::where('module_id', $moduleId)->orderBy('menu_order_no')->get();

            return $menus;
        } else {
            $allMenus = Auth::user()->getRoleMenus();
            $menus = [];

            if($allMenus) {
                foreach($allMenus as $menu) {
                    if($menu->module_id == ModuleInfo::MODULE_ID) {
                        $menus[] = $menu;
                    }
                }
            }

            return $menus;
        };
    }

    public function implodeValue($types)
    {
        $strTypes = implode(",", $types);
        return $strTypes;
    }

    public function explodeValue($types)
    {
        $strTypes = explode(",", $types);
        return $strTypes;
    }

    public function random_code()
    {
        return rand(1111, 9999);
    }

    public function remove_special_char($text)
    {
        $t = $text;
        $specChars = array(
            ' ' => '-', '!' => '', '"' => '',
            '#' => '', '$' => '', '%' => '',
            '&amp;' => '', '\'' => '', '(' => '',
            ')' => '', '*' => '', '+' => '',
            ',' => '', '₹' => '', '.' => '',
            '/-' => '', ':' => '', ';' => '',
            '<' => '', '=' => '', '>' => '',
            '?' => '', '@' => '', '[' => '',
            '\\' => '', ']' => '', '^' => '',
            '_' => '', '`' => '', '{' => '',
            '|' => '', '}' => '', '~' => '',
            '-----' => '-', '----' => '-', '---' => '-',
            '/' => '', '--' => '-', '/_' => '-',
        );

        foreach ($specChars as $k => $v) {
            $t = str_replace($k, $v, $t);
        }

        return $t;
    }

    public function datatable($datas, $table_id = null, $link = [])
    {
        $this->id = $table_id;
        $this->links = $link;
        return Datatables::of($datas)
            ->addColumn('action', function ($data) {
                if ($this->id) {
                    $icon = ['bx-edit', 'bx-trash', 'bx-show'];   // ['edit','delete','view']
                    $str = '';
                    foreach ($this->links as $key => $link) {
                        if (isset($link) && $link != '')
                            $str .= '<a href="' . $link . '/' . $data[$this->id] . '"><i class="bx ' . $icon[$key] . ' cursor-pointer"></i></a>&nbsp;';
                    }
                    return $str;
                } else {
                    return '';
                }
            })
            ->make(true);
    }


    public static function breadCrumbs($routeName)
    {
        if (in_array($routeName, ['colony.edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => 'Colony', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['building.edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => 'Building', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['house.edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => 'House', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['advertisement.edit'])) {
            return [
                ['submenu_name' => 'Setup', 'action_name' => ''],
                ['submenu_name' => 'Advertisement', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['ha-application.edit'])) {
            return [
                ['submenu_name' => 'Applications', 'action_name' => ''],
                ['submenu_name' => 'Allotment', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['house-interchange-application.edit'])) {
            return [
                ['submenu_name' => 'Applications', 'action_name' => ''],
                ['submenu_name' => 'Interchange', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['house-replacement-application.edit'])) {
            return [
                ['submenu_name' => 'Applications', 'action_name' => ''],
                ['submenu_name' => 'Replacement', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['house-interchange-approval.edit'])) {
            return [
                ['submenu_name' => 'Approvals', 'action_name' => ''],
                ['submenu_name' => 'Interchange', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['house-replacement-approval.edit'])) {
            return [
                ['submenu_name' => 'Approvals', 'action_name' => ''],
                ['submenu_name' => 'Replacement', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['allotmentLetter.edit'])) {
            return [
                ['submenu_name' => 'Allotment Letter', 'action_name' => ''],
                ['submenu_name' => 'General', 'action_name' => '']
            ];
        } else if (in_array($routeName, ['allotmentLetterInterchange.edit'])) {
            return [
                ['submenu_name' => 'Allotment Letter', 'action_name' => ''],
                ['submenu_name' => 'Interchange', 'action_name' => '']
            ];
        }  else if (in_array($routeName, ['replaceAllotmentLetter.edit'])) {
            return [
                ['submenu_name' => 'Allotment Letter', 'action_name' => ''],
                ['submenu_name' => 'Replacement', 'action_name' => '']
            ];
        }  else if (in_array($routeName, ['allocate-flat.edit'])) {
            return [
                ['submenu_name' => 'House Alloted', 'action_name' => ''],
            ];
        } else {
            $breadMenus = [];

            try {
                $authorizationManager = new AuthorizationManager();
                $getRouteMenuId = $authorizationManager->findSubMenuId($routeName);
                if ($getRouteMenuId && !empty($getRouteMenuId)) {
                    $breadMenus[] = $bm = $authorizationManager->findParentMenu($getRouteMenuId);
                    if ($bm && isset($bm['parent_submenu_id']) && !empty($bm['parent_submenu_id'])) {
                        $breadMenus[] = $authorizationManager->findParentMenu($bm['parent_submenu_id']);
                    }
                }
            } catch (\Exception $e) {
                return false;
            }

            return is_array($breadMenus) ? array_reverse($breadMenus) : false;
        }
    }

    public static function getActiveRouteNameWrapping($routeName)
    {
        if (in_array($routeName, ['colony.edit'])) {
            return 'colony.colony_register';
        }  else if (in_array($routeName, ['building.edit'])) {
            return 'building.index';
        } else if (in_array($routeName, ['house.edit'])) {
            return 'house.index';
        } else if (in_array($routeName, ['advertisement.edit'])) {
            return 'advertisement.index';
        } else if (in_array($routeName, ['ha-application.edit'])) {
            return 'ha-application.index';
        }  else if (in_array($routeName, ['house-interchange-application.edit'])) {
            return 'house-interchange-application.index';
        } else if (in_array($routeName, ['house-replacement-application.edit'])) {
            return 'house-replacement-application.index';
        }  else if (in_array($routeName, ['house-interchange-approval.edit'])) {
            return 'house-interchange-approval.index';
        } else if (in_array($routeName, ['house-replacement-approval.edit'])) {
            return 'house-replacement-approval.index';
        } else if (in_array($routeName, ['allotmentLetter.edit'])) {
            return 'allotmentLetter.index';
        } else if (in_array($routeName, ['allotmentLetterInterchange.edit'])) {
            return 'allotmentLetterInterchange.index';
        }  else if (in_array($routeName, ['replaceAllotmentLetter.edit'])) {
            return 'replaceAllotmentLetter.index';
        }  else if (in_array($routeName, ['allocate-flat.edit'])) {
            return 'allocate-flat.index';
        } else {
            return [
                [
                    'submenu_name' => $routeName,
                ]
            ];
        }
    }

    public static function activeMenus($routeName)
    {
        //$menus = [];
        try {
            $authorizationManager = new AuthorizationManager();
            $menus[] = $getRouteMenuId = $authorizationManager->findSubMenuId(self::getActiveRouteNameWrapping($routeName));

            if ($getRouteMenuId && !empty($getRouteMenuId)) {
                $bm = $authorizationManager->findParentMenu($getRouteMenuId);
                $menus[] = $bm['parent_submenu_id'];
                if ($bm && isset($bm['parent_submenu_id']) && !empty($bm['parent_submenu_id'])) {
                    $m = $authorizationManager->findParentMenu($bm['parent_submenu_id']);
                    if (!empty($m['submenu_id'])) {
                        $menus[] = $m['submenu_id'];
                    }
                }
            }
        } catch (\Exception $e) {
            $menus = [];
        }
        return is_array($menus) ? $menus : false;
    }

    public static function hasChildMenu($routeName)
    {
        $authorizationManager = new AuthorizationManager();
        $getRouteMenuId = $authorizationManager->findSubMenuId($routeName);
        return $authorizationManager->hasChildMenu($getRouteMenuId);
    }


    public static function fileUpload($request)
    {
        // $upload_handler = new UploadHandler();
        $filename = [];
        foreach ($request->file("attachment") as $file) {
            $extension = $file->getClientOriginalExtension();
            Storage::disk('public')->put($file->getFilename() . '.' . $extension, File::get($file));
            $filename[] = $file->getFilename() . '.' . $extension;
        }
        return $filename;
    }

    public static function getStatusName($statusTag = '')
    {
        switch (strtoupper($statusTag)) {
            case 'A':
                $name = 'Approved';
                break;
            case 'I':
                $name = 'Inactive';
                break;
            case 'P':
                $name = 'Pending';
                break;
            case 'D':
                $name = 'Delete';
                break;
            case 'C':
                $name = 'Complete';
                break;
            case 'R':
                $name = 'Cancel';
                break;
            case 'Y':
                $name = 'Yes';
                break;
            case 'N':
                $name = 'No';
                break;
            default:
                $name = 'Unknown';
                break;
        }
        return $name;
    }

    public static function getColorCode($priorityID = '')
    {

        $code = '';
        if (!empty($priorityID)) {
            $colorCode = LPriorityType::where('priority_id', $priorityID)->get('color_code');
            $code = $colorCode[0]->color_code;
        }
        return $code;
    }

    public static function customDateFormat($datetime)
    {
        return date("d-m-Y", strtotime($datetime));
    }

    public static function customTimeFormat($datetime)
    {
        return date("h:i A", strtotime($datetime));
    }

    public static function customDateTimeFormat($datetime)
    {
        return date("d-m-Y h:i A", strtotime($datetime));
    }

    public static function customDateTimeDiff($startDateTime, $endDateTime)
    {
        try {
            if (strtotime($startDateTime) >= strtotime($endDateTime)) {
                $res = false;
            } else {
                $res = true;
            }
        } catch (\Exception $e) {
            $res = false;
        }

        return $res;

    }

    public static function customDateDiff($startDateTime, $endDateTime)
    {
        try {
            if (strtotime($startDateTime) > strtotime($endDateTime)) {
                $res = false;
            } else {
                $res = true;
            }
        } catch (\Exception $e) {
            $res = false;
        }

        return $res;

    }

    public static function operatorUser()
    {
        try {
            $res = [];
            // operator_user_id secretary
            // operator_for_user_id  chairman
            $users = OperatorMapping::where('operator_user_id', Auth::id())->where('status', 'A')->get();
            foreach ($users as $user) {
                if ($user->operator_for_user_id) {
                    $res[] = $user->operator_for_user_id;
                }
            }
        } catch (\Exception $e) {
            $res = [];
        }
        return $res;
    }

    public static function findDistrictByDivision($divisionId)
    {
        return LGeoDistrict::where('geo_division_id', $divisionId)->get();
    }

    public static function getRole($roleName)
    {
        $roles = [];
        foreach (Auth::user()->getRoles() as $role) {
            $roles [] = $role->role_name;
        }
        if (in_array($roleName, $roles)) {
            return true;
        }
        return false;
    }

    public static function custom_array_search($searchElement , $searchArray = array(),$needToReturn = 'bool',$checkWith = 'value'){

        foreach ($searchArray as $key => $value){
            $data = ($checkWith == 'value')? $value : $key;
            if($searchElement == "$data"){
                if($needToReturn == 'key'){
                    return $key;
                }elseif($needToReturn == 'value'){
                    return $value;
                }else{
                    return true;
                }
            }
        }

    }

    public static function workflowStatus($workflowId, $object_id)
    {
        $progressBarData = WorkFlowStep::where('approval_workflow_id', $workflowId)->orderby('process_step')->get();
        $current_step = [];
        $previous_step = [];
        $workflowProcess = WorkFlowProcess::with('workflowStep')
            ->where('workflow_object_id', $object_id)
            ->orderBy('workflow_process_id', 'DESC')
            ->whereHas('workflowStep', function ($query) use ($workflowId) {
                $query->where('approval_workflow_id', $workflowId);
            })->get();

        $option = [];
        if (!count($workflowProcess)) {
            $next_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->orderBy('process_step', 'asc')->first();
        } else {
            if ($workflowProcess) {
                $current_step = $workflowProcess[0]->workflowStep;
                $sql = 'select e.emp_code, e.emp_name, d.designation
                       from cpa_security.sec_users u
                         inner join pmis.employee e on (e.emp_id = u.emp_id)
                         left join pmis.L_DESIGNATION d  on (d.designation_id = e.designation_id)
                         where user_id=:userId';
                $user = db::selectOne($sql, ['userId' => $workflowProcess[0]->insert_by]);
                $current_step->user = $user;
                $current_step->note = $workflowProcess[0]->note;
            }

            $next_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->where('process_step', '>', $current_step->process_step)->orderBy('process_step', 'asc')->first();
            $previous_step = WorkFlowStep::where('approval_workflow_id', $workflowId)->where('process_step', '<', $current_step->process_step)->orderBy('process_step', 'asc')->get();
        }

        if (!empty($previous_step)) {
            foreach ($previous_step as $previous) {
                $option[] = [
                    'text' => $previous->backward_title,
                    'value' => $previous->workflow_step_id,
                ];
            }
        }

        if (!empty($current_step)) {
            $option[] = [
                'text' => $current_step->forward_title,
                'value' => $current_step->workflow_step_id,
                'disabled' => true
            ];
        }

        if (!empty($next_step)) {
            $option[] = [
                'text' => $next_step->forward_title,
                'value' => $next_step->workflow_step_id,
                'selected' => true,
            ];
        }

        $process = [];
        foreach ($workflowProcess as $wp) {
            $sql = 'select e.emp_code, e.emp_name, d.designation
                       from cpa_security.sec_users u
                         inner join pmis.employee e on (e.emp_id = u.emp_id)
                         left join pmis.L_DESIGNATION d  on (d.designation_id = e.designation_id)
                         where user_id=:userId';
            $user = db::selectOne($sql, ['userId' => $wp->insert_by]);
            $wp->user = $user;
            $process[] = $wp;
        }

        $msg = '';
        $ids = array_column($option, 'value');
        $value = $ids ? max($ids) : 0;
        $prev_val = $value;
        foreach ($option as $data) {
            $disabeld = (isset($data['disabled']) && $data['disabled']) ? 'disabled' : '';
            $selected = (isset($data['selected']) && $data['selected']) ? 'selected' : '';
            $msg .= '<option value="' . $data['value'] . '" ' . $disabeld . ' ' . $selected . '>'
                . $data['text'] . '</option>';
        }

        $is_approved = WorkFlowProcess::where('workflow_object_id', $object_id)->where('workflow_step_id', null)->first();

        return
            [
                'workflowProcess' => $process,
                'progressBarData' => $progressBarData,
                'next_step' => $next_step,
                'previous_step' => $previous_step,
                'current_step' => $current_step,
                'options' => $msg,
                'is_approved' => $is_approved,
            ];
    }

    public static function hasPermission($workflowId, $object_id)
    {
        $workflow = HelperClass::workflowStatus($workflowId,$object_id);
        $user_roles = Auth::user()->roles->pluck('role_key')->toArray();
        $role = isset($workflow['current_step']->user_role) && $workflow['current_step']->user_role ? $workflow['current_step']->user_role : '';
        $has_permission = in_array($role, $user_roles);

        $permission = false;
        if(!$workflow['next_step'] && $has_permission && !$workflow['is_approved']) {
            $permission = true;
        } else if ($workflow['next_step'] && $has_permission) {
            $permission = true;
        }

        return $permission;
    }

}
