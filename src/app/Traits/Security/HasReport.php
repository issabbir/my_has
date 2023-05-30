<?php
namespace App\Traits\Security;

/**
 * User grant access based on grant_all_yn
 *
 * Trait HasGrantAccess
 * @package App\Traits\Security
 */
trait HasReport
{
    /**
     * Get all permission based on user role assigned to user
     *
     * @return array
     */
    public function getReports() {
        $_roles = $this->getRoles();
        return \Cache::remember(
            'acl.getReportsById_'.$this->user_id,
            now()->addSeconds(env('ACL_CACHE_SECONDS',60)), function() use ($_roles) {
            $_reports = [];
            foreach ($_roles as $role) {
                $reports = $role->reports;
                $slugs = $reports->pluck('report_id','report_id');
                $arr = is_null($reports)
                    ? []
                    : $slugs->all();
                $_reports = array_unique(array_merge($_reports,$arr));
            }
            return $_reports;
        });
    }

    /**
     * Has report permission to check for the user
     *
     * @param $key
     * @return bool
     */
    public function hasReportPermission($rid) {
        if ($hasGrantAll = $this->hasGrantAccess())
            return $hasGrantAll;

        $_data = $this->getReports();
        return in_array($rid, $_data);
    }
}