<?php
namespace App\Traits\Security;

/**
 * User grant access based on grant_all_yn
 *
 * Trait HasGrantAccess
 * @package App\Traits\Security
 */
trait HasGrantAccess
{

    public function hasGrantAll() {
        return $this->hasGrantAccess();
    }

    /**
     * Has grant access
     *
     * @return bool
     */
    private function hasGrantAccess() {
        $roles = $this->getRoles();
        $slugs = $roles->pluck('grant_all_yn','role_id');
        $arr = is_null($roles)
            ? []
            : $slugs->all();

        return in_array('Y', $arr);
    }
}