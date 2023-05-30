<?php
namespace App\Traits\Security;

/**
 * User grant access based on grant_all_yn
 *
 * Trait HasGrantAccess
 * @package App\Traits\Security
 */
trait HasPermission
{

    /**
     * Get all permission based on user role assigned to user
     *
     * @return array
     */
    public function getPermissions() {
        $_roles = $this->getRoles();
        return \Cache::remember(
            'acl.getPermissionById_'.$this->user_id,
            now()->addSeconds(env('ACL_CACHE_SECONDS',60)), function() use ($_roles) {
            $permissions = [];
            foreach ($_roles as $role) {
                $rolePermission = $role->permissions;
                $slugs = $rolePermission->pluck('permission_key','permission_id');
                $arr = is_null($rolePermission)
                    ? []
                    : $slugs->all();
                $permissions = array_unique(array_merge($permissions,$arr));
            }
            return $permissions;
        });
    }

    /**
     * Has permission check for the user
     *
     * @param $key
     * @return bool
     */
    public function hasPermission($key) {
        if ($hasGrantAll = $this->hasGrantAccess())
            return $hasGrantAll;

        $_permissions = $this->getPermissions();
        return in_array($key, $_permissions);
    }
}