<?php


namespace App\Entities\Admin;

use Illuminate\Database\Eloquent\Model;

class LDptDivision extends Model
{
    protected $table = "pmis.l_dpt_division";
    protected $primaryKey = "dpt_division_id";

    protected $appends = ['text', 'value'];

    public function getTextAttribute() {
        return $this->dpt_division_name;
    }

    public function getValueAttribute() {
        return $this->dpt_division_id;
    }
}
