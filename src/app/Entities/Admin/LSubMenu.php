<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LSubMenu extends Model
{
    protected $table = "pmis.l_submenu";
    protected $primaryKey = "submenu_id";
    protected $with = ['submenus'];

    public function submenus() {
        return $this->hasMany(LSubMenu::class, 'parent_submenu_id')->orderBy("menu_order_no", 'asc');
    }


}
