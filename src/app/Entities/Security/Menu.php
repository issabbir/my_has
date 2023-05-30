<?php


namespace App\Entities\Security;

use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
    protected $table = 'CPA_SECURITY.sec_menu';
    protected $primaryKey = 'menu_id';
    //protected $with = [ 'sub_menus'];
   // protected $appends = ['role_submenus'];

    public function module() {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function sub_menus() {
        return $this->hasMany(Submenu::class, 'menu_id')->whereNull('parent_submenu_id')->orderBy('parent_submenu_id', 'asc')->orderBy('menu_order_no', 'asc');
    }
}

