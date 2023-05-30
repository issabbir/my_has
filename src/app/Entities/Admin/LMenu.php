<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LMenu extends Model
{
    protected $table = "pmis.l_menu";
    protected $primaryKey = "menu_id";

    protected $with = ['sub_menus'];

    public function sub_menus() {
        return $this->hasMany(LSubMenu::class, 'menu_id')->whereNull('parent_submenu_id')->orderBy('parent_submenu_id', 'asc')->orderBy('menu_order_no', 'asc');
    }
}
