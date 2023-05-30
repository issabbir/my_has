<?php


namespace App\Entities\Security;


use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    protected $table = 'CPA_SECURITY.sec_submenu';
    protected $primaryKey = 'submenu_id';

    public function submenus() {
        return $this->hasMany(Submenu::class, 'parent_submenu_id')->orderBy("menu_order_no", 'asc');
    }
}
