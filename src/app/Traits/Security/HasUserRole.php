<?php


namespace App\Traits\Security;

use App\Entities\Security\Role;

trait HasUserRole
{

    /**
     * Users can have many roles.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'cpa_security.sec_user_roles', 'user_id', 'role_id');
    }

    /**
     * Get all roles.
     *
     * @return array
     */
    public function getRoles()
    {
        $this_roles = \Cache::remember(
            'acl.getRolesById_'.$this->user_id,
            now()->addSeconds(env('ACL_CACHE_SECONDS',60)),
            function () {
                return $this->roles;
            }
        );

        return $this_roles;
    }

    /**
     * Checking user role
     *
     * @return bool
     */
    public function hasUserRole() {
        $roles = $this->getRoles();
        $slugs = $roles->pluck('grant_all_yn','role_id');
        $arr = is_null($roles)
            ? []
            : $slugs->all();

        if (count($arr)>0)
            return true;

        return false;
    }
}
