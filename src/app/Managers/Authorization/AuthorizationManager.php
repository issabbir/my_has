<?php
namespace App\Managers\Authorization;

use App\Contracts\Authorization\AuthContact;

use App\Entities\Security\User;
use App\Enums\Auth\UserParams;
use Dotenv\Exception\ValidationException;
use Dotenv\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

use App\Entities\Security\Role;
use App\Entities\Security\SecRoleMenus;
use App\Entities\Security\SecUserRoles;
use App\Entities\Security\Submenu;
use Illuminate\Support\Arr;

/**
 * Class  as a services to maintain some business logic with db operation
 *
 * @package App\Managers\Authorization
 */
class AuthorizationManager implements AuthContact
{

    private $user;


    public function __construct()
    {
        $this->user = new User();
        $this->user->setConnection('cpa_security');
    }

    /**
     * Authorization Login process
     *
     * @return mixed
     */
    public function login($params)
    {
        try {
            $fullName = sprintf('%4000s', '');
            $o_user_id = sprintf('%4000s', '');
            $o_status_code = sprintf('%4000s', '');
            $o_need_pass_reset = sprintf('%4000s', '');
            $o_status_message = sprintf('%4000s', '');

            $mappedParams = UserParams::bindParams($params);
            $mappedParams['o_user_full_name'] = &$fullName;
            $mappedParams['o_user_id'] = &$o_user_id;
            $mappedParams['o_status_code'] = &$o_status_code;
            $mappedParams['o_need_pass_reset'] = &$o_need_pass_reset;
            $mappedParams['o_status_message'] = &$o_status_message;
            DB::executeProcedure('cpa_security.SECURITY.SEC_USERS_LOGIN', $mappedParams);
            if ($mappedParams['o_status_code'] == 1) {
                $this->user = $this->user->where('user_id', '=', $mappedParams['o_user_id'])->first();
                if ($this->user && $this->user->user_id) {
                    Auth::login($this->user);
                    if ($this->user->need_pass_reset == 'Y')
                        return Redirect::to("/user/change-password");

                    return Redirect::to("/dashboard");
                }
            }
            $validator = \Illuminate\Support\Facades\Validator::make([], []);
            $validator->getMessageBag()->add('error', $mappedParams['o_status_message']);
            return Redirect::back()->withErrors($validator)->withInput();
        }
        catch (\Exception $e) {
            $error = \Illuminate\Validation\ValidationException::withMessages([
                'error' => [$e->getMessage()]
            ]);
            throw $error;
        }
    }

    /**Authorization Login process
     *
     * @return mixed
     */
    public function logout()
    {
        // TODO: Implement logout() method.
    }

    /**
     * Recovering password
     *
     * @return mixed
     */
    public function recoverPassword()
    {
        // TODO: Implement recoverPassword() method.
    }

    /**
     * Make active an user
     *
     * @param $userId
     * @return mixed
     */
    public function makeActive($userId)
    {
        // TODO: Implement makeActive() method.
    }

    /**
     * Make deactivate an user
     *
     * @param $uerId
     * @return mixed
     */
    public function makeDeactivate($uerId)
    {
        // TODO: Implement makeDeactivate() method.
    }

    public function findSubMenuId($id)
    {

        $menuData =  Submenu::where('action_name', $id)->get('submenu_id');
        if(count($menuData) > 0){
            return $menuData[0]->submenu_id;
        }
        return false;
    }

    public function hasLinkAccess($user_id, $menu_id)
    {
        try{
            $roleMenus=[];
            $userRoleId = SecUserRoles::where('user_id', $user_id)->get('role_id');
            foreach ($userRoleId as $id=>$value){
                $menuString = SecRoleMenus::where('role_id', $value->role_id)->get('submenus')[0]->submenus;
                $menuArray = json_decode($menuString, TRUE);
                $roleMenus = array_unique(array_merge($menuArray, $menuArray));
            }
            if(count($roleMenus) > 0){
                return in_array($menu_id,$roleMenus );
            }else{
                return false;
            }
        }catch (\Exception $e){
            return false;
        }

    }


    public function findParentMenu($childMenuId)
    {
        $menuData =  Submenu::where('submenu_id', $childMenuId)->get();
        if(count($menuData) > 0){
            return  [
                'submenu_name'=>$menuData[0]->submenu_name,
                'submenu_id'=>$menuData[0]->parent_submenu_id,
                'parent_submenu_id'=>$menuData[0]->parent_submenu_id,
                'action_name'=>$menuData[0]->action_name,
                'route_name'=>$menuData[0]->route_name,
            ];
        }
        return false;
    }

    public function hasChildMenu($id)
    {
        $menuData =  Submenu::where('parent_submenu_id', $id)->get('submenu_id');
        if(count($menuData) > 0){
            return true;
        }
        return false;
    }

    // FIXME: FORGOT PASSWORD. NEEDED.
    public function findUserByEmail($email)
    {
        $query = <<<QUERY
SELECT * FROM CPA_SECURITY.SEC_USERS WHERE EMAIL = :email
QUERY;

        return DB::selectOne(DB::raw($query), ['email' => $email]);
    }

    public function generatePin($user): array
    {
        try {
            $o_user_id = sprintf('%4000s', '');
            $o_generated_pin = sprintf('%4000s', '');
            $o_status_code = sprintf('%4000s', '');
            $o_status_message = sprintf('%4000s', '');

            $params = [
                'p_email' => $user->email,
                'o_user_id' => &$o_user_id,
                'o_generated_pin' => &$o_generated_pin, // FIXME: FORGOT PASSWORD. CHANGE KEY AS REQUIRED.
                'o_status_code' => &$o_status_code,
                'o_status_message' => $o_status_message
            ];

            // DB::executeProcedure('cpa_security.SECURITY.generate_pin', $params); // FIXME: FORGOT PASSWORD. ENABLE PROCEDURE WHEN REAL PROCEDURE IS WRITTEN!
            return $params;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
    // FIXME: FORGOT PASSWORD. NEEDED END
}