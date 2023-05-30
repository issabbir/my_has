<?php


namespace App\Entities\Security;

use Illuminate\Database\Eloquent\Model;
class Module extends Model
{
    protected $table = 'CPA_SECURITY.sec_module';
    protected $primaryKey = 'module_id';
    protected $appends = ['text', 'value'];

   // protected $with = [ 'menus'];

    public function menus() {
        return $this->hasMany(Menu::class, 'module_id');
    }

    public function permissions() {
        return $this->hasMany(Permission::class, 'module_id');
    }

    public function reports() {
        return $this->hasMany(Report::class, 'module_id');
    }

    protected function getTextAttribute() {
        return $this->module_name;
    }

    protected function getValueAttribute() {
        return $this->module_id;
    }
}
