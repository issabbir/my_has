<?php

namespace App\Entities\Security;
use Illuminate\Database\Eloquent\Model;


class Role extends Model
{
    protected $primaryKey = "role_id";
    protected $table = "cpa_security.sec_role";
    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->role_name;
    }

    public function getValueAttribute() {
        return $this->role_id;
    }

    /**
     * Lazy load permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions() {
        return $this->belongsToMany(Permission::class, 'cpa_security.sec_role_permissions','role_id', 'permission_id')->where('enabled_yn','Y');
    }

    /**
     * Lazy load menus based on role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menus() {
        return $this->belongsToMany(Menu::class, 'cpa_security.sec_role_menus', 'role_id', 'menu_id')->withPivot('role_id', 'menu_id', 'submenus');;
    }

    public function users() {
        return $this->belongsToMany(User::class, 'cpa_security.sec_user_roles', 'role_id', 'user_id')->withPivot('user_id');
    }

    /**
     * Lazy load permission
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reports() {
        return $this->belongsToMany(Report::class, 'cpa_security.sec_role_reports','role_id', 'report_id');
    }
}
