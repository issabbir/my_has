<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LModule extends Model
{
    protected $table = "pmis.l_module";
    protected $primaryKey = "module_id";

    protected $with = ['menus'];

    public function menus() {
        return $this->hasMany(LMenu::class, 'module_id');
    }
}
